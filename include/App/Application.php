<?php

namespace MerapiPanel\App {

    use Il4mb\Routing\Http\Request;
    use Il4mb\Routing\Http\Response;
    use Il4mb\Routing\Router;
    use MerapiPanel\Admin\AdminService;
    use MerapiPanel\App\Http\Request as HttpRequest;
    use MerapiPanel\Database\Database;
    use MerapiPanel\Main\MainService;
    use MerapiPanel\System\WebContainer;
    use MerapiPanel\System\WebService;
    use Symfony\Component\Filesystem\Path;

    class Application extends WebContainer
    {
        public readonly Database $db;
        public readonly Router $router;

        /**
         * @var array<Webservice> $webservices
         */
        protected array $webservices;

        function __construct(string $basePath)
        {

            $this->loadEnv($basePath);
            parent::__construct(__DIR__);
            $this->db = new Database([Path::canonicalize(__DIR__ . "/../Entity")]);
            $this->router = new Router(interceptors: [], options: []);
            $this->webservices = [
                new AdminService($this),
                new MainService($this)
            ];
        }

        function handle(Request|null $request = null)
        {
            $this->response = $this->router->dispatch($request ?? HttpRequest::getInstance());
            foreach ($this->webservices as $webservice) {
                if ($webservice->handle($request, $this->response)) {
                    $webservice->dispath($this->response);
                    break;
                }
            }
        }

        private function loadEnv(string $basePath)
        {
            $path = Path::join($basePath, ".env");

            if (!file_exists($path)) {
                error_log("Environment file not found: " . $path);
                return;
            }

            $content = file_get_contents($path);

            foreach (array_filter(explode("\n", trim($content))) as $line) {
                $line = trim($line);

                // Skip comments and empty lines
                if (empty($line) || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                    continue;
                }

                // Extract key-value pair safely
                if (preg_match('/^([\w.]+)\s*=\s*(.*)$/', $line, $matches)) {
                    $key = $matches[1];
                    $value = trim($matches[2], '"\''); // Remove surrounding quotes if present
                    $_ENV[trim($key)] = $value;
                } else {
                    error_log("Invalid line in .env: " . $line);
                }
            }
            $_ENV["APP_CWD"] = $basePath;
        }
    }
}
