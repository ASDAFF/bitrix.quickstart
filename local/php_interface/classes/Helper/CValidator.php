<?php
/**
 * Copyright (c) 9/2/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper;


class CValidator
{
    /**
     * Input fields array
     *
     * @var array $fields
     */
    public $fields = array();

    /**
     * Array of validation rules
     *
     * @var array $rules
     */
    public $rules = array();

    /**
     * Set fields
     * @param array $arFields
     */
    public function setFields($arFields)
    {
        $this->fields = $arFields;
    }

    /**
     * Set validation rules
     * @param array $arRules
     */
    public function setRules($arRules)
    {
        $this->rules = $arRules;
    }

    /**
     * Returns array of validation exams
     *
     * @return array $arAnswer
     */
    public function validate()
    {
        foreach ($this->rules as $fieldName => $fieldRules) {
            $fieldValue = $this->fields[$fieldName];
            foreach ($fieldRules as $validatorName => $validator) {
                $arAnswer[$fieldName][$validatorName] = $this->$validatorName($fieldValue, $validator);
            }
        }
        $arAnswer = $this->answer($arAnswer);

        return $arAnswer;
    }

    /**
     * Returns array of display validation errors
     *
     * @param array $arFields
     * @return array $displayErrors
     */
    public function answer($arFields)
    {
        foreach ($arFields as $fieldName => $field) {
            $displayErrors[$fieldName] = $this->getFirstError($field);
        }
        return $displayErrors;
    }

    /**
     * Returns array of first validation errors
     *
     * @param string $field
     * @return boolean
     */
    public function getFirstError($field)
    {
        foreach ($field as $error) {
            if ($error) {
                return $error;
            }
        }
        return false;
    }

    /**
     * Validation rule for required fields
     *
     * @param string $name
     * @param $val
     * @return string|boolean
     */
    public function required($val, $options)
    {
        $defaultMessage = "Это поле обязательно к заполнению";

        if (!strval($val) || strval($val) == '') {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Validation rule for required fields
     *
     * @param string $name
     * @param $val
     * @return string|boolean
     */
    public function checkSessId($val, $options)
    {
        $defaultMessage = "Идентификатор сессии неверный";

        if (!check_bitrix_sessid()) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Validation rule for minimum string length
     *
     * @param string $name
     * @param $val
     * @param int $min
     * @return boolean
     */
    public function length($val, $options)
    {
        if (!$val) return false;
        $defaultMessage = "Длина текста должна быть от 3 до 50 символов";

        if (mb_strlen(strval($val)) < $options["min"] || mb_strlen(strval($val)) > $options["max"]) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Validation rule for email fields
     *
     * @param string $name
     * @param $val
     * @return boolean
     */
    public function email($val, $options)
    {
        if (!$val) return false;

        $defaultMessage = "Вы указали некорректный e-mail адрес";

        if (!preg_match("/^[a-zA-Z0-9]+(([a-zA-Z0-9_.-]+)?)@[a-zA-Z0-9+](([a-zA-Z0-9_.-]+)?)+\.+[a-zA-Z]{2,4}$/", $val)) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Validation rule for numeric fields
     *
     * @param string|integer $name
     * @param $val
     * @return boolean
     */
    public function numeric($val, $options)
    {
        if (!$val) return false;

        $defaultMessage = "В это поле можно вводить только цифры";
        if (!is_numeric($val)) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Check email exists in the system
     *
     * @param  [string] $val
     * @param  [array] $options
     * @return [string]
     */
    private function emailExists($val, $options)
    {
        if (!$val) return false;

        $errorMessage = "Пользователь с таким e-mail не зарегистрирован на сайте";
        if (!UserHelper::CheckExistsByEmail($val)) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }

    /**
     * Check email is free
     *
     * @param  [string] $val
     * @param  [array] $options
     * @return [string]
     */
    private function emailIsFree($val, $options)
    {
        global $USER;
        if (!$val || $USER->isAuthorized()) return false;

        $errorMessage = "Пользователь с таким e-mail уже зарегистрирован на сайте";
        if (UserHelper::CheckExistsByEmail($val)) {
            return ($options["message"]) ? $options["message"] : $defaultMessage;
        }

        return false;
    }
}