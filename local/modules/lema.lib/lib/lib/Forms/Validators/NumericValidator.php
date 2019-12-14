<?php

namespace Lema\Forms\Validators;


/**
 * Class NumericValidator
 * @package Lema\Forms\Validators
 */
class NumericValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Поле {name} должно быть числом';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !filter_var($this->checkValue, FILTER_VALIDATE_INT));
    }
}