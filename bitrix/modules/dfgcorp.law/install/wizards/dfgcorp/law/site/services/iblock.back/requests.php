<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
if($_SESSION["DEMO_IBLOCK_BOOKS"] === false){
	$arReplace = array();
	if(intval($arReplace["FEEDBACK_IBLOCK_ID"])>0){
		$rsProperty = CIBlockProperty::GetByID("REQUEST_SERVICE", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		$rsProperty = CIBlockProperty::GetByID("REQUEST", $arReplace["FEEDBACK_IBLOCK_ID"], $iblockCode);
		$db_enum_list = CIBlockProperty::GetPropertyEnum("REQUEST_TYPE", Array(), Array("IBLOCK_ID"=>$arReplace["FEEDBACK_IBLOCK_ID"]));
		$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID;
?>