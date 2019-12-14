<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

switch($arParams['VIEW']) {
	case 'showcase': //////////////////////////////////////// showcase ////////////////////////////////////////
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/showcase.php");
		break;
	case 'gallery': //////////////////////////////////////// gallery ////////////////////////////////////////
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/gallery.php");
		break;
	default: //////////////////////////////////////// table ////////////////////////////////////////
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/table.php");
}

$templateData['ADD_HIDER'] = false;
if(!is_array($arResult['ITEMS']) || count($arResult['ITEMS'])<1 && $arParams['EMPTY_ITEMS_HIDE_FIL_SORT']=='Y' && empty($_REQUEST['set_filter']) ) {
	$templateData['ADD_HIDER'] = true;
}