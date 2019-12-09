<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

foreach ($arParams as $name => $prop){
	if(preg_match('/^ICON_NOVELTY_PROP_(\d+)$/', $name, $arMatches)){
		$iBlockID = (int)$arMatches[1];
		if(0 >= $iBlockID){
			continue;
		}
		if('' != $arParams[$name] && '-' != $arParams[$name]){
			$arParams['ICON_NOVELTY_PROP'][$iBlockID] = $arParams[$name];
		}
		unset($arParams[$arMatches[0]]);
	}
	if(preg_match('/^ICON_DEALS_PROP_(\d+)$/', $name, $arMatches)){
		$iBlockID = (int)$arMatches[1];
		if(0 >= $iBlockID){
			continue;
		}
		if('' != $arParams[$name] && '-' != $arParams[$name]){
			$arParams['ICON_DEALS_PROP'][$iBlockID] = $arParams[$name];
		}
		unset($arParams[$arMatches[0]]);
	}
	if(preg_match('/^ICON_DISCOUNT_PROP_(\d+)$/', $name, $arMatches)){
		$iBlockID = (int)$arMatches[1];
		if(0 >= $iBlockID){
			continue;
		}
		if('' != $arParams[$name] && '-' != $arParams[$name]){
			$arParams['ICON_DISCOUNT_PROP'][$iBlockID] = $arParams[$name];
		}
		unset($arParams[$arMatches[0]]);
	}
	if(preg_match('/^OFFER_TREE_COLOR_PROPS_(\d+)$/', $name, $arMatches)){
		$iBlockID = (int)$arMatches[1];
		if(0 >= $iBlockID){
			continue;
		}
		if('' != $arParams[$name] && '-' != $arParams[$name]){
			$arParams['OFFER_TREE_COLOR_PROPS'][$iBlockID] = $arParams[$name];
		}
		unset($arParams[$arMatches[0]]);
	}
	/*if(preg_match('/^ARTICLE_PROP_(\d+)$/', $name, $arMatches)){
		 $iBlockID = (int)$arMatches[1];
		if(0 >= $iBlockID){
		continue;
		}
		if('' != $arParams[$name] && '-' != $arParams[$name]){
		$arParams['ARTICLE_PROP'][$iBlockID] = $arParams[$name];
		}
		unset($arParams[$arMatches[0]]);
	}*/
}

if (!isset($arParams['SECTION_TITLE'])) {
    $arParams['SECTION_TITLE'] = getMessage('RS_SLINE.BSBS_AL.BESTSELLERS');
}

$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/result_modifier.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}