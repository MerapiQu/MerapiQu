<?php

namespace App\ContentManagement;

class AssetManager
{

    /**
     * Summary of stack
     * @var array<string, string> $stack
     */
    private array $sources = [];
    private AssetLoader $loader;

    public function __construct(array $sources)
    {
        $this->loader = new AssetLoader($sources);
        $this->sources = $sources;
    }

    function getPublicPath($path)
    {

        return $this->loader->getPath($path);
    }
}
