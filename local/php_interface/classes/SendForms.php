<?php
/**
 * Copyright (c) 2018 Created by ASDAFF asdaff.asad@yandex.ru
 */

/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 21.11.2018
 * Time: 3:27
 */

class SendForms
{
    /**
     * @param $arItem
     * @param $LANG_CHARSET_BEGIN
     * @param $LANG_CHARSET_END
     *
     * ������� ��������� ���������
     */
    function convert_charset_array(&$arItem, $LANG_CHARSET_BEGIN, $LANG_CHARSET_END)
    {
        foreach ($arItem as &$value) {
            if (is_array($value))
                self::convert_charset_array($value, $LANG_CHARSET_BEGIN, $LANG_CHARSET_END);
            else $value = iconv($LANG_CHARSET_BEGIN, $LANG_CHARSET_END, urldecode($value));
        }
    }
}