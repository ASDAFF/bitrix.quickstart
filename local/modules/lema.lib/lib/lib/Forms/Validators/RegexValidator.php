<?php

namespace Lema\Forms\Validators;


/**
 * Class RegexValidator
 * @package Lema\Forms\Validators
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