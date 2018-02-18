<?php
/**
 * created by: Nikolay Tuzov
 */

namespace adeka\core\base;

use adeka\core\Db;
use Valitron\Validator;

abstract class BaseModel
{
    const LOAD_OPTION_HTMLSPECIALCHARS = 1;
//    protected $pdo;
    protected static $table;
    protected $pk = 'id';

    public $attributes = [];
    public $errors = [];
    public $rules = [];

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->db = Db::instance();
    }

    public function __get($field)
    {
        return $this->attributes[$field];
    }

    /**
     * find all records in model's table
     * TODO: must return instanses of BaseModel
     *
     * @return array
     */
    public static function findAll()
    {
        return \R::findAll(static::$table);
    }

    /**
     * find one record in model's table
     * TODO: must return instanse of BaseModel
     */
    public static function findOne($filter, $params)
    {
        return \R::findOne(static::$table);
    }


    /**
     * Load data to model
     *
     * @param $data
     * @param $options
     */
    public function load($data, $options)
    {
        $doHtmlspecialchars = false;
        if (!isset($options['type'])) {
            $doHtmlspecialchars = $options['type'] == self::LOAD_OPTION_HTMLSPECIALCHARS;
        }

        foreach ($data as $name => $value) {
            if (isset($this->attributes[$name])) {
                if($doHtmlspecialchars){
                    $this->attributes[$name] = htmlspecialchars($data[$name]);
                } else{
                    $this->attributes[$name] = $data[$name];
                }
            }
        }
    }

    /**
     * save model to DB
     * @return int|string
     */
    public function save()
    {
        $tbl = \R::dispense(static::$table);
        foreach ($this->attributes as $name => $value) {
            $tbl[$name] = $value;
        }

        return \R::store($tbl);
    }

    /**
     * Validate data
     *
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        $res = false;

        // TODO: get from config
        //Validator::lang('ru');
        $v = new Validator($data);
        $v->rules($this->rules);

        if ($v->validate()) {
            $res = true;
        } else {
            $this->errors = array_merge_recursive($this->errors, $v->errors());
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * returns formatted errors (html)
     *
     * @param bool $field
     * @return string
     */
    public function showErrors($field = false)
    {
        if (empty($this->errors) || ($field && empty($this->errors[$field]))) {
            return '';
        }

        $res = '<div class="alert alert-danger"><ul>';

        if ($field) {
            foreach ($this->errors[$field] as $error) {
                $res .= "<li>$error</li>";
            }
        } else {
            foreach ($this->errors as $field) {
                foreach ($field as $error) {
                    $res .= "<li>$error</li>";
                }
            }
        }

        $res .= '</ul></div>';

        return $res;
    }

    //Этот код был написан до подключения RedBean

//    /**
//     * Выполнение указанного SQL-запроса
//     * @param $sql
//     * @return bool
//     */
//    public function query($sql)
//    {
//        return $this->pdo->execute($sql);
//    }
//
//    /**
//     * Получение всех записей таблицы
//     * @return array
//     */
//    public function findAll()
//    {
//        $sql = "SELECT * FROM {$this->table}";
//
//        return $this->pdo->query($sql);
//    }
//
//    /**
//     * Получение одной записи по знанию поля
//     * @param $value
//     * @param string $field
//     * @return array
//     */
//    public function findOne($value, $field = '')
//    {
//        $field = $field ?: $this->pk;
//        $sql = "SELECT * FROM {$this->table} WHERE $field = ? LIMIT 1";
//
//        return $this->pdo->query($sql, [$value]);
//    }
//
//    public function findBySql($sql, $params = [])
//    {
//        return $this->pdo->query($sql, $params);
//    }
//
//    public function findLike($str, $field, $table = '')
//    {
//        $table = $table ?: $this->table;
//        $sql = "SELECT * FROM $table WHERE $field LIKE ?";
//
//        return $this->pdo->query($sql, ['%' . $str . '%']);
//    }


}