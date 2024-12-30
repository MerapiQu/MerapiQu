<?php

namespace App\CoreModules;

use App\Miscellaneous\Utility;
use App\ModuleSystem\ModuleManager;
use App\ModuleSystem\ModuleModel\Module;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;

class EntityManager extends DoctrineEntityManager
{
    private $connection;

    public function __construct($paths = [])
    {
        $config = $this->createConfig($paths);
        $this->connection = $this->createConnection($config);
        $eventManager = $this->createEventManager();
        parent::__construct($this->connection, $config, $eventManager);

        // $this->initializeSchema($this->connection);
    }

    /**
     * Create the Doctrine ORM configuration.
     */
    private function createConfig($paths)
    {
        return ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: true,
        );
    }

    /**
     * Create and return the database connection.
     */
    private function createConnection($config)
    {
        return DriverManager::getConnection([
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => '',
            'dbname'   => 'database_mp',
        ], $config);
    }

    /**
     * Create and return the event manager.
     */
    private function createEventManager()
    {
        $eventManager = new \Doctrine\Common\EventManager();
        $eventManager->addEventSubscriber(new DynamicTableNameSubscriber());
        return $eventManager;
    }

    /**
     * Initialize schema updates and creations.
     */
    private function initializeSchema($connection)
    {
        $schemaTool = new SchemaTool($this);
        $metadatas  = $this->getMetadataFactory()->getAllMetadata();
        $schemaManager = $connection->createSchemaManager();
        foreach ($metadatas as $metadata) {
            if ($schemaManager->tableExists($metadata->getTableName())) {
                $schemaTool->updateSchema([$metadata]);
            } else {
                $schemaTool->createSchema([$metadata]);
            }
        }
    }

    public function __wakeup()
    {
        // Reinitialize the connection after unserialization
        if ($this->connection === null) {
            $this->connection = $this->createConnection($this->createConfig([]));
        }

        // Re-run schema updates as needed after unserialization
        // $this->initializeSchema($this->connection);
    }
}


class DynamicTableNameSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        // Check if the entity is the one you want to modify
        if (Module::isModuleEntity($classMetadata->getName())) {
            $moduleName = Module::getModuleName($classMetadata->getName());
            // Set the table name dynamically
            $classMetadata->setPrimaryTable([
                'name' => strtolower($moduleName . "_" . $args->getClassMetadata()->getTableName()),
            ]);
        }
    }
}
