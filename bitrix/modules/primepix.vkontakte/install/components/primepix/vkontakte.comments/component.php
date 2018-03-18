<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arResult = array();
 
if ($this->StartResultCache())
{
    // установка обязательных параметров
    $arParams["ID_APLICATION"] = ($arParams["ID_APLICATION"]) ? $arParams["ID_APLICATION"] : "";
    $arParams["WIDTH_FORM"] = ($arParams["WIDTH_FORM"]) ? $arParams["WIDTH_FORM"] : "496";
    $arParams["NUM_COMMENTS"] = ($arParams["NUM_COMMENTS"]) ? $arParams["NUM_COMMENTS"] : "10";
    
    // обработка параметров
    if ($arParams['MEDIA_GRAFFITI'] == "Y") $attach = "graffiti";
    if ($arParams['MEDIA_PHOTOS'] == "Y") $attach .= ",photo";
    if ($arParams['MEDIA_VIDEO'] == "Y") $attach .= ",video";
    if ($arParams['MEDIA_AUDIO'] == "Y") $attach .= ",audio";
    if ($arParams['MEDIA_REF'] == "Y") $attach .= ",link";

    if (($arParams['MEDIA_GRAFFITI'] == "Y")AND($arParams['MEDIA_PHOTOS'] == "Y")AND
        ($arParams['MEDIA_VIDEO'] == "Y")AND($arParams['MEDIA_AUDIO'] == "Y")AND
        ($arParams['MEDIA_REF'] == "Y")) 
        $attach = "*";
    else
        $attach = "false";

    $options = sprintf('{limit: %s, width: "%s", attach: "%s"}',
        $arParams['NUM_COMMENTS'],
        $arParams['WIDTH_FORM'],
        $attach);

    // обработка результата
    $aplication = ($arParams['ID_APLICATION'])? 
        $arParams['ID_APLICATION']:
        COption::GetOptionString("socialservices", "vkontakte_appid", NULL);

    $arResult['ID_APLICATION'] = $arParams['ID_APLICATION'];
    $arResult['FORM']['WIDTH'] = $arParams['WIDTH_FORM'];
    $arResult['NUM_COMMENTS'] = $arParams['NUM_COMMENTS'];
    $arResult['MEDIA']['GRAFFITI'] = $arParams['GRAFFITI'];
    $arResult['MEDIA']['PHOTOS'] = $arParams['MEDIA_PHOTOS'];
    $arResult['MEDIA']['VIDEO'] = $arParams['MEDIA_VIDEO'];
    $arResult['MEDIA']['AUDIO'] = $arParams['MEDIA_AUDIO'];
    $arResult['MEDIA']['REF'] = $arParams['MEDIA_REF'];
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