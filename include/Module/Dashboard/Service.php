<?php
namespace MerapiPanel\Module\Dashboard;
use MerapiPanel\Box\Module\__Fragment;

class Service extends __Fragment
{
    protected $module;
    function onCreate(\MerapiPanel\Box\Module\Entity\Module $module)
    {
        $this->module = $module;
    }


    function test($hallo, $hallo2) {

        error_log("test $hallo $hallo2");

        return "Hallo user";
    }

}