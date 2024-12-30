<?php

namespace App\CoreModules;

use Symfony\Component\Filesystem\Path;

class Database
{
    private $entityManager;
    private array $paths = [];

    function __construct($paths = [])
    {
        $this->paths = [
            Path::canonicalize(__DIR__ . "/../Entity"),
            ...$paths
        ];
        $this->entityManager = new EntityManager($this->paths);
    }

    function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }


    function save($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    function getRepository(string $name)
    {
        $repository = $this->entityManager->getRepository($name);
        return $repository;
    }


    public static function getInstance()
    {
        return new self();
    }
}
