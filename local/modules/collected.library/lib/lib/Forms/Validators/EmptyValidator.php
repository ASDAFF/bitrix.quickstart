<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Forms\Validators;


/**
 * Class EmptyValidator
 * @package Collected\Forms\Validators
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