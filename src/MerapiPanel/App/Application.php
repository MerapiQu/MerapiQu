<?php

namespace MerapiPanel\App;

use Il4mb\Routing\Http\Request;
use Il4mb\Routing\Http\Response;
use Il4mb\Routing\Router;
use MerapiPanel\Admin\AdminService;
use MerapiPanel\App\Http\Request as HttpRequest;
use MerapiPanel\Error\ErrorService;
use MerapiPanel\System\WebService;

class Application
{
    protected Router $router;
    protected Response $response;

    /**
     * @var array<Webservice> $webservices
     */
    protected array $webservices;
    function __construct()
    {
        $this->router = new Router(interceptors: [], options: []);
        $this->webservices = [
            new AdminService($this->router),
            new ErrorService($this->router)
        ];
    }


    function handle(Request|null $request = null)
    {
        $this->response = $this->router->dispatch($request ?? HttpRequest::getInstance());
        foreach ($this->webservices as $webservice) {
            if ($webservice->handle($request, $this->response)) {
                $webservice->dispath($this->response);
                break;
            }
        }
    }

    function render()
    {
        echo $this->response->send();
    }
}
