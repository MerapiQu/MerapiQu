<?php

namespace App {

    use App\CoreModules\Database;
    use App\ModuleSystem\ModuleManager;
    use App\System\Base;
    use App\System\Theme\ThemeManager;
    use App\WebService\Default\DefaultWebService;
    use Panel\WebService as PanelWebService;
    use App\WebService\WebService;
    use App\WebService\WebServiceManager;
    use Il4mb\Routing\Http\Response;
    use Il4mb\Routing\Router;

    class Application extends Base
    {
        protected ModuleManager $moduleManager;
        protected Router $router;
        protected WebService $defaultWebService;
        protected WebServiceManager $webServiceManager;
        protected ThemeManager $themeManager;
        protected Database $database;

        function __construct()
        {

            parent::__construct();

            $this->router = new Router(interceptors: [], options: []);
            $this->defaultWebService = new DefaultWebService();
            // $this->webServiceManager = new WebServiceManager(Request::getInstance(), [
            //     new PanelWebService($this->router)
            // ]);
            // $this->moduleManager =  new ModuleManager(__DIR__ . "/../media/Modules");
            // $modulePaths = $this->moduleManager->getModulePaths();
            // foreach ($modulePaths as $key => $modulePath) {
            //     $path = Path::canonicalize(Path::join($modulePath, "Entity"));
            //     if (!is_dir($path)) {
            //         unset($modulePaths[$key]);
            //     } else {
            //         $modulePaths[$key] = $path;
            //     }
            // }
            // $modulePaths = array_values($modulePaths);
            // $this->database = new Database($modulePaths);
            // $this->injectRepository(
            //     $this->database,
            //     Settings::instance(),
            //     "database"
            // );

            $this->themeManager  = new ThemeManager();
        }

        private function injectRepository(mixed $value, object $target, string $property)
        {
            $reflector = new \ReflectionClass($target);
            if ($reflector->hasProperty($property)) {
                $property = $reflector->getProperty($property);
                $property->setAccessible(true);
                $property->setValue($target, $value);
                $property->setAccessible(false);
            }
        }

        function getTheme()
        {
            return $this->themeManager->getTheme();
        }

        function addService(WebService $service)
        {
            $this->webServiceManager->add($service);
        }

        /**
         * Summary of whereService
         * @throws \BadMethodCallException
         * @return \App\WebService\WebService
         */
        function whereService(): WebService
        {
            if (isset($this->webServiceManager) && $service = $this->webServiceManager->whereService()) {
                return $service;
            } else return $this->defaultWebService;
        }

        function run()
        {

            $webService = $this->whereService();

            // $this->moduleManager->fireAll("onCreate");
            // $this->moduleManager->addService(new class() extends ModuleService {
            //     function onCall(MethodEvent $event): void
            //     {
            //         if ($event->fragmentMethod->hasAttribute(POST::class)) {
            //             throw new \BadMethodCallException("Could't call method \"{$event->methodName}\" is define for route");
            //         }
            //     }
            //     function handle(string $classOrigin, string $methodName, Closure $next): FragmentMethod
            //     {
            //         return $next($classOrigin, $methodName);
            //     }
            // });


            if (!$this->hasErrors()) {

                // get response from route
                $response = $this->router->dispatch($webService);
                // dispath response with defaultWebService
                $final = $this->dispathWithDefault($response);
                // ObjectCached::saveAll();
                // send response
                echo $final->send();
                return;
            }
        }

        private function dispathWithDefault(Response $response)
        {
            return  $this->defaultWebService->dispath($response);
        }
    }
}
