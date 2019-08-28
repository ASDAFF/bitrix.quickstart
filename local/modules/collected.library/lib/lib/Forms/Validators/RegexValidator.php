<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Forms\Validators;


/**
 * Class RegexValidator
 * @package Collected\Forms\Validators
 */
class RegexValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Неверный формат {name}';

    /**
     * Check if field is valid by regex pattern (PCRE format)
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !preg_match($this->additionalParams['pattern'], $this->checkValue));
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setDataPattern($value)
    {
        $this->additionalParams['pattern'] = $value;
    }
}