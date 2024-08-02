<?php

namespace MerapiPanel\Views\Extension;

use MerapiPanel\Utility\AES;
use MerapiPanel\Views\Abstract\Extension;

class Bundle extends Extension
{

    public function fl_url(mixed $path, array $pattern = [])
    {

        $parse = parse_url($path ?? "");
        if (!isset($parse['scheme'])) {
            $parse['scheme'] = $_SERVER['REQUEST_SCHEME'];
        }
        if (!isset($parse['host'])) {
            $parse['host'] = $_SERVER['HTTP_HOST'];
        }

        $result_path = $parse['path'];
        preg_match_all('/\:([a-z0-9]+)/i', $result_path, $matches);


        if (isset($matches[1])) {
            foreach ($matches[1] as $value) {
                $result_path = preg_replace('/\:' . $value . '/', (isset($pattern[$value]) && !empty($pattern[$value]) ? $pattern[$value] : ""), $result_path);
            }
        }
        return $parse['scheme'] . "://" . $parse['host'] . "/" . ltrim($result_path, "/");
    }


    public function fn_time()
    {
        return time();
    }


    public function fn_microtime($as_float = true)
    {
        return microtime($as_float);
    }



    function fl_access_path(string $path)
    {

        if (!isset($_ENV["__MP_ACCESS__"], $_ENV["__MP_" . strtoupper($_ENV["__MP_ACCESS__"]) . "__"])) {
            return $path;
        }
        $access = $_ENV["__MP_" . strtoupper($_ENV["__MP_ACCESS__"]) . "__"];
        $prefix = $access["prefix"];

        return rtrim($prefix, "/") . "/" . ltrim($path, "/");
    }



    /**
     * Get block content from the context
     * @option needs_context true
     */
    public function fn_block_content(array $context, string $blockName)
    {
        // Get block content from the context
        $blockContent = $context['_view']->renderBlock($blockName);

        return $blockContent;
    }


    function fn_error_log($message)
    {

        error_log(is_string($message) ? $message : print_r($message, 1));
    }


    function fn_is_string($value)
    {
        return is_string($value);
    }

    function fn_is_numberic($value)
    {
        return is_numeric($value);
    }

    function fn_is_array($value)
    {
        return is_array($value);
    }
}
