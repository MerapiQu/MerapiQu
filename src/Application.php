<?php

namespace il4mb\Mpanel;

use il4mb\Mpanel\Core\Database;
use il4mb\Mpanel\Core\Directory;
use il4mb\Mpanel\Core\Http\Request;
use il4mb\Mpanel\Core\Http\Response;
use il4mb\Mpanel\Logger\Logger;
use il4mb\Mpanel\Core\Http\Router;
use il4mb\Mpanel\Core\EventSystem;
use il4mb\Mpanel\Core\Plugin\PluginManager;
use il4mb\Mpanel\Exceptions\Error;
use il4mb\Mpanel\Core\Modules\ModuleStack;
use il4mb\Mpanel\TemplateEngine\TemplateEngine;
use Throwable;

class Application
{


    const GET_ASSIGNMENT      = 'app_get_assignment';
    const POST_ASSIGNMENT     = 'app_post_assignment';
    const PUT_ASSIGNMENT      = 'app_put_assignment';
    const VIEW_ASSIGNMENT     = 'app_view_assignment';
    const DELETE_ASSIGNMENT   = 'app_delete_assignment';
    const ON_SET_TEMPLATE     = 'on_set_template';
    const ON_GET_TEMPLATE     = 'on_get_template';
    const ON_SET_CONFIG       = 'on_setconfig';
    const ON_GET_CONFIG       = 'on_getconfig';
    const ON_SET_DATABASE     = 'on_setdatabase';
    const ON_GET_DATABASE     = 'on_getdatabase';
    const ON_GET_PLUGIN       = 'on_getplugin';
    const ON_SET_PLUGIN       = 'on_setplugin';
    const ON_SET_LOGGER       = 'on_setlogger';
    const ON_GET_LOGGER       = 'on_getlogger';
    const ON_CONTENT_RESPONSE = 'on_response';
    const ON_REQUEST          = 'on_request';
    const BEFORE_REQUEST      = 'before_request';
    const AFTER_REQUEST       = 'after_request';


    protected $router;
    protected $database;
    protected $logger;
    protected PluginManager $pluginManager;
    protected Directory $directory;
    protected EventSystem $eventSystem;
    protected ModuleStack $moduleManager;


    /**
     * Constructor function for initializing the class.
     * 
     * It creates instances of the EventSystem, MiddlewareStack,
     * Router, Config, Logger, and PluginManager classes.
     *
     * @return void
     */
    public function __construct()
    {

        $this->eventSystem     = new EventSystem();
        $this->router          = Router::getInstance();
        $this->logger          = new Logger();
        $this->pluginManager   = new PluginManager($this);
        $this->moduleManager   = new ModuleStack();
    }



    public function getEventSystem(): EventSystem
    {

        return $this->eventSystem;
    }



    /**
     * Retrieves the PluginManager object.
     *
     * @return PluginManager The PluginManager object.
     */
    public function getPluginManager(): PluginManager
    {

        return $this->pluginManager;
    }

    function get_directory(): Directory
    {

        return $this->directory;
    }

    /**
     * Set the router object.
     *
     * @param Router $router The router object to set.
     */
    public function setRouter(Router $router)
    {

        $this->router = $router;
    }

    /**
     * Retrieves the router object.
     *
     * @return Router The router object.
     */
    public function getRouter()
    {

        return $this->router;
    }



    /**
     * Sets the database for the object.
     *
     * @param Database $database The database object to set.
     */
    public function setDatabase(Database $database)
    {

        $this->database = $database;

        $this->eventSystem->fire(self::ON_SET_DATABASE, [$database]);
    }




    /**
     * Retrieves the database object.
     *
     * @return Database The database object.
     */
    public function getDatabase(): Database
    {

        $this->eventSystem->fire(self::ON_GET_DATABASE, [$this->database]);

        return $this->database;
    }




    /**
     * Set the logger for the class.
     *
     * @param Logger $logger the logger instance to set
     */
    public function setLogger(Logger $logger)
    {

        $this->logger = $logger;

        $this->eventSystem->fire(self::ON_SET_LOGGER, [$logger]);
    }




    /**
     * Retrieves the logger instance.
     *
     * @return Logger The logger instance.
     */
    public function getLogger(): Logger
    {

        $this->eventSystem->fire(self::ON_GET_LOGGER, [$this->logger]);

        return $this->logger;
    }




    /**
     * Get method for the router.
     *
     * @param string $path The path to match.
     * @param callable $callback The callback function.
     * @return Router The router object.
     */
    public function get(string $path, mixed $callback): Router
    {

        $this->eventSystem->fire(self::GET_ASSIGNMENT, [$path, $callback]);

        $this->router->get($path, $callback);

        return $this->router;
    }




    /**
     * Handles a POST request to a specific path.
     *
     * @param string $path The path to handle the POST request.
     * @param callable $callback The callback function to execute when the POST request is made.
     * @return Router The router object.
     */
    public function post(string $path, mixed $callback)
    {

        $this->eventSystem->fire(self::POST_ASSIGNMENT, [$path, $callback]);

        $this->router->post($path, $callback);

        return $this->router;
    }





    /**
     * Executes a PUT request on the specified path.
     *
     * @param string $path The path to execute the PUT request on.
     * @param callable $callback The callback function to handle the PUT request.
     * @return Router The Router object.
     */
    public function put(string $path, mixed $callback)
    {

        $this->eventSystem->fire(self::PUT_ASSIGNMENT, [$path, $callback]);

        $this->router->put($path, $callback);

        return $this->router;
    }




    /**
     * Deletes a resource at the specified path.
     *
     * @param string $path The path of the resource to delete.
     * @param callable $callback The callback function to execute when the resource is deleted.
     * @return $this The current instance of the object.
     */
    public function delete(string $path, mixed $callback)
    {

        $this->eventSystem->fire(self::DELETE_ASSIGNMENT, [$path, $callback]);

        $this->router->delete($path, $callback);

        return $this->router;
    }




    /**
     * Runs the application.
     */
    public function run(): void
    {
        try {

            // Trigger the BEFORE_REQUEST event
            $this->eventSystem->fire(self::BEFORE_REQUEST);

            // Create a new request
            $request = new Request();

            // Run the plugins
            $this->pluginManager->runPlugins();

            // Send the response
            $this->sendResponse($this->router->dispatch($request));

            // Trigger the AFTER_REQUEST event
            $this->eventSystem->fire(self::AFTER_REQUEST);
            
        }
        catch (Throwable $e) 
        {

            if ($e instanceof Error) 
            {

                echo $e->getHtmlView();

            } else {

                $error = new Error($e->getMessage(), $e->getCode());
                echo  $error->getHtmlView();
            }
        }
    }





    // Method to Send HTTP Response
    protected function sendResponse(Response $response): void
    {

        $response->send();

        if ($response->getHeader("Content-Type") == "text/html") {

            $data = json_decode($response->getContent(), true) ?? [];

            if (!json_last_error()) {

              //  $data = array_merge($data, $this->getConfig()->all());
            } else {

               // $data = $this->config->all();
            }

            // if (isset($this->templateEngine) && $this->templateEngine instanceof TemplateEngine) {

            //     $response->setContent($this->templateEngine->render($data));
            // }
        }

        $this->eventSystem->fire(self::ON_CONTENT_RESPONSE, [$response]);

        // Send content
        echo $response->getContent();
    }
}
