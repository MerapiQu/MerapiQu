<?php

namespace App\System\Views;

use App\Miscellaneous\Utility;
use Nette\FileNotFoundException;
use Symfony\Component\Filesystem\Path;

class ViewLoader implements \Twig\Loader\LoaderInterface
{
    private $extensions = ["twig", "html", "json"];
    private static $dirs = [
        __DIR__ . "/../../WebService/Default/views",
        __DIR__ . "/../../WebService/Error/views",
    ];

    static function addPath(string $path)
    {
        self::$dirs[] = $path;
        array_unique(self::$dirs);
    }

    function patifyName(string $name): string
    {
        return str_replace(".", "/", $name);
    }

    function getFileFromModule($moduleName, $name): string | false
    {

        $path = Path::join(Utility::getcwd("media/Modules/$moduleName/views/"));
        if (!file_exists($path)) return false;

        foreach ($this->extensions as $ext) {
            if (file_exists(Path::join($path, "$name.$ext"))) {
                return Path::join($path, "$name.$ext");
            }
        }
        return false;
    }

    function getFile(string $name): string | false
    {
        $name = $this->patifyName($name);
        if (preg_match("/^@(\w+)\/(.*)/", $name, $matches)) {
            $module = $matches[1];
            $file = $matches[2];
            return $this->getFileFromModule($module, $file);
        }

        foreach (self::$dirs as $dir) {
            foreach ($this->extensions as $ext) {
                if (file_exists(Path::join($dir, "$name.$ext"))) {
                    return Path::join($dir, "$name.$ext");
                }
            }
        }
        return false;
    }

    function exists(string $name): bool
    {
        $file = $this->getFile($name);
        return $file !== false;
    }

    function getCacheKey(string $name): string
    {
        return $name;
    }

    function getSourceContext(string $name): \Twig\Source
    {
        $file = $this->getFile($name);
        if (!file_exists($file)) {
            throw new FileNotFoundException("File \"" . $name . "\" not found!");
        }
        return new \Twig\Source(file_get_contents($file), $name, $file);
    }

    function isFresh(string $name, int $time): bool
    {
        return true;
    }
}
