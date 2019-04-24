<?php

namespace Lema\Forms\Validators;


/**
 * Class BooleanValidator
 * @package Lema\Forms\Validators
 */
class BooleanValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Поле {name} должно быть булевым';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !filter_var($this->checkValue, FILTER_VALIDATE_BOOLEAN));
    }

}