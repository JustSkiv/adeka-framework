<?php
/**
 * created by: Nikolay Tuzov
 */

namespace adeka\core;


trait TSingletone
{
    protected static $instance;

    /**
     * Получение ссылки на подключение к БД
     * @return Object
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}