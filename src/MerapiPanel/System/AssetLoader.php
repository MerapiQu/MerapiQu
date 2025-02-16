<?php

namespace MerapiPanel\System;

use Symfony\Component\Filesystem\Path;

class AssetLoader
{
    protected string $basePath;
    protected string $moduleRoot;
    private array $sources = [];
    private array $fileMap;

    function __construct(array $sources)
    {
        $this->basePath = $_ENV['APP_CWD'];
        $this->fileMap = [];
        $this->loadFileMap();
        $this->sources = $sources;
    }


    function getPath($path)
    {

        if (file_exists(Path::join($this->basePath, "public", $path))) return $path;

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
        return str_replace(Path::join($this->basePath, "public"), "", $file);
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

        if (preg_match("/^assets(\w+)\//im", $path)) {
            $path = preg_replace("/^assets(\w+)\//", "", $path);
        }
        $publicPath = Path::join($_ENV["APP_CWD"], "public", "assets", $signature, $path);
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
        $cwd = $_ENV['APP_CWD'];
        $middle = str_replace($cwd, "", str_replace($path, "", $realPath));
        if (!isset($this->fileMap[$middle])) {
            $signature = base64_encode(random_bytes(8));
            $this->fileMap[$middle] = $signature;
            $this->saveFileMap();
        } else {
            $signature = $this->fileMap[$middle];
        }
        return $signature;
    }

    private function loadFileMap()
    {
        $mapFile = Path::join($_ENV['APP_CWD'], ".cache", "assetsMap");

        if (!file_exists($mapFile)) {
            $this->fileMap = [];
        } else {
            try {
                $this->fileMap = json_decode(file_get_contents($mapFile), true) ?? [];
            } catch (\Exception $e) {
                unlink(Path::join($_ENV['APP_CWD'], ".cache", "assetsMap"));
                $this->fileMap = [];
            }
        }
    }

    private function saveFileMap()
    {
        $targetPath = Path::join($_ENV['APP_CWD'], ".cache");
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        file_put_contents(
            Path::join($targetPath, "assetsMap"),
            json_encode($this->fileMap)
        );
    }
}
