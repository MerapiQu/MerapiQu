<?php

namespace App\ModuleSystem\ModuleModel;

use App\CoreModules\ObjectCached;
use App\ModuleSystem\ModuleManager;
use App\ModuleSystem\ModuleModel\Fragments\FragmentContext;

/**
 * Module context
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 * @extends     parent<FragmentContext>
 */
final class ModuleContext extends FragmentContext
{
    public function __construct(ModuleManager $manager, $path)
    {
        parent::__construct($manager, $path);
        $this->provider  = new Module();
        $this->client    = ObjectCached::with(ModuleClient::class, [$this->signature])->get();
        $this->injectProperty("signature", $this->signature);
        $this->syncMethods();
    }

    function call(string $classOrigin, string $method, array $args = [])
    {

        if ($method == "getClass") {
            return $this->getClassName();
        }
        return parent::call($classOrigin, $method, $args);
    }
}
