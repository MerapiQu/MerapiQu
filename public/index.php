<?php

if (file_exists(__DIR__ . "/php-error.log"))
    // unlink((__DIR__ . "/php-error.log"));
ini_set("error_log", __DIR__ . "/php-error.log");

include_once __DIR__ . '/../vendor/autoload.php';

use App\Application;
use Services\Admin\AdminService;

$app = new Application();
// $app->addService(new AdminService());
$app->run();
