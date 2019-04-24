<?php

namespace Lema\Forms\Validators;


/**
 * Class Validator
 * @package Lema\Forms\Validators
 */
abstract class Validator
{
    /**
     * @var string check field name
     */
    protected $checkField = null;
    /**
     * @var string check field value
     */
    protected $checkValue = null;
    /**
     * @var string error message text
     */
    protected $errorMessage = null;

    /**
     * @var array of additional params ( e.g. ['length' => ['min' => 3, 'max' => 10]] )
     */
    protected $additionalParams = array();

    /**
     * @var bool has error ?
     */
    protected $hasError = false;
    /**
     * @var string return error text
     */
    protected $error = null;

    /**
     * @var bool show all errors ( e.g. empty + wrong format or empty only )
     */
    protected $showAllErrors = false;

    /**
     * @const string default error message
     */
    const DEFAULT_MESSAGE = '';

    /**
     * Validator constructor.
     *
     * @param string $name - field name
     * @param string $value - field value
     * @param array $additional - array of additional params ( e.g. ['length' => ['min' => 3, 'max' => 10]] )
     *
     * @access public
     */
    public function __construct($name, $value, array $additional = array())
    {
        $this->checkField = $name;
        $this->checkValue = $value;

        isset($additional) && $this->setAdditionalParams($additional);

        $this->setErrorMessage();

        $this->validate();
    }

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    abstract function validate();

    /**
     * Get validator error
     *
     * @return null|string
     *
     * @access public
     */
    public function getError()
    {
        $this->setError();
        return $this->error;
    }

    /**
     * @return bool
     *
     * @access public
     */
    public function showAllErrors()
    {
        return $this->showAllErrors;
    }


    /**
     * Set additional params for validation
     *
     * @param array $additional - array of additional params ( e.g. ['length' => ['min' => 3, 'max' => 10]] )
     *
     * @return void
     *
     * @access protected
     */
    protected function setAdditionalParams(array $additional=array())
    {
        foreach($additional as $name => $value)
        {
            if(method_exists($this, 'setData' . $name))
                $this->{'setData' . $name}($value);
        }
    }

    /**
     * Set error message
     *
     * @param string $errorMessage
     *
     * @return void
     *
     * @access protected
     */
    protected function setErrorMessage($errorMessage = null)
    {
        $replace = array(
            '{name}' => $this->checkField
        );

        $message = $this->errorMessage;
        if(empty($errorMessage) && empty($message))
            $message = strtr(static::DEFAULT_MESSAGE, $replace);

        if(is_array($errorMessage) && !empty($errorMessage[0]))
        {
            $message = $errorMessage[0];
            if(isset($errorMessage[1]) && is_array($errorMessage[1]))
            {
                foreach($errorMessage[1] as $name => $value)
                    $replace['{' . $name . '}'] = $value;
            }
        }

        if(!empty($this->additionalParams))
        {
            foreach($this->additionalParams as $name => $value)
                $replace['{' . $name . '}'] = $value;
        }

        $this->errorMessage = strtr((empty($this->errorMessage) ? $message : $this->errorMessage), $replace);
    }

    /**
     * Set error message text
     *
     * @param $message
     *
     * @return void
     *
     * @access protected
     */
    protected function setDataMessage($message)
    {
        $this->errorMessage = $message;
    }

    /**
     * Set error
     *
     * @return void
     *
     * @access protected
     */
    protected function setError()
    {
        $this->error = empty($this->errorMessage) ? static::DEFAULT_MESSAGE : $this->errorMessage;
    }

    /**
     * @param bool $showAll
     *
     * @return void
     *
     * @access protected
     */
    protected function setDataShowAllErrors($showAll)
    {
        $this->showAllErrors = (bool) $showAll;
    }
}
