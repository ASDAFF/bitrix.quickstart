<?php
namespace Cpeople\Classes\Forms;

define('VALIDATOR_ERROR_EMPTY',         1);
define('VALIDATOR_ERROR_EMAIL',         2);
define('VALIDATOR_ERROR_EQUAL_TO',      3);
define('VALIDATOR_ERROR_MINLENGTH',     4);
define('VALIDATOR_ERROR_MAXLENGTH',     5);
define('VALIDATOR_ERROR_RANGELENGTH',   6);
define('VALIDATOR_ERROR_MIN',           7);
define('VALIDATOR_ERROR_MAX',           8);
define('VALIDATOR_ERROR_RANGE',         9);
define('VALIDATOR_ERROR_URL',           10);
define('VALIDATOR_ERROR_INT',           11);
define('VALIDATOR_ERROR_FLOAT',         12);
define('VALIDATOR_ERROR_DIGITS',        13);
define('FILTER_VALIDATE_ACCEPT',        14);
define('VALIDATOR_ERROR_PHONE',         15);

class Validator
{
    public $rules = array();
    public $data;

    public $errors;

    function __construct($data = array())
    {
        $this->setData($data);
    }

    function setData($data)
    {
        $this->data = array_map('trim', $data);
    }

    function setRules($rules)
    {
        $this->rules = $rules;
    }

    function validate($rules = array())
    {
        if (!empty($rules))
        {
            $this->setRules($rules);
        }

        $retval = array();

        $errors = array();

        foreach ($this->rules as $key => $rule_data)
        {
            $res = $this->validate_string(@$this->data[$key], $rule_data, $key);

            if ($res > 0)
            {
                $errors[$key] = $res;
            }
        }

        $this->errors = $errors;

        return empty($errors);
    }

    public function validate_string($value, $rules, $key = null)
    {
        $res = 0;

        foreach ($rules as $k => $v)
        {
            if (!$v) continue;

            $method = "__validate_$k";

            if (!method_exists($this, $method)) continue;

            if (($res = $this->$method($value, $v, $key)) !== 0)
            {
                break;
            }
        }

        return $res;
    }

    public function __validate_required($value, $rule_data)
    {
        if (is_array($rule_data)) return 0;
        return !empty($value) ? 0 : VALIDATOR_ERROR_EMPTY;
    }

    public function __validate_email($value, $rule_data)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? 0 : VALIDATOR_ERROR_EMAIL;
    }

    public function __validate_equalTo($value, $rule_data)
    {
        return @$this->data[$rule_data] == $value ? 0 : VALIDATOR_ERROR_EQUAL_TO;
    }

    public function __validate_minlength($value, $rule_data)
    {
        return strlen($value) >= $rule_data ? 0 : VALIDATOR_ERROR_MINLENGTH;
    }

    public function __validate_maxlength($value, $rule_data)
    {
        return strlen($value) <= $rule_data ? 0 : VALIDATOR_ERROR_MAXLENGTH;
    }

    public function __validate_rangelength($value, $rule_data)
    {
        return (strlen($value) >= $rule_data[0] && strlen($value) <= $rule_data[1]) ? 0 : VALIDATOR_ERROR_RANGELENGTH;
    }

    public function __validate_min($value, $rule_data)
    {
        return ((int) $value >= $rule_data) ? 0 : VALIDATOR_ERROR_MIN;
    }

    public function __validate_max($value, $rule_data)
    {
        return ((int) $value <= $rule_data) ? 0 : VALIDATOR_ERROR_MAX;
    }

    public function __validate_range($value, $rule_data)
    {
        return ((int) $value >= $rule_data[0] && (int) $value <= $rule_data[1]) ? 0 : VALIDATOR_ERROR_RANGE;
    }

    public function __validate_url($value, $rule_data)
    {
        return filter_var($value, FILTER_VALIDATE_URL) ? 0 : VALIDATOR_ERROR_URL;
    }

    /**
     * TODO
     */

    public function __validate_date($value, $rule_data)
    {
        return 0;
    }

    /**
     * TODO
     */

    public function __validate_dateISO($value, $rule_data)
    {
        return 0;
    }

    public function __validate_number($value, $rule_data)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) ? 0 : VALIDATOR_ERROR_FLOAT;
    }

    public function __validate_digits($value, $rule_data)
    {
        return preg_match('/^\d/', $value) ? 0 : VALIDATOR_ERROR_DIGITS;
    }

    public function __validate_accept($value, $rule_data)
    {
        $path       = array_map('strtolower', pathinfo($value));
        $allowed    = array_map('strtolower', explode('|', $rule_data));
        return @in_array($path['extension'], $allowed) ? 0 : FILTER_VALIDATE_ACCEPT;
    }

    public function __validate_phone($value, $rule_data)
    {
        $pattern = (is_array($rule_data) && isset($rule_data['pattern'])) ? $rule_data['pattern'] : '/^\+?[0-9]\ ?\(?[0-9]{3}\)?\ ?[0-9]{3}[- ]?[0-9]{2}[- ]?[0-9]{2}$/';

        if(preg_match($pattern, $value))
        {
            return 0;
        }
        else
        {
            return VALIDATOR_ERROR_PHONE;
        }
    }
}
