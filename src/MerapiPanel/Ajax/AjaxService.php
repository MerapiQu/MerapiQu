<?php

namespace MerapiPanel\Ajax;

use Il4mb\Routing\Http\ContentType;
use Il4mb\Routing\Http\Response;
use MerapiPanel\App\Http\Request;
use MerapiPanel\System\WebService;

class AjaxService extends WebService
{

    function handle(Request $request, Response $response): bool
    {
        if ($request->isAjax()) {
            return true;
        }
        return false;
    }

    function dispath(Response $response): Response
    {
        $response->setContentType(ContentType::JSON);
        return $response;
    }
}
