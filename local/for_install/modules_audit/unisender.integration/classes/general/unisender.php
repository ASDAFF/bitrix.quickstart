<?php

IncludeModuleLangFile(__FILE__);

class Unisender
{
    public static function getUserFields()
    {
        $permittedTypes = array(
            'string', 'integer', 'double', 'datetime', 'date', 'boolean', 'string_formatted');

        $fields = array(
            'EMAIL' => array(
                'type' => 'string',
                'title' => GetMessage('EMAIL')),
            'PERSONAL_PHONE' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_PHONE')),
            'LOGIN' => array(
                'type' => 'string',
                'title' => GetMessage('LOGIN')),
            'NAME' => array(
                'type' => 'string',
                'title' => GetMessage('NAME')),
            'SECOND_NAME' => array(
                'type' => 'string',
                'title' => GetMessage('SECOND_NAME')),
            'LAST_NAME' => array(
                'type' => 'string',
                'title' => GetMessage('LAST_NAME')),
            'PERSONAL_GENDER' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_GENDER')),
            'PERSONAL_BIRTHDAY' => array(
                'type' => 'date',
                'title' => GetMessage('PERSONAL_BIRTHDAY')),
            'PERSONAL_MOBILE' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_MOBILE')),
            'PERSONAL_CITY' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_CITY')),
            'PERSONAL_STREET' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_STREET')),
            'PERSONAL_COUNTRY' => array(
                'type' => 'string',
                'title' => GetMessage('PERSONAL_COUNTRY'))
        );

        $userTypes = CUserTypeEntity::GetList(array(), array('SHOW_IN_LIST' => 'Y'));
        foreach ($userTypes->arResult as $type) {
            if (!in_array($type['USER_TYPE_ID'], $permittedTypes)) {
                continue;
            }
            $fields[$type['FIELD_NAME']] = array(
                'type' => $type['USER_TYPE_ID'],
                'title' => $type['FIELD_NAME']
            );
        }

        foreach ($fields as $name => $field) {
            switch ($field['type']) {
                case 'string':
                case 'string_formatted':
                    $fields[$name]['type'] = 'string';
                    break;
                case 'double':
                case 'integer':
                    $fields[$name]['type'] = 'number';
                    break;
                case 'datetime':
                case 'date':
                    $fields[$name]['type'] = 'date';
                    break;
                case 'boolean':
                    $fields[$name]['type'] = 'bool';
                    break;
            }
        }

        return $fields;
    }

}

?>
