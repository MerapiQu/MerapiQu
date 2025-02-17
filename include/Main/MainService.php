<?php

namespace MerapiPanel\Main;

use Il4mb\BlockNode\NodeParser;
use Il4mb\Routing\Http\Code;
use Il4mb\Routing\Http\Request as HttpRequest;
use Il4mb\Routing\Http\Response;
use Il4mb\Routing\Interceptor;
use Il4mb\Routing\Map\Route;
use MerapiPanel\App\Application;
use MerapiPanel\App\Http\Request;
use MerapiPanel\Database\Database;
use MerapiPanel\Entity\Page;
use MerapiPanel\System\WebService;
use Throwable;

class MainService extends WebService implements Interceptor
{
    protected Database $db;
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $app->router->addInterceptor($this);
        $this->db = $app->db;
    }

    function handle(Request $request, Response $response): bool
    {
        if ($response->getCode() != Code::OK || $response->getCode() != Code::CREATED) {
            return true;
        }
        return false;
    }

    function dispath(Response $response): Response
    {
        $isOke = $response->getCode() == Code::OK || $response->getCode() == Code::CREATED;
        $template = $isOke ? "base" : ($response->getCode() == Code::NOT_FOUND ? "404" : "error");

        $context = is_array($response->getContent()) ? $response->getContent() : [$response->getContent()];
        $response->setContent(view($template, [
            ...$context
        ]));
        return $response;
    }


    public function onAddRoute(Route &$route): bool
    {
        return false;
    }
    public function onInvoke(Route &$route): bool
    {
        return false;
    }
    public function onBeforeInvoke(Route &$route): bool
    {
        return false;
    }

    public function onDispatch(HttpRequest &$request, Response &$response): bool
    {

        return false;
    }

    public function onFailed(Throwable $t, HttpRequest &$request, Response &$response): bool
    {
        if ($t->getCode() == 404) {
            $repository = $this->db->getRepository(Page::class);
            $slug = preg_replace(
                pattern: "/[^a-zA-Z0-9\/]+/",
                replacement: "-",
                subject: urldecode(rtrim($request->uri->getPath() ?? "", "\/"))
            ) . "/";

            $page = $repository->findOneBy([
                "slug" => $slug,
                "status" => 1
            ]);
            if ($page) {
                $this->buildPage($page, $response);
                return true;
            }
        }
        return false;
    }

    function buildPage(Page $page, Response $response)
    {
        $response->setContentType("text/html");
        $response->setCode(Code::OK);
        $response->setContent([
            "body" => NodeParser::fromArray($page->getBody() ?? [])->render(),
            "head" => NodeParser::fromArray($page->getHead() ?? [])->render()
        ]);
    }
}
