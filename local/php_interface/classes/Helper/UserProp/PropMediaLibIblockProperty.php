<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 17.10.2018
 * Time: 9:33
 */

namespace Helper\UserProp;


\Bitrix\Main\Loader::IncludeModule("fileman");
\CMedialib::Init();

class PropMediaLibIblockProperty
{
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "MediaLibIblockProperty",
            "DESCRIPTION" => "Привязка к медиабиблиотеке",
            "GetSettingsHTML" => array("PropMediaLibIblockProperty", "GetSettingsHTML"),
            "GetPropertyFieldHtml" => array("PropMediaLibIblockProperty", "GetPropertyFieldHtml"),
            "GetAdminListViewHTML" => array("PropMediaLibIblockProperty", "GetAdminListViewHTML"),
            "GetAdminFilterHTML" => array("PropMediaLibIblockProperty", "GetAdminFilterHTML"),
            "GetPublicViewHTML" => array("PropMediaLibIblockProperty", "GetPublicViewHTML"),
        );
    }

    public function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = array("HIDE" => array("ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE"));

        return '';
    }

    public function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE']) {
            \Bitrix\Main\Loader::IncludeModule("fileman");
            \CMedialib::Init();
            $arMediaLib = \CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $value['VALUE']
                )
            ));

            if (count($arMediaLib) > 0) {
                return $arMediaLib[0]['NAME'];
            } else return '&nbsp;';
        } else return '&nbsp;';
    }

    public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
    {
        $lAdmin = new \CAdminList($strHTMLControlName["TABLE_ID"]);
        $lAdmin->InitFilter(array($strHTMLControlName["VALUE"]));
        $filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

        if (isset($filterValue) && is_array($filterValue)) $values = $filterValue;
        else $values = array();

        if ($arProperty["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
        else $multiple = '';

        $html = "<select name='" . $strHTMLControlName['VALUE'] . "' " . $multiple . "><option value=''>Нет</option>";

        $arMediaLib = \CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib) {
            $html .= "<option " . ($mediaLib['ID'] == $filterValue["VALUE"] ? 'selected' : '') . " value='" . $mediaLib['ID'] . "'>[" . $mediaLib['ID'] . "] " . $mediaLib['NAME'] . "</option>";
        }

        $html .= "</select>";

        return $html;
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE']) {
            $arMediaLib = \CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $value['VALUE']
                )
            ));
            if (count($arMediaLib) > 0) {
                return $arMediaLib[0]['NAME'];
            } else return '&nbsp;';
        } else return '&nbsp;';
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        $return = "<select name='" . $strHTMLControlName['VALUE'] . "'><option value=''>Нет</option>";

        $arMediaLib = \CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib) {
            $return .= "<option " . ($mediaLib['ID'] == $value["VALUE"] ? 'selected' : '') . " value='" . $mediaLib['ID'] . "'>[" . $mediaLib['ID'] . "] " . $mediaLib['NAME'] . "</option>";
        }

        $return .= "</select>";

        return $return;
    }
}