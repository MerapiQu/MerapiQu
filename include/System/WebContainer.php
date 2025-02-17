<?php

namespace MerapiPanel\System {

    use Il4mb\BlockNode\NodeParser;
    use Il4mb\Routing\Http\Code;
    use Il4mb\Routing\Http\ContentType;
    use Il4mb\Routing\Http\Response;
    use MerapiPanel\App\Http\Request;
    use MerapiPanel\System\Views\View;

    abstract class WebContainer extends WebEnvironment
    {
        protected Response $response;
        private array $errors = [];

        function __construct(string $basePath)
        {
            $this->basePath = $basePath;
            parent::__construct($basePath);
            $this->response = new Response(code: Code::INTERNAL_SERVER_ERROR);
            set_error_handler([$this, "__errorHandler"]);
            register_shutdown_function([$this, "__onProgramFinish"]);
            set_exception_handler([$this, "__exceptionHandler"]);
        }

        public function __onProgramFinish()
        {
            if ($this->hasErrors()) {
                if (ob_get_level() > 0) ob_clean();
                $this->response->setCode(Code::INTERNAL_SERVER_ERROR);
                $this->response->setContent(view("error", [
                    "error" => $this->getLastedError()
                ]));
                return $this->send();
            }
        }

        public function __errorHandler($errno, $errstr, $errfile, $errline)
        {
            $this->errors[] = ErrorContainer::create(...[
                "name" =>  "UnhandledException",
                "message" => "Caught error in $errfile at line $errline: $errstr",
                "file" => $errfile,
                "line" => $errline
            ]);
            return true;
        }

        public function __exceptionHandler($exception)
        {
            $this->errors[] = new ErrorContainer($exception);
        }


        public function getLastedError()
        {
            return $this->errors[0] ?? false;
        }


        public function hasErrors()
        {
            return !empty($this->errors);
        }


        public function send()
        {
            $response = $this->response;
            $content = $response->getContent();

            $request = Request::getInstance();
            if ($request->isAjax() || $request->headers['accept'] == ContentType::JSON->value) {
                $response->setContentType(ContentType::JSON->value);
                if ($content instanceof View) {
                    $this->render($content, $content->getData());
                    $content = $this->document->toArray();
                }
                $status =  $response->getCode() == Code::OK || $response->getCode() == Code::CREATED;
                $response->setContent([
                    "status" => $status,
                    "message" => $status ? "Fetch successfully" : "Failed fetch resource",
                    "data" => $content
                ]);
            } else {
                $response->setContentType(ContentType::HTML->value);
                $response->setContent(view("base"));
            }

            $content = $response->getContent(); // reasign content
            if ($content instanceof View)
                $this->response->setContent($this->render($content, $content->getData()));

            echo $this->response->send();
        }
    }
}

namespace {

    use MerapiPanel\System\Views\View;

    function view($file, $data = [])
    {

        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        if (isset($debug[1]['class'])) {
            $class = $debug[1]['class'];
            if (preg_match('/Media[\\\\\/]Modules[\\\\\/](\w+)/mi', $class, $matches)) {
                $module = $matches[1];
                $file = "@{$module}/{$file}";
            }
        }

        return new View($file, $data);
    }
}
