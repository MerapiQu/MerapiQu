<?php

namespace MerapiPanel\App;

use Il4mb\Routing\Http\Response;
use MerapiPanel\System\WebService;

class ApplicationService extends WebService
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    function dispath(Response $response): Response
    {
        return parent::dispath($response);
    }
}
