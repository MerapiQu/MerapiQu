<?php

namespace App\System\Views;

use App\CoreModules\Blocks\NodeParser;
use Exception;

class View
{

    private bool $hasRendered = false;
    protected string $name;
    protected array $data;
    protected array $rendered = [];

    function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    function isRendered(): bool
    {
        return $this->hasRendered;
    }

    function getRendered(): array
    {
        return $this->rendered;
    }

    function render(ViewEngine $viewEngine)
    {
        if ($this->hasRendered) throw new Exception("View already rendered");
        $this->hasRendered = true;
        $this->rendered = NodeParser::fromHTML(
            $viewEngine->render(
                $this->name,
                $this->data
            )
        );
        return $this->rendered;
    }
}
