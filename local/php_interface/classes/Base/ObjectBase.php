<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 17.03.14
 * Time: 9:47
 */

namespace Base;


class ObjectBase
{
    protected $data;

    public function __construct($data = array())
    {
        if (!is_array($data))
        {
            throw new \Exception('Argument should be an array to ' . __METHOD__);
        }

        $this->data = $data;
    }

    public function __get($name)
    {
        if (isset($this->data[strtoupper($name)]))
        {
            return $this->data[strtoupper($name)];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property in __get(): ' . $name .
            ' in file ' . $trace[0]['file'] .
            ' line ' . $trace[0]['line'], E_USER_NOTICE
        );
    }

    public function unescape($name)
    {
        return html_entity_decode($this->{$name});
    }

    public function escape($name)
    {
        return $this->escaped($name);
    }

    public function escaped($name)
    {
        return htmlspecialchars($this->{$name});
    }

    public function getData()
    {
        return $this->data;
    }
} 