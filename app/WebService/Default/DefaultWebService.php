<?php

namespace App\WebService\Default;

use App\WebService\WebService;
use Il4mb\Routing\Http\Request;
use Il4mb\Routing\Http\Response;

class DefaultWebService extends WebService
{

    function __construct()
    {
        parent::__construct(__DIR__);
    }
    function isAccepted(Request $request): bool
    {
        return true;
    }


    function dispath(Response $response): Response
    {

        $content = $response->getContent();
        $request = Request::getInstance();
        if ($request->isAjax()) {
            $response->setContentType("application/json");

            $newContent = [
                "status"  => $response->getCode() == HTTP_CODE::OK || $response->getCode() == HTTP_CODE::CREATED,
                "message" => $response->getCode()->reasonPhrase(),
                "data"    => $content
            ];
            $response->setContent($newContent);
        }

        return parent::dispath($response);
    }
}
