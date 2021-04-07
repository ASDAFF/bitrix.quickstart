<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 17.10.2018
 * Time: 9:30
 */

\Bitrix\Main\Loader::IncludeModule("fileman");
CMedialib::Init();

class PropMediaLibUserType
{
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID"	=> "medialib",
            "CLASS_NAME"	=> "PropMediaLibUserType",
            "DESCRIPTION"	=> "Привязка к медиабиблиотеке",
            "BASE_TYPE"		=> "string",
        );
    }

    function OnSearchIndex($arUserField)
    {
        if (is_array($arUserField['VALUE']))
        {
            $return = Array();
            $arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => implode('|', $arUserField['VALUE'])
                )
            ));
            foreach ($arMediaLib as $mediaLib)
            {
                $return[] = $mediaLib['NAME'];
            }
            return implode("\r\n", $return);
        }
        else
        {
            $arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $arUserField['VALUE']
                )
            ));
            if (count($arMediaLib) > 0)
            {
                return $arMediaLib[0]['NAME'];
            }
            else return '';
        }
    }

    function GetFilterHTML($arUserField, $arHtmlControl)
    {
        global $lAdmin;
        $lAdmin->InitFilter(Array($arHtmlControl["NAME"]));

        $values = is_array($arHtmlControl["VALUE"]) ? $arHtmlControl["VALUE"] : Array($arHtmlControl["VALUE"]);

        if ($arUserField["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
        else $multiple = '';

        $html = "<select name='".$arHtmlControl['NAME'].($arUserField["MULTIPLE"] === "Y"?"[]":"")."' ".$multiple."><option value=''>Нет</option>";

        $arMediaLib = CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib)
        {
            $html .= "<option ".(in_array($mediaLib['ID'], $values)?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
        }

        $html .= "</select>";

        return  $html;
    }

    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        if ($arHtmlControl['VALUE'])
        {
            $arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
                array(
                    'ACTIVE' => 'Y',
                    'ID' => $arHtmlControl['VALUE']
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

    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        $return = "<select name='".$arHtmlControl['NAME']."' ".($arUserField['EDIT_IN_LIST']==='N'?"disabled='disabled'":"")."><option value=''>Нет</option>";

        $arMediaLib = CMedialibCollection::GetList();
        foreach ($arMediaLib as $mediaLib)
        {
            $return .= "<option ".($mediaLib['ID'] == $arHtmlControl["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
        }

        $return .= "</select>";

        return $return;
    }

    function GetDBColumnType($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }
}