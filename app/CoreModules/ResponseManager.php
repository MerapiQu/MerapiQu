<?php
namespace App\CoreModules;

use App\Application;
use App\HttpSystem\Response;

class ResponseManager {
    
    private Response $response;
    public function __construct(Response $response) {
        $this->response = $response;
    }
    
}