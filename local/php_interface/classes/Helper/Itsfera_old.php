<?php
namespace Helper;

use Bitrix\Main\Loader;

class Itsfera
{
    public static function getBrandByCode( $sCode )
    {
        if ( !empty($sCode) ) {
            Loader::includeModule("iblock");
            $arSelect = Array("ID","PROPERTY_SEO_TEXT");
            $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode('brands'), "CODE" => $sCode, "ACTIVE" => "Y");

            $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), $arSelect);
            if ($ob = $res->GetNextElement()){
                $arFields =$ob->GetFields();

                return $arFields;
            }
        }
        return false;

    }

    public static function getSubBrandsByBrandCode( $sCode )
    {
        return array();
    }
}