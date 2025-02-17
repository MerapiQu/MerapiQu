<?php

if (file_exists(__DIR__ . "/error.log")) {
    // unlink((__DIR__ . "/php-error.log"));
}
ini_set("error_log",  "error.log");

include_once __DIR__ . '/vendor/autoload.php';

use MerapiPanel\App\Application;
use MerapiPanel\App\Http\Request;

$app = new Application(realpath(__DIR__));
$app->handle(Request::getInstance());
$app->send();
