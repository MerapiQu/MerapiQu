<?php

namespace MerapiQu\Module\Modules\Controller;

use MerapiQu\Core\Abstract\Module;

class Admin extends Module
{

    public function register($router)
    {

        $router->get("/modules", "index", self::class);

        $panel = $this->getBox()->Module_Panel();
        $site = $this->getBox()->Module_Site();
        $panel->addMenu([
            "name" => "Modules",
            "link" => $site->adminLink("/modules"),
            'icon' => 'fa-solid fa-cube'
        ]);
    }

    public function index($view)
    {

        return $view->render("index.html.twig");
    }
}
