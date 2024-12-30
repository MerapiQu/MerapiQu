<?php

namespace App\CoreModules;

use App\Entity\Config;
use Doctrine\ORM\EntityRepository;

class ConfigManager
{

    private Database $db;
    private EntityRepository $repository;
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->repository = $this->db->getRepository(Config::class);
    }


    public function get(string $name, $default = null)
    {
        /**
         * @var Config $config
         */
        $config = $this->repository->findOneBy(["name" => $name]);
        if ($config === null) {
            return $default;
        }
        return $config->getValue();
    }


    public function set(string $name, string $value): void
    {
        $config = new Config($name, $value);
        $this->repository->save($config);
    }
}
