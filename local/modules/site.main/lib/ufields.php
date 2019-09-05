<?php
/**
*  module
*
* @category    
* @link        http://.ru
* @revision    $Revision$
* @date        $Date$
*/

namespace Site\Main;

/**
* Утилиты для работы с пользовательскими свойствами
*/
class Ufields
{

    /**
    * Получаем значение пользовательского свойства по arFilter
    * @param $arrFilter - фильтр
    * @return array
    */
    public function getUFValue($arrFilter=array(), $cacheTime=3600){
        $arFilter = array();
        $arFilter = array_merge($arFilter, $arrFilter);
        $cache = new Cache(serialize(array(__METHOD__, $arFilter)), __CLASS__, $cacheTime);
        if ($cache->start()) {
            $rsField = \CUserFieldEnum::GetList(array(), $arFilter);
            while($arField = $rsField->GetNext()){
                $value[] = $arField;
            }
            if($value){
                $result = $value;
                $cache->end($result);
            }else{
                $cache->abort();
            }

        } else {
            $result = $cache->getVars();
        }
        return $result;
    }


    /**
    * Получаем значение пользовательского свойства по userID
    * @param $userID - id пользователя
    * @param $arSelect - массив получаемых полей
    * @param $arFields - массив получаемых пользовательских свойств
    * @return array
    */
    public function getUFValueByUser($userID, $arSelect = array(), $arFields = array(), $cacheTime=3600){
        if(!$userID){
            return false;
        }
        $cache = new Cache(serialize(array(__METHOD__, $userID, $arSelect, $arFields)), __CLASS__, $cacheTime);
        if ($cache->start()) {
            $rsUser = \CUser::GetList($by, $order,
                array("ID" => $userID),
                array("SELECT" => $arSelect, "FIELDS" => $arFields)
            );
            if($arUser = $rsUser->Fetch()){
                $value = $arUser;
            }
            if($value){
                $result = $value;
                $cache->end($result);
            }else{
                $cache->abort();
            }
        } else {
            $result = $cache->getVars();
        }
        return $result;
    }


    /**
    * Получаем максимальное значение свойства
    * 
    * @param mixed $ufCode  - код запрашиваемого свойства
    * @param mixed $cacheTime
    */
    public function getMaxValue($ufCode = '', $cacheTime = 3600)
    {
        if(!$ufCode) {
            return false;
        }

        $cache = new Cache(serialize(array(__METHOD__, $ufCode)), __CLASS__, $cacheTime);
        if ($cache->start()) {
            $rsUser = \CUser::GetList(($by = $ufCode), ($order = "desc"),
                array("ACTIVE" => "Y"),
                array("SELECT" => array($ufCode), "FIELDS" => array('ID'))
            );
            if($arUser = $rsUser->GetNext()){
                $value = $arUser[$ufCode];
            }
            
            if($value){
                $value = ceil(($value/100))*100;
                $result = $value;
                $cache->end($result);
            }else{
                $cache->abort();
            }
        } 
        else {
            $result = $cache->getVars();
        }

        return $result;
    }
}