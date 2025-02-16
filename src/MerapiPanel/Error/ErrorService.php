<?php

namespace MerapiPanel\Error;

use Il4mb\Routing\Http\Code;
use Il4mb\Routing\Http\Response;
use MerapiPanel\App\Http\Request;
use MerapiPanel\System\WebService;

class ErrorService extends WebService
{

    function handle(Request $request, Response $response): bool
    {
        if ($response->getCode() != Code::OK || $response->getCode() != Code::CREATED) {
            return true;
        }
        return false;
    }

    function dispath(Response $response): Response
    {
        $response->setContent($this->getPath());
        return $response;
    }
}
