<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);
if($_REQUEST["reset_form"]){
	$arResult["RESET_FORM"]="Y";
}
echo CUtil::PHPToJSObject($arResult, true);
?>