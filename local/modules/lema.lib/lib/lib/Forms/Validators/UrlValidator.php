<?php

namespace Lema\Forms\Validators;


/**
 * Class UrlValidator
 * @package Lema\Forms\Validators
 */
class UrlValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Неверный формат URL';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !filter_var($this->checkValue, FILTER_VALIDATE_URL));
    }
}