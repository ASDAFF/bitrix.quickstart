<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_SUBSCRIBE_NEW"));
$APPLICATION->IncludeComponent(
	'bitrix:catalog.product.subscribe.list',
	'al',
	array(
        'SET_TITLE' => $arParams['SET_TITLE_SUBSCRIBE'],
        'LINE_ELEMENT_COUNT' => '5',
    ),
	$component
);

