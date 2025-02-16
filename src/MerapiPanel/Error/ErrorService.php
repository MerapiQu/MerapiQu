<?php

namespace MerapiPanel\Error;

use Il4mb\Routing\Http\Code;
use Il4mb\Routing\Http\Response;
use MerapiPanel\App\Http\Request;
use MerapiPanel\System\WebService;

class ErrorService extends WebService
{

    public function __construct(\Il4mb\Routing\Router $router)
    {
        parent::__construct($router);
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
        $template = $response->getCode() == Code::NOT_FOUND ? "404" : "error";
        $response->setContent(view($template, [
            $response->getContent()
        ]));
        return $response;
    }
}
