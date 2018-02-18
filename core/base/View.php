<?php
/**
 * Created by Nikolay Tuzov
 */

namespace adeka\core\base;

class View
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

    public $scripts = [];

    public static $meta = [
        'title' => '',
        'keywords' => '',
        'description' => ''
    ];

    /**
     * View constructor.
     * @param array $route
     * @param string $view
     * @param string $layout
     */
    public function __construct(array $route, $view = '', $layout = '')
    {
        $this->route = $route;
        $this->view = $view;

        if ($layout === false) {
            $this->layout = false;
        } else {
            $this->layout = $layout ?: LAYOUT;
        }
    }

    public function render($data)
    {
        if (is_array($data)) extract($data);

        if ($prefix = $this->route['prefix']) {
            $prefix .= DIRECTORY_SEPARATOR;
        }
        $fileView = APP . "/views/{$prefix}{$this->route['controller']}/{$this->view}.php";
        ob_start();
        if (is_file($fileView)) {
            require $fileView;
        } else {
//            echo "<p>View <strong>$fileView</strong> not found </p>";
            throw new \Exception("View $fileView not found");
        }
        $content = ob_get_clean();

        if ($this->layout !== false) {
            $fileLayout = APP . "/views/layouts/{$this->layout}.php";
            if (is_file($fileLayout)) {
                $content = $this->cutScripts($content);

//                DebugHelper::debug($this->scripts);
                require $fileLayout;
            } else {
                throw new \Exception("Layout $fileView not found");
            }
        }
    }

    protected function cutScripts($content)
    {
        $pattern = "#<script.*?>.*?</script>#si";
        preg_match_all($pattern, $content, $scripts);


        if (!empty($scripts)) {
            $this->scripts = $scripts[0];
            $content = preg_replace($pattern, '', $content);
        }

        return $content;
    }

    public static function getMeta()
    {
        $meta = "<title>" . self::$meta['title'] . "</title>\n";
        $meta .= "<meta name='description' content='" . self::$meta['description'] . "'>\n";
        $meta .= "<meta name='keywords' content='" . self::$meta['keywords'] . "'>\n";

        return $meta;
    }

    public static function setMeta(array $meta)
    {
        self::$meta['title'] = $meta['title'] ?: '';
        self::$meta['keywords'] = $meta['keywords'] ?? '';
        self::$meta['description'] = $meta['description'] ?? '';
    }

}