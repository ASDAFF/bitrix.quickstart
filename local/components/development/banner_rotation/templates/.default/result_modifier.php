<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams['PAGER_STYLE'])) {
	$arParams['PAGER_STYLE'] = 'text';
}

// compatibility with 1.1.0
if(in_array($arParams['PAGER_STYLE'], array('horizontal', 'vertical'))) {
	$arParams['PAGER_ORIENT'] = $arParams['PAGER_STYLE'];
}

if(!isset($arParams['PAGER_POSITION'])) {
	$arParams['PAGER_POSITION'] = 'bottom_left';
}

if(!isset($arParams['EFFECT'])) {
	$arParams['EFFECT'] = '';
}

if(!isset($arParams['TRANSITION_SPEED'])) {
	$arParams['TRANSITION_SPEED'] = 300;
}

if(!isset($arParams['TRANSITION_INTERVAL'])) {
	$arParams['TRANSITION_INTERVAL'] = 5000;
}

$arResult['SLIDER_CSS_CLASS'] = 'beono-banner_slider';

if ($arParams['PAGER_STYLE']) {
	$arResult['SLIDER_CSS_CLASS'] .= ' beono-banner_slider_pager_'.$arParams['PAGER_STYLE'];
}

if ($arParams['PAGER_ORIENT']) {
	$arResult['SLIDER_CSS_CLASS'] .= ' beono-banner_slider_pager_'.$arParams['PAGER_ORIENT'];
}

if ($arParams['PAGER_POSITION']) {
	$arResult['SLIDER_CSS_CLASS'] .= ' beono-banner_slider_pager_'.$arParams['PAGER_POSITION'];
}
if($arParams['PAGER_STYLE']=='thumbs') {	
	foreach($arResult['BANNERS'] as $key=>$arBanner) {		
		$url = $arBanner['FIELDS']["URL"];
		$url = CAdvBanner::PrepareHTML($url, $arBanner['FIELDS']);
		$url = CAdvBanner::GetRedirectURL($url, $arBanner['FIELDS']);
		$arResult['BANNERS'][$key]['FIELDS']["URL"] = $url;
	}
}
?>