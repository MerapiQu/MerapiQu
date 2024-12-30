<?php

namespace App\ModuleSystem\ModuleModel;

use App\Miscellaneous\Utility;
use App\ModuleSystem\ModuleModel\Fragments\Fragment;
use Symfony\Component\Filesystem\Path;

/**
 * Module
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 * @extends     parent<Fragment>
 */
final class Module extends Fragment
{

    /**
     * get module manifest
     * @throws \Exception
     * @return \App\ModuleSystem\ModuleModel\Manifest
     */
    function getManifest(): Manifest
    {
        $path = Path::join($this->getPath(), "manifest.json");
        if (!file_exists($path)) {
            throw new \Exception("Module does't provide manifest");
        }
        return new Manifest($path);
    }
    function onCreate(): void
    {
        // error_log("Module created");
    }

    static function isModuleEntity(string|object $class): bool
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $class = "\\" . ltrim($class, "\\");
        return preg_match("/\\\\Media\\\\Modules\\\\\w+/", $class) === 1;
    }

    static function getModuleName(string|object $path): ?string
    {
        if (is_object($path)) {
            $path = get_class($path);
        }

        $path = "\\" . ltrim($path, "\\");
        if (file_exists($path)) {
            $path = Utility::normalizePath($path);
            preg_match("/\\\\media\\\\Modules\\\\(\\w+)/i", $path, $match);
        } elseif (class_exists($path)) {
            preg_match("/\\\\Media\\\\Modules\\\\(\\w+)/", $path, $match);
        } else {
            return null;
        }

        return $match[1] ?? null;
    }
}
