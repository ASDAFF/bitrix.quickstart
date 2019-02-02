<?php

namespace Cpeople\Classes\Infoblock;

class Property extends \Cpeople\Classes\Base\Object
{
    protected $data;
    protected $options;

    public function getOptions()
    {
        if (!isset($this->options))
        {
            $res = \CIBlockProperty::GetPropertyEnum(
                $this->data['ID'],
                Array("SORT"=>"asc"),
                Array()
            );

            while ($row = $res->Fetch())
            {
                $this->options[] = $row;
            }
        }

        return $this->options;
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
