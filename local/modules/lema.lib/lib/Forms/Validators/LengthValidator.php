<?php

namespace Lema\Forms\Validators;


/**
 * Class LengthValidator
 * @package Lema\Forms\Validators
 */
class LengthValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Поле {name} должно содержать от {min} до {max} символов';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        $this->checkValue = trim($this->checkValue);

        return !($this->hasError = (
            isset($this->additionalParams['min']) && mb_strlen($this->checkValue, 'UTF-8') < $this->additionalParams['min'] ||
            isset($this->additionalParams['max']) && mb_strlen($this->checkValue, 'UTF-8') > $this->additionalParams['max']
        ));
    }

    /**
     * @param string $value
     * @return void
     */
    public function setDataMin($value)
    {
        $this->additionalParams['min'] = $value;
    }
    /**
     * @param string $value
     * @return void
     */
    public function setDataMax($value)
    {
        $this->additionalParams['max'] = $value;
    }
}