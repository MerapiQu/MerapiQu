<?php

namespace App\WebService\Error;

use App\Application;
use App\WebService\WebService;
use Il4mb\Routing\Http\Code;
use Il4mb\Routing\Http\ContentType;
use Il4mb\Routing\Http\Request;
use Il4mb\Routing\Http\Response;

class ErrorWebService extends WebService
{

    function __construct()
    {
        parent::__construct(__DIR__);
    }
    function init(Application $app) {}

    function isAccepted(Request $request): bool
    {
        return true;
    }

    function dispath(Response $response): Response
    {
        $code    = $response->getCode();
        $content = $response->getContent();
        $request = Request::getInstance();

        if (!is_array($content)) return parent::dispath($response);

        $errorHttpCode = Code::fromCode($content['code'] ?? 500);
        $response->setCode($errorHttpCode ?? Code::INTERNAL_SERVER_ERROR);

        if ($request->isAjax() || $request->isAccept(ContentType::JSON)) {

            $response->setContentType("application/json");

            // Format content if it's an array
            if (is_array($content)) {
                // remove unwanted response
                $data    = $content["data"] ?? null;
                $message = $content["message"] ?? "Success";
                unset($content["message"]);

                $content = [
                    "data"    => $data,
                    "message" => $message
                ];
            }

            // Prepare the final response content
            $newContent = [
                "status" => $code === Code::OK,
                ...(is_array($content) ? $content : ["message" => $content])
            ];

            $response->setContent($newContent);
        } else {
            if ($code === Code::NOT_FOUND) {
                $response->setContent(view("404"));
            } else {
                $response->setContentType("text/html");
                $response->setContent(view("error", $content));
            }
        }

        return parent::dispath($response);
    }
}
