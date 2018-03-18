<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arResult = array();
 
if ($this->StartResultCache())
{
    // установка обязательных параметров
    $arParams["TYPE_BUTTON"] = ($arParams["TYPE_BUTTON"]) ? $arParams["TYPE_BUTTON"] : "full";
    $arParams["HEIGHT_BUTTON"] = ($arParams["HEIGHT_BUTTON"]) ? $arParams["HEIGHT_BUTTON"] : "22";
    $arParams["NAME_BUTTON"] = ($arParams["NAME_BUTTON"]) ? $arParams["HEIGHT_BUTTON"] : "like";

    // обработка параметров
    $options = "{type: \"".$arParams['TYPE_BUTTON']."\"";
    if ($arParams['HEIGHT_BUTTON'] != "22")
    	$options .= ", height: ".$arParams['HEIGHT_BUTTON'];
    if ($arParams['NAME_BUTTON'] == 'interes')
    	$options .= ", verb: 1";
    $options .= "}";
    if ($arParams['ID_ELEMENT']){
    	$options .= ", ".$arParams['ID_ELEMENT'];
        $suffix = "-";
    }

    // обработка результата
    $aplication = ($arParams['ID_APLICATION'])? 
        $arParams['ID_APLICATION']:
        COption::GetOptionString("socialservices", "vkontakte_appid", NULL);

    $arResult['ID_APLICATION'] = $aplication;
    $arResult['ID_ELEMENT'] = $arParams['ID_ELEMENT'];
    $arResult['FORM']['BUTTON'] = $arParams["TYPE_BUTTON"];
    $arResult['FORM']['HEIGHT'] = $arParams["HEIGHT_BUTTON"];
    $arResult['FORM']['NAME'] = $arParams["NAME_BUTTON"];
    $arResult['SUFFIX'] = $suffix.$arParams['ID_ELEMENT'];
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