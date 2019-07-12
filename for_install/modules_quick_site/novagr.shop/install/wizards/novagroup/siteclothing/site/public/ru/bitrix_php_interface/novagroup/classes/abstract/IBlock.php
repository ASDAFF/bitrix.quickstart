<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_IBlock
{
    protected $lastResult;

    function checkInstalledModule()
    {
        if( !CModule::IncludeModule("iblock") ) die("iblock module is not installed");
    }

    function prepareFilter($arSelect = array())
    {
        $arSelect['ACTIVE'] = (isset($arSelect['ACTIVE'])) ? $arSelect['ACTIVE'] : "Y";
        $arSelect['ACTIVE_DATE'] = (isset($arSelect['ACTIVE_DATE'])) ? $arSelect['ACTIVE_DATE'] : "Y";
        return $arSelect;
    }

    function getSectionList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $bIncCnt = false, $arSelect = array(), $arNavStartParams=false)
    {
        $this->checkInstalledModule();
        $arFilter = $this->prepareFilter($arFilter);

        $res = CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt,$arSelect, $arNavStartParams);
        $arResult = array();
        while($ar_res = $res->GetNext())
        {
            $arResult[] = $ar_res;
        }
        return $arResult ;
    }

    function getSection($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $bIncCnt = false, $arSelect = array(), $arNavStartParams=false)
    {
        $arResult = $this->getSectionList($arOrder, $arFilter, $bIncCnt,$arSelect, $arNavStartParams);
        return $arResult[0];
    }

    function getElementList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=Array())
    {
        $res = $this->__getElementList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        $arResult = array();
        while($ar_res = $res->GetNext())
        {
            $arResult[] = $ar_res;
        }
        return $arResult ;
    }

    function __getElementList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=Array())
    {
        $this->checkInstalledModule();
        $arFilter = $this->prepareFilter($arFilter);
        return $this->lastResult = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
    }

    function getElement($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $arGroupBy=false, $arNavStartParams=false, $arSelectFields=Array())
    {
        $arResult = $this->getElementList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        return $arResult[0];
    }

    function getLastResult()
    {
        return $this->lastResult;
    }

    function SubQuery($strField, $arFilter)
    {
        if(is_array($arFilter) && count($arFilter)>0)
        {
            $arFilter = $this->prepareFilter($arFilter);
            return CIBlockElement::SubQuery(
                $strField,
                $arFilter
            );
        }
    }
}