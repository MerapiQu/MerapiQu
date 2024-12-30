<?php

namespace App\System\Theme;

use App\ContentManagement\AssetManager;
use Symfony\Component\Filesystem\Path;

class Theme
{

    private AssetManager $assets;
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        // $this->assets = new AssetManager($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }


    public function load()
    {
        $this->loadAssets();
    }

    private function loadAssets()
    {
        $path = Path::join($this->path, "assets");
        
    }
}
