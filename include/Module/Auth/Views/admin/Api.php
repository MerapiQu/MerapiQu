<?php
namespace MerapiPanel\Module\Auth\Views\Admin {
    use MerapiPanel\Box\Module\__Fragment;

    class Api extends __Fragment
    {
        protected $module;
        function onCreate(\MerapiPanel\Box\Module\Entity\Module $module)
        {
            $this->module = $module;
        }

        function LogedinUser()
        {

            $user = $this->module->getLogedinUser([]);
            return $user;
        }
    }
}