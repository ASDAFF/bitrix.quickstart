<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if( $arParams['USE_AJAX'] == "Y" && 
    !empty($arParams['TEMPLATE_AJAX_ID'])) {
        
    $arResult['COMPONENT_CONTAINER_ID'] = $arParams['TEMPLATE_AJAX_ID'];
    $arResult['INSTANT_RELOAD'] = true;
}
?>

<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);
echo CUtil::PHPToJSObject($arResult, true);
?>