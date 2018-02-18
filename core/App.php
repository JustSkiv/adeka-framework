<?php
/**
 * created by: Nikolay Tuzov
 */

namespace sk\core;


class App
{
    public static $app;

    /**
     * App constructor.
     */
    public function __construct()
    {
        self::$app = Registry::instance();
//        new ErrorHandler();
    }


}