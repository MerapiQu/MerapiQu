<?php
namespace App\HttpSystem;

enum HTTP_METHOD : string {
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
}