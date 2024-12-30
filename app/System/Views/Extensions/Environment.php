<?php

namespace App\System\Views\Extensions;

use App\ContentManagement\AssetManager;
use App\System\Views\ExtensionAdapter;
use App\System\Views\ViewDocument;
use Twig\Template;
use Twig\TwigFunction;

class Environment extends ExtensionAdapter
{
    private ViewDocument $document;
    private AssetManager $assetManager;

    public function __construct(ViewDocument $document)
    {
        $this->document = $document;
        $this->assetManager = $document->getAssetManager();
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("style", [$this, "style"]),
            new TwigFunction("script", [$this, "script"]) // Add script function here
        ];
    }

    public function style($content, array $attr = [])
    {
        /**
         * @var Template $template
         */
        $template;
        foreach (debug_backtrace() as $trace) {
            if (isset($trace['object']) && $trace['object'] instanceof Template && 'Twig\Template' !== get_class($trace['object'])) {
                $template = $trace['object'];
            }
        }

        if (isset($template)) {
            $name =  $template->getTemplateName();
            if (preg_match("/^@\w+/", $name, $matches) && strpos($content, "@") !== 0) {
                $content = $matches[0] . $content;
            }
        }
        if (!$this->isLink($content)) {
            $content = $this->assetManager->getPublicPath($content);
        }
        $head = $this->document->head();
        $head->append([
            "tagName" => "link",
            "attribute" => [
                "type" => "text/css",
                ...$attr,
                "rel" => "stylesheet",
                "href" => $content
            ]
        ]);
    }

    public function script(string $content, array $attr = [])
    {
        /**
         * @var Template $template
         */
        $template;
        foreach (debug_backtrace() as $trace) {
            if (isset($trace['object']) && $trace['object'] instanceof Template && 'Twig\Template' !== get_class($trace['object'])) {
                $template = $trace['object'];
            }
        }

        if (isset($template)) {
            $name =  $template->getTemplateName();
            if (preg_match("/^@\w+/", $name, $matches) && strpos($content, "@") !== 0) {
                $content = $matches[0] . $content;
            }
        }

        if (!$this->isLink($content)) {
            $content = $this->assetManager->getPublicPath($content);
        }
        $body = $this->document->body();
        $body->append([
            "tagName" => "script",
            "attribute" => [
                "type" => "text/javascript",
                ...$attr,
                "src" => $content
            ]
        ]);
    }

    private function isLink(string $text): bool
    {
        $unixPattern = '/^(http|\/\/)/';
        $windowsPattern = '#^[a-zA-Z]:\\(?:[\\w\\d._-]+\\)*[\\w\\d._-]+$#';
        return preg_match($unixPattern, $text) || preg_match($windowsPattern, $text) || preg_match('#^https?://#', $text);
    }


    private function minifyStyle(string $content)
    {

        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/\s*([{};,:>])\s*/', '$1', $content);
        $content = preg_replace('/;(?=\s*})/', '', $content);
        $content = trim($content);

        return $content;
    }

    private function minifyScript(string $content)
    {

        // Remove comments (both single-line and multi-line)
        $content = preg_replace('/\/\*.*?\*\/|\/\/.*(?=[\n\r])/s', '', $content);

        $content = preg_replace('/(=\s.*[^;](?<!;))/m', '$1;', $content);

        // Remove unnecessary whitespace
        $content = preg_replace('/\s+/', ' ', $content);

        // Insert semicolons at the end of statements if missing
        // Add semicolon after a closing bracket if followed by a statement start (e.g., a variable definition)
        $content = preg_replace('/\}\s*(?=[\w\$\_])/', '};', $content);
        $content = preg_replace('/(\=\s.*)/', '$1;', $content);

        // Add semicolon after statements that should be terminated
        $content = preg_replace('/([^\;\}\{])\s*(?=[\}\n])/', '$1;', $content);

        // Remove space before and after certain characters
        $content = preg_replace('/\s*([{};,:()])\s*/', '$1', $content);

        // remove uneed semicolon
        $content = preg_replace('/\;\}/', '}', $content);
        $content = preg_replace('/\;\;/', ';', $content);

        // Trim the final content
        $content = trim($content);

        return $content;
    }
}
