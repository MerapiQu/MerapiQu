<?php

namespace App\WebService;

use App\HttpSystem\Request;

class WebServiceManager
{
    protected $app;
    protected $request;
    /**
     * @var array<WebService>
     */
    protected array $services = [];

    function __construct(Request $request, array $services)
    {
        $this->request  = $request;
        $this->services = $services;
    }

    function add(WebService $service)
    {
        $this->services[] = $service;
    }

    function whereService() {
        foreach ($this->services as $service) {
            if ($service->isAccepted($this->request)) {
                return $service;
            }
        }
        return null;
    }
    
}
