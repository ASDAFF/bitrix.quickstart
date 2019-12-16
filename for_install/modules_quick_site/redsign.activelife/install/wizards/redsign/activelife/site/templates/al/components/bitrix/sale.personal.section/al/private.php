<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PRIVATE"));
$APPLICATION->SetTitle(Loc::getMessage("SPS_TITLE_PRIVATE"));
?><?$APPLICATION->IncludeComponent(
	"bitrix:main.profile",
	"al",
	Array(
		"SET_TITLE" => "Y",
		"AJAX_MODE" => $arParams['AJAX_MODE_PRIVATE'],
		"SEND_INFO" => $arParams["SEND_INFO_PRIVATE"],
		"CHECK_RIGHTS" => $arParams['CHECK_RIGHTS_PRIVATE']
	),
	$component
);?>