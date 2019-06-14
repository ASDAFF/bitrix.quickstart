<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!\Bitrix\Main\Loader::includeModule('redsign.monopoly'))
	return;

$arParams['HEAD_TYPE'] = RSMonopoly::getSettings('headType', 'type1');

if( \Bitrix\Main\Loader::includeModule('iblock') ) {
	// take data about curent section
	$arFilter = array(
		'ID' => $arParams['IBLOCK_ID'],
		'ACTIVE' => 'Y',
	);
	$obCache = new CPHPCache();
	if($obCache->InitCache(36000, serialize($arFilter) ,'/iblock/catalog')) {
		$arCurIBlock = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$arCurIBlock = array();
		$dbRes = CIBlock::GetList(array(), $arFilter, false);
		if(defined('BX_COMP_MANAGED_CACHE')) {
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache('/iblock/catalog');
			if ($arCurIBlock = $dbRes->Fetch()) {
				$CACHE_MANAGER->RegisterTag('iblock_id_'.$arParams['IBLOCK_ID']);
			}
			$CACHE_MANAGER->EndTagCache();
		} else {
			if(!$arCurIBlock = $dbRes->Fetch()) {
				$arCurIBlock = array();
			}
		}
		$obCache->EndDataCache($arCurIBlock);
	}
	// /take data about curent section
}

?><div class="row"><?
	?><div class="col col-md-12"><?=$arCurIBlock['DESCRIPTION']?></div><?
	?><div class="col col-md-12"><?

?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"monopoly",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => "1",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
		"SET_TITLE" => $arParams["SET_TITLE"],
		// monopoly
		"SIDEBAR" => ($arParams["HEAD_TYPE"]=='type3' ? 'Y' : 'N'),
        "RSMONOPOLY_SHOW_DESC_IN_SECTION" => "Y"
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?><?

	?></div><?
?></div>