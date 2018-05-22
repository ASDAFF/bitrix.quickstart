<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arParams['INCLUDE_JQUERY']=='Y' ){
	$APPLICATION->AddHeadScript($templateFolder.'/js/jquery-1.11.0.min.js');
}
if( $arParams['INCLUDE_JQUERY_COOKIE']=='Y' ){
	$APPLICATION->AddHeadScript($templateFolder.'/js/jquery.cookie.js');
}