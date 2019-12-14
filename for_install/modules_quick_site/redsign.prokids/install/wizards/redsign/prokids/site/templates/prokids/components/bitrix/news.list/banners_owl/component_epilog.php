<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if( $arParams['RSGOPRO_NOT_INCLUDE_OWL_SCRIPTS']!='Y' ) {
	$APPLICATION->AddHeadScript($templateFolder.'/owl/owl.carousel.min.js');
	$APPLICATION->SetAdditionalCSS($templateFolder.'/owl/owl.carousel.css');
}