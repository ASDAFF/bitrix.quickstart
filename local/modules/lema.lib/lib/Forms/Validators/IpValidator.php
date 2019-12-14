<?php

namespace Lema\Forms\Validators;


/**
 * Class IpValidator
 * @package Lema\Forms\Validators
 */
class IpValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Неверный формат IP-адреса';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !filter_var($this->checkValue, FILTER_VALIDATE_IP));
    }

}