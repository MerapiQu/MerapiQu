<?php
namespace App\WebService;

interface ServiceHolder {
    function whereService() : WebService;
}