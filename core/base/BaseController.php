<?php
/**
 * created by: Nikolay Tuzov
 */

namespace sk\core\base;


abstract class BaseController
{
    /**
     * Текущий маршрут
     * @var array
     */
    public $route = [];

    /**
     * Текущий вид
     * @var string
     */
    public $view;

    /**
     * Текущий шаблон
     * @var string
     */
    public $layout;

    /**
     * Пользовательские данные
     * @var array
     */
    public $data = [];

    /**
     * BaseController constructor.
     * @param array $route
     */
    public function __construct(array $route)
    {
        $this->route = $route;
        $this->view = $this->route['action'];
    }

    /**
     * Генерация представления
     */
    public function renderView()
    {
        $viewObject = new View($this->route, $this->view, $this->layout);
        $viewObject->render($this->data);
    }

    /**
     * Передача переменных в представление
     * @param $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function loadView($view, $data = [])
    {
        extract($data);
        require_once APP . "/views/{$this->route['controller']}/{$view}.php";
    }

    public function redirect($url = false)
    {
        $redirect = '';

        if ($url) {
            $redirect = $url;
        } else {
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/';

        }

        header("Location: $redirect");
        exit;
    }

}