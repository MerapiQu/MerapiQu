<?php

namespace App\WebService;

use App\ContentManagement\PagesManager;
use App\CoreModules\Blocks\TypedNodeManager;
use App\CoreModules\Patterns\PatternManager;
use App\HttpSystem\Request;
use App\HttpSystem\Response;
use App\System\Views\View;
use App\System\Views\ViewDocument;
use App\System\Views\ViewLoader;
use Symfony\Component\Filesystem\Path;

abstract class WebService
{
    protected PatternManager $patternManager;
    protected TypedNodeManager $typedNodeManager;
    protected PagesManager $pagesManager;
    protected string $path;

    function __construct(string $path)
    {
        $this->path = $path;
        $this->patternManager = new PatternManager([
            Path::join($path, "patterns")
        ]);
        $this->pagesManager = new PagesManager([
            Path::join($path, "pages")
        ]);
        $this->typedNodeManager = new TypedNodeManager();
        ViewLoader::addPath(Path::join($path, "views"));
    }

    function getPath(): string
    {
        return $this->path;
    }

    function dispath(Response $response): Response
    {

        $content = $response->getContent();
        if ($content instanceof View) {
            $response->setContent(new ViewDocument($content));
        }
        return $response;
    }
    abstract function isAccepted(Request $request): bool;
}
