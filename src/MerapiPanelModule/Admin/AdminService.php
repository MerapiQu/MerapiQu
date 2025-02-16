<?php

namespace MerapiPanel\Admin;

use Il4mb\Routing\Http\Response;
use Il4mb\Routing\Router;
use MerapiPanel\App\Http\Request;
use MerapiPanel\System\WebService;
use MerapiPanel\Admin\Controller\DashboardController;
use MerapiPanel\App\Application;

class AdminService extends WebService
{
    public function __construct(Application $app)
    {
        $app->router->addRouteBy("/admin", new DashboardController());
        parent::__construct($app);
    }

    function handle(Request $request, Response $response): bool
    {
        if (rtrim($request->uri->getPath(), "/") == "/admin") {
            return true;
        }
        return false;
    }
}
