<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 17.10.2018
 * Time: 9:33
 */

\Bitrix\Main\Loader::IncludeModule("fileman");
CMedialib::Init();

class PropMediaLibIblockProperty
{
    function GetUserTypeDescription()
    {
        return Array(
            "PROPERTY_TYPE"			=> "S",
            "USER_TYPE"				=> "MediaLibIblockProperty",
            "DESCRIPTION"			=> "Привязка к медиабиблиотеке",
            "GetSettingsHTML"		=> Array("PropMediaLibIblockProperty", "GetSettingsHTML"),
            "GetPropertyFieldHtml"	=> Array("PropMediaLibIblockProperty", "GetPropertyFieldHtml"),
            "GetAdminListViewHTML"	=> Array("PropMediaLibIblockProperty", "GetAdminListViewHTML"),
            "GetAdminFilterHTML"	=> Array("PropMediaLibIblockProperty", "GetAdminFilterHTML"),
            "GetPublicViewHTML"		=> Array("PropMediaLibIblockProperty", "GetPublicViewHTML"),
        );
    }

    function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = Array("HIDE" => array("ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE"));

        return '';
    }

    function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE'])
        {
            \Bitrix\Main\Loader::IncludeModule("fileman");
            CMedialib::Init();
            $arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $value['VALUE']
                )
            ));

            if (count($arMediaLib) > 0)
            {
                return $arMediaLib[0]['NAME'];
            }
            else return '&nbsp;';
        }
        else return '&nbsp;';
    }

    function GetAdminFilterHTML($arProperty, $strHTMLControlName)
    {
        $lAdmin = new CAdminList($strHTMLControlName["TABLE_ID"]);
        $lAdmin->InitFilter(Array($strHTMLControlName["VALUE"]));
        $filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

        if (isset($filterValue) && is_array($filterValue)) $values = $filterValue;
        else $values = Array();

        if ($arProperty["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
        else $multiple = '';

        $html = "<select name='".$strHTMLControlName['VALUE']."' ".$multiple."><option value=''>Нет</option>";

        $arMediaLib = CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib)
        {
            $html .= "<option ".($mediaLib['ID'] == $filterValue["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
        }

        $html .= "</select>";

        return  $html;
    }

    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE'])
        {
            $arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $value['VALUE']
                )
            ));
            if (count($arMediaLib) > 0)
            {
                return $arMediaLib[0]['NAME'];
            }
            else return '&nbsp;';
        }
        else return '&nbsp;';
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        $return = "<select name='".$strHTMLControlName['VALUE']."'><option value=''>Нет</option>";

        $arMediaLib = CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib){
            $return .= "<option ".($mediaLib['ID'] == $value["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
        }

        $return .= "</select>";

        return $return;
    }
}