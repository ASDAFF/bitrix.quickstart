<?php

namespace Cpeople\Classes\Infoblock;

class Field {

    protected $data;

    public function __construct($key, $data = array())
    {
        $data['CODE'] = $key;

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
}
