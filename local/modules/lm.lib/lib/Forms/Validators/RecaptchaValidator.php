<?php

namespace Lm\Forms\Validators;


/**
 * Class RecaptchaValidator
 * @package Lm\Forms\Validators
 */
class RecaptchaValidator extends Validator
{
    /**
     *
     */
    const DEFAULT_MESSAGE = 'Неверный проверочный код';

    /**
     * Check if field is valid
     *
     * @return bool
     *
     * @access public
     */
    public function validate()
    {
        if(empty($this->checkValue))
            return !($this->hasError = true);

        $reCaptcha = \Lm\Forms\ReCaptcha::get($this->additionalParams['sitekey']);
        $response = $reCaptcha->verifyResponse(
            $_SERVER['REMOTE_ADDR'],
            $this->checkValue
        );

        return !($this->hasError = (!$response || !$response->success));
    }
    /**
     * @param string $value
     * @return void
     */
    public function setDataSitekey($value)
    {
        $this->additionalParams['sitekey'] = $value;
    }
}