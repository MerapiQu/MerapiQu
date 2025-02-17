<?php

namespace MerapiPanel\System\Views;

class View
{
    protected string $name;
    protected array $data;

    function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getData(): array
    {
        return $this->data;
    }
    
    function __tostring()
    {

        return $this->name;
    }
}
