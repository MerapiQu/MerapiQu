<?php

namespace MerapiPanel\App;

use Il4mb\Routing\Http\Response;
use MerapiPanel\System\WebService;

class ApplicationService extends WebService
{
    public function __construct(\Il4mb\Routing\Router $router)
    {
        parent::__construct($router);
    }
    function dispath(Response $response): Response
    {
        return parent::dispath($response);
    }
}
