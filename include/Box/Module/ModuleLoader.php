<?php

namespace MerapiPanel\Box\Module {

    use Exception;
    use MerapiPanel\Box\Module\AbstractLoader;
    use MerapiPanel\Box\Container;
    use MerapiPanel\Box\Module\Entity\Module;
    use MerapiPanel\Box\Module\Entity\Fragment;
    use MerapiPanel\Box\Module\Entity\Proxy;
    use MerapiPanel\Utility\Http\Request;
    use Symfony\Component\Filesystem\Path;
    use Throwable;

    class ModuleLoader extends AbstractLoader
    {

        protected string $directory;
        protected string $classNamePrefix = "\\MerapiPanel\\Module";
        private static array $defaultModules = ["Setting", "Panel", "Ajax", "Auth", "Editor", "FileManager", "Dashboard", "Setting", "User"];

        public function __construct(string $directory)
        {
            $this->directory = $directory;
        }

        public final static function getDefaultModules()
        {
            return self::$defaultModules;
        }

        public function initialize(Container $container): void
        {
            $this->postLoad($container);
            $this->registerController($container);
        }


        public final function postLoad($container)
        {
            foreach (glob(Path::join($this->directory, "*/.active")) as $dirname) {
                $dirname = preg_replace("/\/.active$/", "", $dirname);
                $moduleName = basename($dirname);
                $service = $container->$moduleName->Service;
                if ($service instanceof Proxy && $service->__method_exists("onInit")) {
                    $service->onInit();
                }
            }
        }


        public final function registerController($container)
        {

            $access = ['guest'];
            if (isset($_ENV['__MP_ADMIN__']['prefix'])) {
                if (strpos(Request::getInstance()->getPath(), $_ENV['__MP_ADMIN__']['prefix']) === 0) {
                    $access[] = "admin";
                }
            }

            foreach (glob(Path::join($this->directory, "*"), GLOB_ONLYDIR) as $dirname) {
                $moduleName = basename($dirname);

                try {
                    /**
                     * @var Fragment $controller
                     */
                    if ($controller = $container->$moduleName->Controller) {

                        foreach ($access as $accessName) {
                            $accessName = ucfirst($accessName);
                            if ($controller->$accessName) {
                                try {
                                    $controller->$accessName->register();
                                } catch (Throwable $e) {
                                    error_log("Unable to register controller: $moduleName, " . $e->getMessage());
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    // silent
                }
            }
        }


        function loadFragment(string $name, Module|Fragment $parent): Fragment|null
        {
            if ($parent instanceof Fragment) {

                if (!class_exists($parent->resolveClassName($name))) {

                    if (file_exists($parent->resolvePath($name))) {
                        return new Fragment($name, $parent);
                    }

                    return null;
                }

                return new Proxy($name, $parent);
            } else if ($parent instanceof Module) {

                if (!class_exists($parent->namespace . '\\' . $name)) {
                    if (file_exists(Path::join($parent->path, $name))) {
                        return new Fragment($name, $parent);
                    }
                    return null;
                }
                return new Proxy($name, $parent);
            }

            return null;
        }


        function loadModule(string $name, Container $container): Module
        {

            $path = Path::join($this->directory, $name);
            if (!file_exists($path)) {
                throw new Exception("Module not found: $name");
            }

            if (!in_array($name, self::$defaultModules) && !file_exists(Path::join($path, ".active"))) throw new Exception("Module {$name} inactive", 500);
            return new Module($container, [
                "namespace" => $this->classNamePrefix . "\\$name",
                "path" => $path,
            ]);
        }
    }
}
