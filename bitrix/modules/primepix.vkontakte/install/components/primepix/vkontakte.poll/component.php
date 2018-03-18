<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arResult = array();
 
if ($this->StartResultCache())
{
    // установка обязательных параметров
    $arParams["ID_APLICATION"] = ($arParams["ID_APLICATION"]) ? $arParams["ID_APLICATION"] : "";
    $arParams["ID_POLL"] = ($arParams["ID_POLL"]) ? $arParams["ID_POLL"] : "0";
    $arParams["WIDTH_FORM"] = ($arParams["WIDTH_FORM"]) ? $arParams["WIDTH_FORM"] : "300";

    // обработка параметров
    $options = sprintf('{width: %s}', $arParams['WIDTH_FORM']); 
    
    // обработка результата
    $aplication = ($arParams['ID_APLICATION'])? 
        $arParams['ID_APLICATION']:
        COption::GetOptionString("socialservices", "vkontakte_appid", NULL);

    $arResult['ID_APLICATION'] = $arParams['ID_APLICATION'];
    $arResult['ID_POLL'] = $arParams['ID_POLL'];
    $arResult['FORM']['WIDTH'] = $arParams['WIDTH_FORM'];
    $arResult['OPTIONS'] = $options;
    
    // подключение скриптов
    if ($GLOBALS['VK_INIT'] != $aplication){
        if ( ! $GLOBALS['VK_API'])
            $APPLICATION->AddHeadString('<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?45"></script>');
        $APPLICATION->AddHeadString('<script type="text/javascript">VK.init({apiId: '.$aplication.', onlyWidgets: true});</script>');
        $GLOBALS['VK_INIT'] = $aplication;
        $GLOBALS['VK_API'] = true;
    }
                 
    $this->IncludeComponentTemplate();
}
?>