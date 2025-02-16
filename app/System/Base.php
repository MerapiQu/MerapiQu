<?php

namespace App\System {

    use App\System\Exceptions\ErrorWrapper;
    use App\WebService\Error\ErrorWebService;
    use App\WebService\WebService;
    use Il4mb\Routing\Http\Response;

    class Base
    {

        private $errors = [];
        private WebService $errorWebService;

        public function __construct()
        {

            $this->errorWebService = new ErrorWebService();

            set_error_handler([$this, "__errorHandler"]);
            register_shutdown_function([$this, "__onProgramFinish"]);
            set_exception_handler([$this, "__exceptionHandler"]);
        }

        public function __onProgramFinish()
        {
            if ($this->hasErrors()) {
                if (ob_get_level() > 0) ob_clean();
                return $this->render($this->getLastedError());
            }
        }

        public function __errorHandler($errno, $errstr, $errfile, $errline)
        {
            $this->errors[] = ErrorWrapper::create(
                "UnhandledException",
                "Caught error in $errfile at line $errline: $errstr",
                $errfile,
                $errline
            );
            return true;
        }

        public function __exceptionHandler($exception)
        {
            $this->errors[] = new ErrorWrapper($exception);
        }

        public function getLastedError()
        {
            return $this->errors[0];
        }

        public function hasErrors()
        {
            return !empty($this->errors);
        }

        function render(ErrorWrapper $t)
        {
            $code    = $t->getCode();
            $message = $t->getMessage();

            // $this->response->setCode(HTTP_CODE::fromCode($code) ?? HTTP_CODE::INTERNAL_SERVER_ERROR);
            // $this->response->setContent([
            //     "code"    => $code,
            //     "message" => $message,
            //     "snippet" => $t->getSnippet(),
            //     "title"   => $t->getTitle(),
            //     "file"    => $t->getFile(),
            //     "line"    => $t->getLine(),
            //     "stackTrace" => $t->getStackTrace()
            // ]);
            // $finalResponse = $this->errorWebService->dispath($this->response);
            // echo $finalResponse->send();
            exit();
        }
    }
}

namespace {

    use App\System\Views\View;

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
