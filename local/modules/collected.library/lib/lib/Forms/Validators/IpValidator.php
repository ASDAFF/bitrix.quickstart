<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Forms\Validators;


/**
 * Class IpValidator
 * @package Collected\Forms\Validators
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