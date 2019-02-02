<?php

namespace Cpeople\Classes\Forms;

abstract class Form
{
    public $fields;
    public $data;
    public $validation_rules;
    public $errors;

    abstract public function process();

    public function __construct($fields = array(), $data = array())
    {
        $this->setFields($fields);
        $this->setData($data);
    }

    public function setFields($fields)
    {
        $this->fields = $fields;

        foreach ($fields as $k => $v)
        {
            if (!empty($v['rules']))
            {
                $this->validation_rules[$k] = $v['rules'];
            }
        }
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setErrors(Array $errors)
    {
        foreach($errors as $key => $error)
        {
            $this->errors[$key] = $error;
        }
    }

    public function setDataItem($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function setRules($rules)
    {
        $this->validation_rules = $rules;
    }

    public function getRules()
    {
        return $this->validation_rules;
    }

    function getValue($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }

    public function getData()
    {
        $retval = $this->data;

        if (!empty_array($_FILES))
        {
            foreach ($_FILES as $k => $data)
            {
                $retval[$k] = $data['name'];
            }
        }

        return $retval;
    }

    public function getDataItem($key)
    {
        $data = $this->getData();
        return isset($data[$key]) ? $data[$key] : FALSE;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function isValid()
    {
        $validator = new Validator($this->getData());

        $check = $validator->validate($this->getRules());

        if (!$check)
        {
            $this->setErrors($validator->errors);
        }

        return $check;
    }

    /**
     * @param $command1 Command
     * @param $command2 Command
     * <br><b>...</b>
     * @param $commandN Command
     */
    public function handleSubmit()
    {
        $args = func_get_args();
        for($i = 0; $i < count($args); $i++)
        {
            if($args[$i] instanceof Command)
            {
                $args[$i]->execute($this);
            }
        }
    }
}
