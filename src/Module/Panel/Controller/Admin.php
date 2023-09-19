<?php

namespace Mp\Module\Panel\Controller;

use Mp\Core\Abstract\Module;

class Admin extends Module
{

    protected $box;

    public function setBox($box)
    {
        $this->box = $box;
    }


    public function register($router)
    {

        $router->get('/', "index", self::class);
        $router->get('/pages', "pages", self::class);
    }

    function index($viewEn)
    {

        return $viewEn->render("base.html.twig");
    }

    public function pages($viewEn)
    {

        return $viewEn->render("pages.html.twig");
    }
}
