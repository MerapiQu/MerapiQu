<?php

namespace App\HttpSystem\Routers;

use App\HttpSystem\Controller\Callback;
use App\HttpSystem\HTTP_CODE;
use App\HttpSystem\Request;
use App\HttpSystem\Response;
use App\HttpSystem\Middlewares\MiddlewareExecutor;
use App\WebService\WebService;
use Exception;

class Router implements Interceptor
{
    /**
     * @var Response
     */
    protected Response $response;
    public function __construct(Response $response)
    {
        $this->addInterceptor($this);
        $this->response = $response;
    }

    /**
     * routing interceptors
     * @var array<Interceptor>
     */
    protected static array $interceptors = [];
    function addInterceptor(Interceptor $interceptor)
    {
        self::$interceptors[] = $interceptor;
    }

    /**
     * @var Route
     */
    private static Route $route;

    private WebService $webService;

    function dispatch(WebService $webService)
    {
        $this->webService = $webService;
        $request = Request::getInstance();
        if (!isset(self::$route)) {
            throw new Exception("Route not found", 404);
        }
        foreach (self::$interceptors as $interceptor) {
            if (!$interceptor->preHandler(self::$route)) {
                return $this->response->setCode(HTTP_CODE::NOT_ACCEPTABLE);
            }
        }

        return $this->handle(self::$route, $request, $webService);
    }

    protected function handle(Route $route, Request $request, WebService $webService): Response
    {

        $executor = new MiddlewareExecutor($route->getMiddleware());
        $response = $executor($request, function (Request $request) use ($route) {
            return $this->process($request, $route);
        });
        $response = $webService->dispath($response);
        foreach ($route->children as $callback) {
            $callback($response, $request, $this->webService);
        }
        return $response;
    }

    private function process(Request $request, Route $route): Response
    {
        $output = $route->getCallback()($this->response, $request, $this->webService, $route);
        if ($output instanceof Response) return $output;
        if ($output) $this->response->setContent($output);
        return $this->response;
    }

    /**
     * Summary of add
     * @param Route|array<Route> $route
     * @return void
     */
    public static function add(Route|array $route)
    {
        if (is_array($route)) {
            foreach ($route as $r) {
                self::add($r);
            }
            return;
        }

        $request = Request::getInstance();
        foreach (self::$interceptors as $interceptor) {
            if (!$interceptor->addHandler($route)) {
                return;
            }
        }
        // filter only same method
        if ($request->getMethod() !== $route->getMethod()) {
            return;
        }
        $uri = $request->getUri();
        // optimize load only mathes route collected
        if ($uri->matchRoute($route)) {
            if (isset(self::$route)) {
                self::$route->addChild($route->getCallback());
                return;
            }
            self::$route = $route;
        }
    }


    function addHandler(Route $route): bool
    {
        return true;
    }
    function postHandler(Route $route): bool
    {
        return true;
    }
    function preHandler(Route $route): bool
    {
        return true;
    }
}
