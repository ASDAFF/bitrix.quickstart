<?php

namespace Ml2WebForms;

/**
 * Class FeedbackWebForm
 * @package Ml2WebForms
 */
class FeedbackWebForm extends WebForm
{
    public function getId()
    {
        return 'feedback';
    }

    protected function useMl2WebFormsAntispam()
    {
        return true;
    }

    protected function getPostEventId()
    {
        return 'ML2WEBFORMS_FEEDBACK_WEBFORM_FILL';
    }

    protected function getPostEventTemplates()
    {
        return array();
    }

    public function getFields()
    {
        return array(
            'name' =>
                array(
                    'type' => WebForm::FIELD_TYPE_TEXT,
                    'value_type' => WebForm::FIELD_VALUE_TYPE_STRING,
                    'filterable' => true,
                    'required' => true,
                    'title' =>
                        array(
                            'ru' => 'Имя',
                            'en' => 'Name',
                        ),
                    'validators' =>
                        array(),
                    'errorText' =>
                        array(),
                ),
            'email' =>
                array(
                    'type' => WebForm::FIELD_TYPE_TEXT,
                    'required' => true,
                    'title' =>
                        array(
                            'ru' => 'E-mail',
                            'en' => 'E-mail',
                        ),
                    'validators' =>
                        array(
                            0 =>
                                array(
                                    0 => 2,
                                ),
                        ),
                    'errorText' =>
                        array(),
                    'value_type' => WebForm::FIELD_VALUE_TYPE_STRING,
                    'filterable' => true,
                ),
            'phone' =>
                array(
                    'type' => WebForm::FIELD_TYPE_TEXT,
                    'required' => false,
                    'title' =>
                        array(
                            'ru' => 'Телефон',
                            'en' => 'Phone',
                        ),
                    'validators' =>
                        array(
                            /*0 =>
                                array(
                                    0 => 1,
                                    1 => '8 \\(\\d{3}\\)\\d{3}-\\d{2}-\\d{2}',
                                ),*/
                        ),
                    'errorText' =>
                        array(),
                    'value_type' => WebForm::FIELD_VALUE_TYPE_STRING,
                    'filterable' => true,
                ),
            'attachment' =>
                array(
                    'type' => WebForm::FIELD_TYPE_FILE,
                    'required' => false,
                    'title' =>
                        array(
                            'ru' => 'Вложение',
                            'en' => 'Attachment',
                        ),
                    'validators' =>
                        array(),
                    'errorText' =>
                        array(),
                    'value_type' => WebForm::FIELD_VALUE_TYPE_TEXT,
                    'filterable' => true,
                ),
            'comment' =>
                array(
                    'type' => WebForm::FIELD_TYPE_TEXTAREA,
                    'required' => false,
                    'title' =>
                        array(
                            'ru' => 'Комментарий',
                            'en' => 'Comment',
                        ),
                    'validators' =>
                        array(),
                    'errorText' =>
                        array(),
                    'value_type' => WebForm::FIELD_VALUE_TYPE_TEXT,
                    'filterable' => true,
                ),
            'agree' =>
                array(
                    'type' => WebForm::FIELD_TYPE_CHECKBOX,
                    'required' => true,
                    'title' =>
                        array(
                            'ru' => 'Я принимаю условия использования',
                            'en' => 'I agree terms of use',
                        ),
                    'validators' =>
                        array(),
                    'errorText' =>
                        array(),
                    'value_type' => WebForm::FIELD_VALUE_TYPE_INTEGER,
                    'filterable' => true,
                ),
            'hobby' =>
                array(
                    'type' => WebForm::FIELD_TYPE_SELECT_MULTIPLE,
                    'value_type' => WebForm::FIELD_VALUE_TYPE_INTEGER,
                    'filterable' => true,
                    'required' => false,
                    'title' =>
                        array(
                            'ru' => 'Хобби',
                            'en' => 'Hobby',
                        ),
                    'list' =>
                        array(
                            1 =>
                                array(
                                    'title' =>
                                        array(
                                            'ru' => 'Спорт',
                                            'en' => 'Sport',
                                        ),
                                    'default' => true,
                                ),
                            2 =>
                                array(
                                    'title' =>
                                        array(
                                            'ru' => 'Чтение',
                                            'en' => 'Reading',
                                        ),
                                ),
                            3 =>
                                array(
                                    'title' =>
                                        array(
                                            'ru' => 'Спаньё',
                                            'en' => 'Sleeping',
                                        ),
                                ),
                        ),
                ),
        );
    }

    public function onBeforeResultAdd(array $fields)
    {
        return $fields;
    }

    public function onAfterResultAdd(array $fields)
    {
        return $fields;
    }
}
