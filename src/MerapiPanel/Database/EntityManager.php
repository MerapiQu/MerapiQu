<?php

namespace MerapiPanel\Database;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use League\Config\Exception\InvalidConfigurationException;


class EntityManager extends DoctrineEntityManager
{

    private $connection;

    public function __construct($paths = [])
    {
        $config = $this->createConfig($paths);
        $this->connection = $this->createConnection($config);
        $eventManager = $this->createEventManager();
        parent::__construct($this->connection, $config, $eventManager);

        $this->initializeSchema($this->connection);
    }


    /**
     * Create the Doctrine ORM configuration.
     */
    private function createConfig($paths)
    {
        return ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths,
            isDevMode: $_ENV['APP_ENV'] === 'development',
        );
    }

    private function createConnection($config)
    {

        // [$DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASSWORD, $DB_DRIVER] = $_ENV;
        extract($_ENV);
        if (!isset($DB_HOST) || !isset($DB_NAME) || !isset($DB_USER) || !isset($DB_DRIVER)) {
            throw new InvalidConfigurationException("Fatal error, please add DB_HOST, DB_NAME, DB_DRIVER and DB_USER to your .env file.");
        }
        $DB_PORT = isset($DB_PORT) ? $DB_PORT : 3306;
        $DB_PASSWORD = isset($DB_PASSWORD) ? $DB_PASSWORD : "";

        return DriverManager::getConnection([
            'driver'   => $DB_DRIVER,
            'host'     => $DB_HOST,
            'port'     => $DB_PORT,
            'user'     => $DB_USER,
            'password' => $DB_PASSWORD,
            'dbname'   => $DB_NAME,
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
            /**
             * @var object $metadata
             */
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
        // if (Module::isModuleEntity($classMetadata->getName())) {
        //     $moduleName = Module::getModuleName($classMetadata->getName());
        //     // Set the table name dynamically
        //     $classMetadata->setPrimaryTable([
        //         'name' => strtolower($moduleName . "_" . $args->getClassMetadata()->getTableName()),
        //     ]);
        // }
    }
}
