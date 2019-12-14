<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!empty($arParams["TEMPLATE_AJAX_ID"])) {
    ?><div id="<?=$arParams['TEMPLATE_AJAX_ID']?>"><?
}

if( $arResult['TEMPLATE_DEFAULT']['TEMPLATE']=='showcase' ) {
	//////////////////////////////////////// showcase ////////////////////////////////////////
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/showcase.php");
} elseif( $arResult['TEMPLATE_DEFAULT']['TEMPLATE']=='list' ) {
	//////////////////////////////////////// list ////////////////////////////////////////
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/list.php");
}

if(!empty($arParams["TEMPLATE_AJAX_ID"])) {
    ?></div><?
}