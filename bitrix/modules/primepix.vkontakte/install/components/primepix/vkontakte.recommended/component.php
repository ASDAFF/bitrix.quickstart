<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arResult = array();
 
if ($this->StartResultCache())
{
    // ????????? ???????????? ??????????
    $arParams["ID_APLICATION"] = ($arParams["ID_APLICATION"]) ? $arParams["ID_APLICATION"] : "";
    $arParams["NUM_RECORDS"] = ($arParams["NUM_RECORDS"]) ? $arParams["NUM_RECORDS"] : "5";
    $arParams["PERIOD"] = ($arParams["PERIOD"]) ? $arParams["PERIOD"] : "week";
    $arParams["FORMULATION"] = ($arParams["FORMULATION"]) ? $arParams["FORMULATION"] : "0";
    $arParams["SORT"] = ($arParams["SORT"]) ? $arParams["SORT"] : "friend_likes";
    $arParams["REF"] = ($arParams["REF"]) ? $arParams["REF"] : "parent";

    // ????????? ??????????
    $options = sprintf('{limit: %s, period: "%s", verb: "%s", sort: "%s", target: "%s"}',
        $arParams['NUM_RECORDS'],
        $arParams['PERIOD'],
        $arParams['FORMULATION'],
        $arParams['SORT'],
        $arParams['REF']);

    // ????????? ??????????
    $aplication = ($arParams['ID_APLICATION'])? 
        $arParams['ID_APLICATION']:
        COption::GetOptionString("socialservices", "vkontakte_appid", NULL);

    $arResult['ID_APLICATION'] = $arParams['ID_APLICATION'];
    $arResult['FORM']['NUM_RECORDS'] = $arParams['NUM_RECORDS'];
    $arResult['FORM']['PERIOD'] = $arParams['PERIOD'];
    $arResult['FORM']['FORMULATION'] = $arParams['FORMULATION'];
    $arResult['EXTRA']['SORT'] = $arParams['SORT'];
    $arResult['MEDIA']['REF'] = $arParams['REF'];
    $arResult['OPTIONS'] = $options;
    
    // ??????????? ????????
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