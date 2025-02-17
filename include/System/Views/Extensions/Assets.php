<?php

namespace MerapiPanel\System\Views\Extensions;

use MerapiPanel\System\Views\ExtensionAdapter;
use MerapiPanel\System\WebEnvironment;
use Twig\TwigFilter;

class Assets extends ExtensionAdapter
{

    public function __construct(WebEnvironment $environment)
    {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('asset', [$this, 'asset']),
        ];
    }

    function asset(string $path)
    {
       // return $this->assetManager->getPublicPath($path);
    }
}
