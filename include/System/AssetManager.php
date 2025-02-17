<?php

namespace MerapiPanel\System;

class AssetManager
{
    private AssetLoader $loader;

    public function __construct(array $sources)
    {
        $this->loader = new AssetLoader($sources);
    }

    function getPublicPath($path)
    {
        return $this->loader->getPath($path);
    }
}
