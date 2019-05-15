<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arResult = array();
 
if ($this->StartResultCache())
{
    // установка обязательных параметров
    $arParams["ID_GROUP"] = ($arParams["ID_GROUP"]) ? $arParams["ID_GROUP"] : "33389398";
    $arParams["TYPE_FORM"] = ($arParams["TYPE_FORM"]) ? $arParams["TYPE_FORM"] : "0";
    $arParams["WIDTH_FORM"] = ($arParams["WIDTH_FORM"]) ? $arParams["WIDTH_FORM"] : "300";
    $arParams["HEIGHT_FORM"] = ($arParams["HEIGHT_FORM"]) ? $arParams["HEIGHT_FORM"] : "290";

    // обработка параметров
    $options = sprintf('{mode: %s, width: "%s", height: "%s"}, %s',
        $arParams['TYPE_FORM'],
        $arParams['WIDTH_FORM'],
        $arParams['HEIGHT_FORM'],
        $arParams['ID_GROUP']);
    switch ($arParams["TYPE_FORM"]) {
        case 0: $suffix = ""; break;
        case 1: $suffix = "name"; break;
        case 2: $suffix = "news"; break;
    }
    // обработка результата
    $arResult['ID_GROUP'] = $arParams['ID_GROUP'];
    $arResult['FORM']['TYPE'] = $arParams['TYPE_FORM'];
    $arResult['FORM']['WIDTH'] = $arParams['WIDTH_FORM'];
    $arResult['FORM']['HEIGHT'] = $arParams['HEIGHT_FORM'];
    $arResult['SUFFIX'] = $suffix;
    $arResult['OPTIONS'] = $options;
    
    // подключение скриптов
    if ( ! $GLOBALS['VK_API']){
        $APPLICATION->AddHeadString('<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?45"></script>');
        $GLOBALS['VK_API'] = true;
    }
                 
    $this->IncludeComponentTemplate();
}
?>