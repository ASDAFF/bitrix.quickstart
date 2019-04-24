<?php

namespace Lema\Forms\Validators;


/**
 * Class PhoneValidator
 * @package Lema\Forms\Validators
 */
class PhoneValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Неверный формат телефона';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        return !($this->hasError = !preg_match(
            '~^\\s*?(?:(?:\\+?\\s*?7)|(?:\\s*?8\\s*?))?(?:\\s*?-?\\s*?)?(?:\\(\\d{3}\\)|\\d{3})(?:\\s*?-?\\s*?)?\\d{3}(?:\\s*?-?\\s*?)?\\d{2}(?:\\s*?-?\\s*?)?\\d{2}$~',
            $this->checkValue
        ));
    }
}