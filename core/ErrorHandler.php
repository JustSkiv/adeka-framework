<?php
/**
 * created by: Nikolay Tuzov
 */

namespace adeka\core;


class ErrorHandler
{
    public function __construct()
    {
        if (DEBUG) {
            error_reporting(-1);
        } else {
            error_reporting(0);
        }

        set_error_handler([$this, "errorHandler"]);

        ob_start();
        register_shutdown_function([$this, "fatalErrorHandler"]);

        set_exception_handler([$this, "exceptionHandler"]);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->logError($errstr, $errfile, $errline);

        if (DEBUG || in_array($errno, [E_USER_ERROR, E_RECOVERABLE_ERROR])) {
            $this->displayError($errno, $errstr, $errfile, $errline);
        }

        return true;
    }

    public function fatalErrorHandler()
    {
        $error = error_get_last();

        if (!empty($error) && $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_end_clean();
            $this->displayError($error['type'], $error['message'], $error['file'], $error['line']);
            $this->logError($error['message'], $error['file'], $error['line']);
        } else {
            ob_end_flush();
        }
    }

    public function exceptionHandler($e)
    {
        $this->logError($e->getMessage(), $e->getFile(), $e->getLine());
        $this->displayError('Исключение', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    }

    protected function displayError($errno, $errstr, $errfile, $errline, $response = 500)
    {
        $response = $response ? $response : 500;
        http_response_code($response);

        if ($response == 404 && !DEBUG) {
            require_once WWW . '/errors/404.php';
            return;
        }

        if (DEBUG) {
            require_once WWW . '/errors/dev.php';
        } else {
            require_once WWW . '/errors/prod.php';
        }
        die();
    }

    protected function logError($message = '', $file = '', $line = '')
    {
        $date = date('Y-m-d H:i:s');
        error_log("[$date] err_txt: {$message} | file: {$file} | 
                            line: {$line} \n", 3, ROOT . '/temp/log/errors.log');
    }
}