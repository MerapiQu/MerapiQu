<?php

namespace MerapiPanel\Admin\Controller;

use Il4mb\Routing\Http\Method;
use Il4mb\Routing\Map\Route;

class DashboardController
{

    #[Route(path: "/", method: Method::GET)]
    function index()
    {
        return "Hallo World";
    }
}
