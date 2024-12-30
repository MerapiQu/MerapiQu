<?php
namespace App\System\Views;

use Twig\Extension\ExtensionInterface;

class ExtensionAdapter implements ExtensionInterface {
    public function getFilters() {
        return [];
    }

    public function getFunctions() {
        return [];
    }

    public function getTests() {
        return [];
    }

    public function getOperators() {
        return [];
    }

    public function getNodeVisitors() {
        return [];
    }

    public function getTokenParsers() {
        return [];
    }

    public function getName() {
        return 'app';
    }
}
