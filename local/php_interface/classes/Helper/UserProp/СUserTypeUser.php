<?php
namespace Helper\UserProp;

use CIBlockPropertyUserID;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class СUserTypeUser
{
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "top10usertypeuser",
            "CLASS_NAME" => "TOP10_USERTYPE_USER",
            "DESCRIPTION" => GetMessage("PROP_NAME"),
            "BASE_TYPE" => "int",
        );
    }

    function GetDBColumnType($arUserField)
    {
        global $DB;

        switch (strtolower($DB->type)) {
            case "mysql":
                return "int(1)";
            case "oracle":
                return "number(1)";
            case "mssql":
                return "int";
        }
    }

    function PrepareSettings($arUserField)
    {
        return array();
    }

    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        return "";
    }

    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        $sField = FindUserID(
            $arUserField["FIELD_NAME"],        // Имя поля для ввода ID пользователя
            $arUserField["VALUE"],            // Значение поля для ввода ID пользователя
            "",                                // ID, логин, имя и фамилия пользователя, выводимые рядом с полем для ввода ID пользователя, сразу же после загрузки страницы
            "user_edit_form",                // Имя формы, в которой находится поле для ввода ID пользователя
            "5",                            // Ширина поля для ввода ID пользователя
            "",                                // Максимальное количество символов в поле для ввода ID пользователя
            " ... ",                        // Подпись на кнопке ведущей на страницу поиска пользователя
            "",                                // CSS класс для поля ввода ID пользователя
            ""                                // CSS класс для кнопки ведущей на страницу поиска пользователя
        );

        return $sField;
    }

    function GetFilterHTML($arUserField, $arHtmlControl)
    {
        return '';
    }

    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        preg_match("/FIELDS\[([0-9]+)\]/", $arHtmlControl["NAME"], $a);

        if ($a[1] > 0) {
            require_once $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/tools/prop_userid.php';

            return CIBlockPropertyUserID::GetAdminListViewHTML(array(), $arHtmlControl, "");
        }

        return "&nbsp;";
    }

    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        $sField = FindUserID(
            $arHtmlControl["NAME"],            // Имя поля для ввода ID пользователя
            $arHtmlControl["VALUE"],        // Значение поля для ввода ID пользователя
            "",                                // ID, логин, имя и фамилия пользователя, выводимые рядом с полем для ввода ID пользователя, сразу же после загрузки страницы
            "form_tbl_user",                // Имя формы, в которой находится поле для ввода ID пользователя
            "5",                            // Ширина поля для ввода ID пользователя
            "",                                // Максимальное количество символов в поле для ввода ID пользователя
            " ... ",                        // Подпись на кнопке ведущей на страницу поиска пользователя
            "",                                // CSS класс для поля ввода ID пользователя
            ""                                // CSS класс для кнопки ведущей на страницу поиска пользователя
        );

        return $sField;
    }

    function CheckFields($arUserField, $value)
    {
        $aMsg = array();

        return $aMsg;
    }

    function OnSearchIndex($arUserField)
    {
        return "";
    }
}