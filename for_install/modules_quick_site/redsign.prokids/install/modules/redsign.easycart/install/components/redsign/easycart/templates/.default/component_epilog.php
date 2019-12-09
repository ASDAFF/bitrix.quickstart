<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arParams['INCLUDE_JQUERY']=='Y' )
{
	//$APPLICATION->AddHeadScript($templateFolder.'/js/jquery-1.11.0.min.js');
	$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/components/redsign/easycart/templates/.default/js/jquery-1.11.0.min.js"></script>');
}
if( $arParams['INCLUDE_JQUERY_COOKIE']=='Y' )
{
	//$APPLICATION->AddHeadScript($templateFolder.'/js/jquery.cookie.js');
	$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/components/redsign/easycart/templates/.default/js/jquery.cookie.js"></script>');
}