<?php

namespace App\ModuleSystem\ModuleModel;

use App\ModuleSystem\ModuleModel\Fragments\FragmentClient;

/**
 * Module client
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 * @extends     parent<FragmentClient>
 */

final class ModuleClient extends FragmentClient
{

    final function __debugInfo()
    {
        return [
            "path" => $this->getPath(),
        ];
    }
}
