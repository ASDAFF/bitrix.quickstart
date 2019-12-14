<?php

namespace Lema\Forms;


/**
 * Class Form
 * @package Lema\Forms
 */
class Form
{
    /**
     * @var array
     */
    protected $fields = array();
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * Form constructor.
     *
     * @param array $fields
     * @param array $rules
     *
     * @access public
     */
    public function __construct(array $rules = array(), array $fields = array())
    {
        empty($fields) || $this->setFields($fields);
        empty($rules)  || $this->setRules($rules);
    }

    /**
     * Returns array of validation rules
     *
     * @return array
     *
     * @access public
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * Set validation rules
     *
     * @param array $rules
     * @return $this
     *
     * @access public
     */
    public function setRules(array $rules = array())
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Set fields data for check (e.g., POST-data)
     *
     * @param array $fields
     * @param bool $clean
     * @return $this
     *
     * @access public
     */
    public function setFields(array $fields = array(), $clean = true)
    {
        if($clean)
            $this->fields = $this->errors = array();

        foreach($fields as $name => $value)
            $this->fields[$name] = trim($value);
        return $this;
    }

    /**
     * Returns array of all fields
     *
     * @return array of attributes
     *
     * @access public
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns field value by name
     *
     * @param $name
     * @return string|null
     *
     * @access public
     */
    public function getField($name)
    {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    /**
     * Returns field value by name (in windows-1251 charset)
     *
     * @param $name
     * @return string|null
     *
     * @access public
     */
    public function getFieldCP1251($name)
    {
        return isset($this->fields[$name]) ? iconv('UTF-8', 'Windows-1251', $this->fields[$name]) : null;
    }
    /**
     * Check if form is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        $existsValidators = $this->validators();
        foreach($this->rules() as $rule)
        {
            if(!isset($rule[0], $rule[1], $existsValidators[$rule[1]]))
                continue;

            $additionalParams = isset($rule[2]) && is_array($rule[2]) ? $rule[2] : array();

            $fields = preg_split('~\\s*+,\\s*+~u', $rule[0], -1, PREG_SPLIT_NO_EMPTY);

            foreach($fields as $field)
            {
                $value = isset($this->fields[$field]) ? $this->fields[$field] : null;
                $this->checkValue($field, new $existsValidators[$rule[1]]($field, $value, $additionalParams));
            }
        }

        return empty($this->errors);
    }

    /**
     * Returns array of errors (errors of one field may be splitted by $splitSym)
     *
     * @param null $splitSym
     * @return array
     *
     * @access public
     */
    public function getErrors($splitSym = null)
    {
        if(isset($splitSym))
        {
            $errors = array();
            foreach($this->errors as $name => $value)
            {
                $errors[$name] = is_array($value) ? join($splitSym, $value) : $value;
            }
            return $errors;
        }
        return $this->errors;
    }

    /**
     * Returns array of errors (errors of one field may be splitted by $splitSym) in windows-1251 charset
     *
     * @param null $splitSym
     * @return array
     *
     * @access public
     */
    public function getErrorsCP1251($splitSym = null)
    {
        $errors = array();
        if(isset($splitSym))
        {
            foreach($this->errors as $name => $value)
                $errors[$name] = iconv('UTF-8', 'Windows-1251//IGNORE', (is_array($value) ? join($splitSym, $value) : $value));
        }
        else
        {
            foreach($this->errors as $name => $value)
                $errors[$name] = iconv('UTF-8', 'Windows-1251//IGNORE', $value);
        }

        return $errors;
    }

    /**
     * Check given value with given validator
     *
     * @param $name
     * @param Validators\Validator $validator
     *
     * @return bool
     *
     * @access protected
     */
    protected function checkValue($name, Validators\Validator $validator)
    {
        if($validator->validate())
            return true;

        $this->setError($name, $validator->getError(), $validator->showAllErrors());

        return false;
    }

    /**
     * Set error for given field
     *
     * @param $name
     * @param $error
     * @param bool $showAllErrors
     *
     * @return void
     *
     * @access protected
     */
    protected function setError($name, $error, $showAllErrors = false)
    {
        if(isset($this->errors[$name]))
        {
            if(!$showAllErrors)
                return ;
            if(is_array($this->errors[$name]))
                $this->errors[$name][] = $error;
            else
                $this->errors[$name] = array($this->errors[$name], $error);
        }
        else
            $this->errors[$name] = $error;
    }

    /**
     * Array of exists validators
     *
     * @TODO Make all validators
     * @return array
     *
     * @access public
     */
    public function validators()
    {
        return array(
            'boolean' => '\\Lema\\Forms\\Validators\\BooleanValidator',
            //'captcha' => '\\Lema\\Forms\\Validators\\CaptchaValidator',
            'recaptcha' => '\\Lema\\Forms\\Validators\\RecaptchaValidator',
            'email' => '\\Lema\\Forms\\Validators\\EmailValidator',
            //'date' => '\\Lema\\Forms\\Validators\\DateValidator',
            'ip' => '\\Lema\\Forms\\Validators\\IpValidator',
            'length' => '\\Lema\\Forms\\Validators\\LengthValidator',
            'regex' => '\\Lema\\Forms\\Validators\\RegexValidator',
            'numerical' => '\\Lema\\Forms\\Validators\\NumericValidator',
            'phone' => '\\Lema\\Forms\\Validators\\PhoneValidator',
            'required' => '\\Lema\\Forms\\Validators\\EmptyValidator',
            //'type' => '\\Lema\\Forms\\Validators\\TypeValidator',
            'url' => '\\Lema\\Forms\\Validators\\UrlValidator',
        );
    }
}