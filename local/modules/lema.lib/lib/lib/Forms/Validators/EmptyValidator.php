<?php

namespace Lema\Forms\Validators;


/**
 * Class EmptyValidator
 * @package Lema\Forms\Validators
 */
class EmptyValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Введите {name}';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = trim($this->checkValue) === '');
    }
}