<?php

namespace MerapiPanel\Box\Module {

    use MerapiPanel\Box\Container;
    use MerapiPanel\Box\Module\Entity\Fragment;
    use MerapiPanel\Box\Module\Entity\Module;

    abstract class AbstractLoader
    {

        protected string $directory;
        abstract function __construct(string $directory);
        abstract function loadModule(string $name, Container $container): Module|bool;
        abstract function loadFragment(string $name, Module|Fragment $parent): Fragment|null;
        abstract function initialize(Container $container): void;

        /**
         * Summary of getListModule
         * @return array<string, string> list active module key is dirname and value is full path
         */
        abstract function getListModule(): array;
    }
}
