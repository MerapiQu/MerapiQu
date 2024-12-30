<?php

namespace App\System\Views;

use App\ContentManagement\AssetManager;
use App\CoreModules\Blocks\BlockNode;
use App\CoreModules\Blocks\NodeParser;
use App\CoreModules\Blocks\NodeQuery;
use App\CoreModules\Patterns\Pattern;
use App\CoreModules\Patterns\PatternManager;
use App\Entity\Page;
use App\System\Theme\Theme;
use App\WebService\WebService;
use Exception;
use Override;

class ViewDocument extends BlockNode
{
    private ?View $view = null;
    // private array $data;
    private ViewEngine $viewEngine;
    private ?PatternManager $patternManager = null;
    private WebService $webService;
    private AssetManager $assetManager;


    public function __construct(?View $view = null, ?WebService $webService = null)
    {

        if ($webService instanceof WebService) {
            $this->webService = $webService;
        } else {
            $debug = debug_backtrace();
            if (!isset($debug[1]['object']) || !($debug[1]['object'] instanceof WebService))
                throw new Exception("Not allowed init ViewDocument not from WebService");
            $this->webService = $debug[1]['object'];
        }
        $this->view = $view;
        $this->assetManager = new AssetManager([
            $this->webService->getPath()
        ]);

        $this->viewEngine = new ViewEngine($this);

        parent::__construct("html", [
            new BlockNode("head", [
                new BlockNode("meta", null, [
                    "charset" => "UTF-8"
                ]),
                new BlockNode("title", "MerapiPanel"),
                new BlockNode("meta", null, [
                    "name" => "viewport",
                    "content" => "width=device-width, initial-scale=1.0"
                ]),
                new BlockNode("link", null, [
                    "rel" => "stylesheet",
                    "href" => "/assets/dist/main.css"
                ]),
            ]),
            new BlockNode("body", []),
            new BlockNode("script", null, ["src" => "/assets/dist/main.js", "id" => "main-js"]),
        ]);



        if ($this->view) {
            $content = $this->view->render($this->viewEngine);
            $this->body()->prepend($content);
        }
    }

    function getAssetManager(): AssetManager
    {
        return $this->assetManager;
    }

    function setView(View $view)
    {
        $this->view = $view;
        $this->body()->prepend($this->view->render($this->viewEngine));
    }

    function head(): NodeQuery
    {
        return $this->query("head");
    }

    function setTitle(string $title)
    {
        $this->query("title")->setContent($title);
    }

    function script()
    {
        return $this->query("script");
    }
    function linkStyle()
    {
        return $this->query("link[rel='stylesheet']");
    }


    function body(): NodeQuery
    {
        return $this->query("body");
    }

    function setPatternManager(PatternManager $patternManager)
    {
        $this->patternManager = $patternManager;
    }

    function renderView(View $view)
    {
        return $view->render($this->viewEngine);
    }

    #[Override]
    function render(): string
    {
        if (isset($this->view) && !empty($this->view) && !$this->view->isRendered()) {
            $content = $this->view->render($this->viewEngine);
            $this->body()->prepend($content);
        }

        $html = parent::render();
        $html = trim($html);
    
        $html = preg_replace('/\/\*.*?\*\//s', '', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        return $html;
    }

    function __tostring()
    {
        return $this->render();
    }
}
