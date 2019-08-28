<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Collected\Forms\Validators;


/**
 * Class RecaptchaValidator
 * @package Collected\Forms\Validators
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

        $reCaptcha = \Collected\Forms\ReCaptcha::get($this->additionalParams['sitekey']);
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