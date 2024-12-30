<?php

namespace App\HttpSystem\Map;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class POST
{
    public function __construct(
        public string $path,
        public string $ajax  = false,
        public string $admin = false,
    ){ }
}


#[Attribute(Attribute::TARGET_METHOD)]
class GET
{
    public function __construct(
        public string $path,
        public string $ajax  = false,
        public string $admin = false,
    ){ }
}

#[Attribute(Attribute::TARGET_METHOD)]
class PUT
{
    public function __construct(
        public string $path,
        public string $ajax  = false,
        public string $admin = false,
    ){ }
}


#[Attribute(Attribute::TARGET_METHOD)]
class DELETE {
    public function __construct(
        public string $path,
        public string $ajax  = false,
        public string $admin = false,
    ){ }
}
