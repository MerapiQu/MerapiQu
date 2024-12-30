<?php

namespace Panel;

use App\CoreModules\Blocks\NodeParser;
use App\HttpSystem\Request;
use App\HttpSystem\Response;
use App\HttpSystem\Routers\Interceptor;
use App\HttpSystem\Routers\Route;
use App\HttpSystem\Routers\Router;
use App\System\Views\View;
use App\System\Views\ViewDocument;
use App\WebService\WebService as AppWebService;
use Override;

class WebService extends AppWebService implements Interceptor
{
    protected string $adminPath = "/panel/admin";
    protected Router $router;

    function __construct(Router $router)
    {
        $this->router = $router;
        parent::__construct(__DIR__);

        if ($this->isAccepted(Request::getInstance())) {
            $this->router->addInterceptor($this);
            $routes = Route::fromController(new Controller());
            Router::add($routes);
        }
    }

    function isAccepted(Request $request): bool
    {
        return $request->getUri()->beginWith($this->adminPath);
    }


    function dispath(Response $response): Response
    {

        $content = $response->getContent();
        $document = new ViewDocument();

        $request = Request::getInstance();
        if ($request->isAjax()) {
            if ($content instanceof View) {
                $document->setView($content);
                $scripts = [];
                foreach ($document->query("script") as $script) {
                    $script->remove();
                    $isCore = $script->getAttr("data-panel-core");
                    if ($isCore || $script->getAttr("src") == "/assets/dist/main.js") $script->remove();
                    else $scripts[] = $script->getAttr("src");
                }

                $styles = [];
                foreach ($document->query("link[rel=stylesheet]") as $style) {
                    $style->remove();
                    $href = $style->getAttr("href");
                    if ($href !== "/assets/dist/main.css") {
                        $styles[] = $href;
                    }
                }

                $content = array_map(fn($item) => $item->toArray(), $document->body()->getChildren());
                $response->setContent([
                    "scripts" => $scripts,
                    "styles"  => $styles,
                    "content" => $content
                ]);
            }
            return $response;
        }

        if ($content instanceof View) {
            $document->setView(view("_", [
                "content" => NodeParser::toHtml($document->renderView($content))
            ]));
            $scripts = $document->query("script");
            foreach ($scripts as $script) {
                $isCore = $script->getAttr("data-panel-core");
                if (!$isCore) {
                    $script->remove();
                }
            }
            $response->setContent($document);
        }

        return parent::dispath($response);
    }


    #[Override]
    function addHandler(Route $route): bool
    {
        $path = ltrim($route->getPath(), "/");
        if (preg_match("/^@admin\//im", $path)) {
            $path = str_replace("@admin", $this->adminPath, $path);
            $route->setPath($path);
        }
        return true;
    }

    #[Override]
    function postHandler(Route $route): bool
    {
        return true;
    }

    #[Override]
    function preHandler(Route $route): bool
    {
        return true;
    }
}
