<?php

namespace Panel;

use App\HttpSystem\Map\GET;
use Doctrine\ORM\Query\Expr\Func;

class Controller
{

    #[GET('@admin/dashboard')]
    public function index()
    {

        return view("index");
    }
    #[GET('@admin/dashboard/settings')]
    public function settings()
    {

        return view("settings");
    }


    #[GET('@admin/dashboard/pages')]
    public function pages()
    {

        return view("pages.index");
    }
    #[GET('@admin/dashboard/pages/editor')]
    public function pages_editor()
    {

        return view("pages.editor");
    }




    #[GET('@admin/dashboard/features')]
    public function features()
    {
        return view("features");
    }
}
