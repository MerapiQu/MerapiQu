<?php

namespace App\ContentManagement;

use App\Miscellaneous\Utility;
use Symfony\Component\Filesystem\Path;

class AssetLoader
{

    protected string $moduleRoot;
    private array $sources = [];
    private array $fileMap;

    function __construct(array $sources)
    {
        $this->moduleRoot = Utility::getcwd("media/modules/");
        $this->fileMap = [];
        $this->loadFileMap();

        $this->sources = $sources;
    }


    function getPath($path)
    {

        if (file_exists(Utility::getcwd("/public/" . $path))) {
            return $path;
        }

        $realPath = $this->getRealPath($path);
        if ($realPath) {
            $signature  = $this->getSignature($realPath, $path);
            $publicPath = $this->getPublicPath($signature, $path);
            if (!file_exists($publicPath)) {
                $this->makeClone($realPath, $publicPath);
            } else if (filemtime($realPath) > filemtime($publicPath)) {
                $this->makeClone($realPath, $publicPath);
            }
            $relativePath = $this->makeRelative($publicPath);
            $relativePath .= "?v=" . filectime($realPath);
            return $relativePath;
        }
        return null;
    }

    function makeRelative($file)
    {
        return str_replace(Utility::getcwd("public"), "", $file);
    }


    private function makeClone($realPath, $publicPath)
    {
        if (!file_exists(dirname($publicPath))) {
            mkdir(dirname($publicPath), 0755, true);
        }
        file_put_contents(
            $publicPath,
            file_get_contents($realPath) ?? ""
        );
    }


    private function getPublicPath($signature, $path)
    {
        $path = preg_replace("/^@\w+/", "", $path);
        $path = ltrim($path, "/");

        if (preg_match("/^asset(\w+)\//im", $path)) {
            $path = preg_replace("/^asset(\w+)\//", "", $path);
        }
        $publicPath = Utility::getcwd("/public/assets/" . $signature . "/" . $path);
        return $publicPath;
    }

    private function getRealPath($path)
    {

        preg_match("/^@\w+/", $path, $matches);
        if (isset($matches[0])) {
            $path = preg_replace("/^@/", "", $path);
            $path = Path::join($this->moduleRoot, $path);
            if (file_exists($path)) {
                return $path;
            }
            return null;
        }

        $realPath = null;

        foreach ($this->sources as $source) {
            $_path = Path::join($source, $path);
            if (file_exists($_path)) {
                $realPath = $_path;
            }
        }
        return $realPath;
    }

    private function getSignature($realPath, $path)
    {
        $cwd = Utility::getcwd();
        $middle = str_replace($cwd, "", str_replace($path, "", $realPath));
        if (!isset($this->fileMap[$middle])) {
            $signature = Utility::random(10, "a-zA-Z0-9");
            $this->fileMap[$middle] = $signature;
            $this->saveFileMap();
        } else {
            $signature = $this->fileMap[$middle];
        }
        return $signature;
    }

    private function loadFileMap()
    {
        $mapFile = Utility::getcwd("/app/.data/assets.map");
        if (!file_exists($mapFile)) {
            $this->fileMap = [];
        } else {
            try {
                $this->fileMap = json_decode(file_get_contents($mapFile), true);
            } catch (\Exception $e) {
                $this->fileMap = [];
            }
        }
    }

    private function saveFileMap()
    {
        file_put_contents(
            Utility::getcwd("/app/.data/assets.map"),
            json_encode($this->fileMap)
        );
    }
}
