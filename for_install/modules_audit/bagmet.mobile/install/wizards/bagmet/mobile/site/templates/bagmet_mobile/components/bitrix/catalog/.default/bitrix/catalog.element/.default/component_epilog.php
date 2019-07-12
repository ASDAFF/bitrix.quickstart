<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery.sliderkit/js/jquery.mousewheel.min.js');
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery.sliderkit/js/jquery.easing.1.3.min.js');
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery.sliderkit/js/jquery.sliderkit.1.9.2.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/jquery.sliderkit/css/sliderkit-core.css');
$APPLICATION->AddHeadScript('/bitrix/templates/'.SITE_TEMPLATE_ID.'/js/fancybox/jquery.fancybox-1.3.1.pack.js');
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/js/fancybox/jquery.fancybox-1.3.1.css');

if(count($arResult["ACCESSORIES"]) > 0):
	global $arRecPrFilter;
	$arRecPrFilter["ID"] = $arResult["ACCESSORIES"];
endif;
if(count($arResult["SAME_GOODS"]) > 0):
	global $arSamePrFilter;
	$arSamePrFilter["ID"] = $arResult["SAME_GOODS"];
endif;
