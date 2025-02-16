<?php

namespace MerapiPanel\Database;

use Symfony\Component\Filesystem\Path;

class Database
{
    private $entityManager;
    private array $paths = [];

    function __construct($paths = [])
    {
        $this->paths = $paths;
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
}
