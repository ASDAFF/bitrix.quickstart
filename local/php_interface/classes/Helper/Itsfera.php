<?php
use Bitrix\Main\Loader;
/**
 * Created by PhpStorm.
 * User: Fyodor V.
 * Date: 18.08.2016
 * Time: 11:57
 */
class Itsfera
{
    public static function convertCode( $sCode ){
        $output = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $sCode);
        $sCode = str_replace("'","_",$output);
        $trans = Cutil::translit($sCode,"ru");
        return $trans;
    }
    public static function getSubBrandsByBrandCode( $sCode ){
        $brands = false;
        $arSelect = Array("ID", "CODE");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode('brands'), "SECTION_CODE" => $sCode, "ACTIVE" => "Y");
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arr = $ob->GetFields();
            $brands[] = $arr['CODE'];
        }
        return $brands;
    }
    public static function getBrandByCode( $sCode )
    {
        if ( !empty($sCode) ) {
            Loader::includeModule("iblock");
            $arFilter = Array('IBLOCK_ID'=>getIBlockIdByCode('brands'), 'GLOBAL_ACTIVE'=>'Y', 'CODE'=>$sCode);
            $db_list = CIBlockSection::GetList(Array('sort'=>'asc'), $arFilter, false);
            if($ar_result = $db_list->GetNext())
            {
                $ar_result['IS_SECTION'] = 'Y';
                $ar_result['PROPERTY_SEO_TEXT_VALUE'] = $ar_result['~PROPERTY_SEO_TEXT_VALUE'] = $ar_result['DESCRIPTION'];
                return $ar_result;
            }
            else {
                $arSelect = Array("ID", "PROPERTY_SEO_TEXT", "NAME", "CODE");
                $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode('brands'), "CODE" => $sCode, "ACTIVE" => "Y");
                $res = \CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), $arSelect);
                if ($ob = $res->GetNextElement()) {
                    return $ob->GetFields();
                }
            }

        }
        return false;

    }


    /**получает ID разделов для элементов с определенным значение свойства (список)
     * @param $propName
     * @param $propValue
     *
     * @return array
     */
    public static function getSectionIDByElemProp ($propName, $propValue)
    {

        $cache = Bitrix\Main\Data\Cache::createInstance();
        if ($cache->initCache(7200, md5("getSectionIDByElemProp" . $propName . $propValue))) {
            $arSection = $cache->getVars();
        } else if ($cache->startDataCache()) {

            $con       = Bitrix\Main\Application::getConnection();
            $res       = $con->query(
                "
          SELECT DISTINCT 
            IBLOCK_SECTION_ID 
          FROM b_iblock_section_element 
          WHERE IBLOCK_ELEMENT_ID IN (
            SELECT
              t1.IBLOCK_ELEMENT_ID
            FROM b_iblock_element_property t1
              LEFT JOIN b_iblock_property_enum t2 ON t1.VALUE = t2.ID
              LEFT JOIN b_iblock_element t3 ON t1.IBLOCK_ELEMENT_ID = t3.ID
            WHERE t1.IBLOCK_PROPERTY_ID IN (SELECT ID
                                            FROM b_iblock_property
                                            WHERE CODE = '{$propName}')
                  AND t2.VALUE = '{$propValue}'
                  AND t3.ACTIVE = 'Y'
            );
            "
            );
            $arSection = array();
            while ($ob = $res->fetch()) {
                $arSection[$ob['IBLOCK_SECTION_ID']] = $ob['IBLOCK_SECTION_ID'];
            }

            $cache->endDataCache($arSection);
        }
        return $arSection;
    }


    /**Получаем ID инфоблоков по значению свойства элемента (тип список)
     * @param $propName
     * @param $propValue
     *
     * @return array
     */
    public static function getIBlockIDByElemProp ($propName, $propValue)
    {

        $cache = Bitrix\Main\Data\Cache::createInstance();
        if ($cache->initCache(7200, md5("getIBlockIDByElemProp" . $propName . $propValue))) {
            $arSection = $cache->getVars();
        } else if ($cache->startDataCache()) {

            $con       = Bitrix\Main\Application::getConnection();
            $res       = $con->query(
                "
                SELECT DISTINCT
                  t3.IBLOCK_ID
                FROM b_iblock_element_property t1
                  LEFT JOIN b_iblock_property_enum t2 ON t1.VALUE = t2.ID
                  LEFT JOIN b_iblock_element t3 ON t1.IBLOCK_ELEMENT_ID = t3.ID
                WHERE t1.IBLOCK_PROPERTY_ID IN (SELECT ID
                                                FROM b_iblock_property
                                                WHERE CODE = '{$propName}')
                    AND t2.VALUE = '{$propValue}'
                    AND t3.ACTIVE = 'Y';
            "
            );
            $arSection = array();
            while ($ob = $res->fetch()) {
                $arSection[$ob['IBLOCK_ID']] = $ob['IBLOCK_ID'];
            }

            $cache->endDataCache($arSection);
        }
        return $arSection;
    }

    /**
     * Получаем сео данные для инфоблока
     *
     * @param $arParams
     *
     * @return array
     */
    public static function getSeoParamsForIblock ($arParams)
    {

        $obCache    = \Bitrix\Main\Data\Cache::createInstance();
        $cache_time = "86400";
        $cache_id   = "seo_params".$arParams['IBLOCK']['ID'];

        if ($obCache->initCache($cache_time, $cache_id, "/seo_params/")) {
            $arResult = $obCache->GetVars();
        } else if ($obCache->startDataCache()) {

            $arResult  = array();
            $isInvalid = false;

            $IBLOCK_ID = $arParams['IBLOCK']['ID'];

            $ipropTemlates = new \Bitrix\Iblock\InheritedProperty\IblockTemplates($IBLOCK_ID);

            $arFields = $arParams['IBLOCK'];

            $values = $ipropTemlates->getValuesEntity();
            $entity = $values->createTemplateEntity();
            $entity->setFields($arFields);

            $templates = $ipropTemlates->findTemplates();

            foreach ($templates as $TEMPLATE_NAME => $templateInfo) {
                $arResult[$TEMPLATE_NAME] = \Bitrix\Main\Text\HtmlFilter::encode(
                    \Bitrix\Iblock\Template\Engine::process($entity, $templateInfo["TEMPLATE"]));

                $isInvalid = true;
            }

            if ($isInvalid) {
                $obCache->endDataCache($arResult);
            } else {
                $obCache->abortDataCache();
            }
        }


        return $arResult;
    }

}