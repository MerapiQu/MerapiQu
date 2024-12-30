<?php

namespace WebService\Panel;

use App\HttpSystem\Map\GET;


class Controller
{

    #[GET('@admin/dashboard')]
    public function index() {

        return view("index");
    }
    #[GET('@admin/dashboard/settings')]
    public function settings() {

        return view("settings");
    }
}
