<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/*------------------add scripts and css if we need it---------------------*/
if($arParams[SET_JQ]==Y){
CUtil::InitJSCore(Array("jquery"));
}
if($arParams[SET_FB]==Y){
foreach($arResult[SCRIPTS] as $jssrc){
$APPLICATION->AddHeadScript($jssrc);
}
foreach($arResult[CSS] as $csssrc){
$APPLICATION->SetAdditionalCSS($csssrc);
}
}
/*----------------end of(add scripts and css if we need it)----------------*/

?>