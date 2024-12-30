<?php

namespace App\System\Views\Extensions;

use App\ContentManagement\AssetManager;
use App\System\Views\ExtensionAdapter;
use App\System\Views\ViewDocument;
use Twig\TwigFilter;

class Assets extends ExtensionAdapter
{

    private $assetManager;
    private ViewDocument $document;


    public function __construct(ViewDocument $document)
    {
        $this->document = $document;
        $this->assetManager = $document->getAssetManager();
    }

    public function getFilters()
    {
        return [
            new TwigFilter('asset', [$this, 'asset']),
        ];
    }

    function asset(string $path)
    {
        return $this->assetManager->getPublicPath($path);
    }
}
