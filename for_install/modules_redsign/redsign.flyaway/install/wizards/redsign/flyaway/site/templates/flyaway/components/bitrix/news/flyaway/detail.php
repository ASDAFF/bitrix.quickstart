<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

if(!\Bitrix\Main\Loader::includeModule('redsign.flyaway'))
	return;

// popup gallery
require_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/popupgallery.php');

$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	$arParams['RSFLYAWAY_DETAIL_TEMPLATES'],
	Array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" 			=> $arParams["USE_SHARE"],
		"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
		// flyaway
		"RSFLYAWAY_PROP_MARKER_TEXT"		=> $arParams["RSFLYAWAY_PROP_MARKER_TEXT_DETAIL"],
		"RSFLYAWAY_PROP_MARKER_COLOR"		=> $arParams["RSFLYAWAY_PROP_MARKER_COLOR_DETAIL"],
		"RSFLYAWAY_PROP_ACTION_DATE"		=> $arParams["RSFLYAWAY_PROP_ACTION_DATE_DETAIL"],
		"RSFLYAWAY_PROP_MORE_PHOTO" 		=> $arParams["RSFLYAWAY_PROP_MORE_PHOTO"],
	),
	$component
);
if(!empty($arParams['RSFLYAWAY_SHOW_RELATED_PRODUCTS']) && !empty($arParams['RSFLYAWAY_RELATED_PRODUCTS_CODE']) && \Bitrix\Main\Loader::includeModule('iblock')) {
	$obCache = new CPHPCache();

	$cacheId = array(
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'ELEMENT_ID' => $ElementID,
		'CODE' => $arParams['RSFLYAWAY_RELATED_PRODUCTS_CODE']
	);
	$cacheId = serialize($cacheId);
	$cacheDir = "/iblock/news.detail";

	$productsIds = array();
	if($obCache->InitCache(3600, $cacheId, $cacheDir)) {
		$productsIds = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$dbProperty = CIBlockElement::GetProperty($arParams['IBLOCK_ID'], $ElementID, array(),  array('CODE' => $arParams['RSFLYAWAY_RELATED_PRODUCTS_CODE']));
		while($arProperty = $dbProperty->Fetch()) {
			$productsIds[] = $arProperty['VALUE'];
		}

		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache($cacheDir);
		$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams['IBLOCK_ID']);
		$CACHE_MANAGER->EndTagCache();

		$obCache->EndDataCache($productsIds);
	}
	if(count($productsIds) > 0) {
		global $productsFilter;
		$productsFilter = array(
			'ID' => $productsIds
		);
		?><br/><div class="row"><div class="col col-md-9 products-new"><?
		?><h2><?=Loc::getMessage('RS.FLYAWAY.RELATED_PRODUCTS');?></h2><?
		$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"light_main",
		array(
			"ACTION_VARIABLE" => "action",
			"ADD_PROPERTIES_TO_BASKET" => "Y",
			"ADD_SECTIONS_CHAIN" => "N",
			"ADD_TO_BASKET_ACTION" => "ADD",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"BACKGROUND_IMAGE" => "-",
			"BASKET_URL" => "/personal/cart/",
			"BROWSER_TITLE" => "-",
			"CACHE_FILTER" => $arParams['CACHE_FILTER'],
			"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
			"CACHE_TIME" => $arParams['CACHE_TIME'],
			"CACHE_TYPE" => $arParams['CACHE_TYPE'],
			"CONVERT_CURRENCY" => "N",
			"DISPLAY_COMPARE" => "Y",
			"ELEMENT_SORT_FIELD" => "sort",
			"ELEMENT_SORT_FIELD2" => "id",
			"ELEMENT_SORT_ORDER" => "asc",
			"ELEMENT_SORT_ORDER2" => "desc",
			"FILTER_NAME" => "productsFilter",
			"HIDE_NOT_AVAILABLE" => "N",
			"IBLOCK_ID" => $arParams['RSFLYAWAY_RELATED_PRODUCTS_IBLOCK'],
			"IBLOCK_TYPE" => $arParams['RSFLYAWAY_RELATED_PRODUCTS_IBLOCK_TYPE'],
			"INCLUDE_SUBSECTIONS" => "A",
			"LABEL_PROP" => "-",
			"MIN_AMOUNT" => "10",
			"OFFERS_CART_PROPERTIES" => "",
			"OFFERS_FIELD_CODE" => array(
				0 => "ID",
				1 => "",
			),
			"OFFERS_LIMIT" => "5",
			"OFFERS_SORT_FIELD" => "sort",
			"OFFERS_SORT_FIELD2" => "id",
			"OFFERS_SORT_ORDER" => "asc",
			"OFFERS_SORT_ORDER2" => "desc",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "flyaway",
			"PAGE_ELEMENT_COUNT" => "120",
			"PARTIAL_PRODUCT_PROPERTIES" => "N",
			"PRICE_CODE" => array(
			0 => "BASE",
			),
			"PRICE_VAT_INCLUDE" => "N",
			"PRODUCT_DISPLAY_MODE" => "N",
			"PRODUCT_ID_VARIABLE" => "id",
			"PRODUCT_PROPERTIES" => array(),
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"PRODUCT_QUANTITY_VARIABLE" => "",
			"PRODUCT_SUBSCRIPTION" => "N",
			"PROPERTY_CODE" => array(),
			"RSFLYAWAY_TEMPLATE" => "showcase",
			"SECTION_CODE" => "",
			"SECTION_CODE_PATH" => "",
			"SECTION_ID" => "",
			"SECTION_ID_VARIABLE" => "SECTION_ID",
			"SECTION_URL" => "",
			"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
			),
			"SEF_MODE" => "N",
			"SEF_RULE" => "",
			"SET_BROWSER_TITLE" => "Y",
			"SET_LAST_MODIFIED" => "N",
			"SET_META_DESCRIPTION" => "Y",
			"SET_META_KEYWORDS" => "Y",
			"SET_STATUS_404" => "N",
			"SET_TITLE" => "Y",
			"SHOW_404" => "N",
			"SHOW_ALL_WO_SECTION" => "Y",
			"SHOW_CLOSE_POPUP" => "N",
			"SHOW_DISCOUNT_PERCENT" => "N",
			"SHOW_EMPTY_STORE" => "Y",
			"SHOW_GENERAL_STORE_INFORMATION" => "N",
			"SHOW_OLD_PRICE" => "N",
			"SHOW_PRICE_COUNT" => "1",
			"SHOW_SECTION_URL" => "Y",
			"SIDEBAR" => "Y",
			"STORES" => array(),
			"STORES_FIELDS" => array(),
			"STORE_PATH" => "/store/#store_id#",
			"USER_FIELDS" => array(
			0 => "",
			1 => "",
			),
			"USE_MAIN_ELEMENT_SECTION" => "N",
			"USE_MIN_AMOUNT" => "Y",
			"USE_PRICE_COUNT" => "N",
			"USE_PRODUCT_QUANTITY" => "N",
			"USE_STORE" => "Y",
			"OFFERS_PROPERTY_CODE" => array(),
			"COMPARE_PATH" => "",
			),
			false
		);

		?></div></div><?
	}
}

?><br/><?
?><div class="row"><?
	?><div class="col col-md-9"><?
		if( $arParams['RSFLYAWAY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
			$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				$arParams['RSFLYAWAY_LIST_TEMPLATES_DETAIL'],
				array(
					"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
					"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
					"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
					"SORT_BY1"	=>	$arParams["SORT_BY1"],
					"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
					"SORT_BY2"	=>	$arParams["SORT_BY2"],
					"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
					"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
					"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
					"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
					"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
					"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
					"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
					"SET_TITLE"	=>	"N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
					"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
					"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
					"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"DISPLAY_TOP_PAGER"	=>	"N",
					"DISPLAY_BOTTOM_PAGER"	=>	"N",
					"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
					"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
					"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
					"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
					"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
					"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
					"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
					"DISPLAY_NAME"	=>	"Y",
					"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
					"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
					"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
					"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
					"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
					"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
					"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
					"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
					"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
					// flyaway
					"RSFLYAWAY_SHOW_BLOCK_NAME"		=> $arParams["RSFLYAWAY_SHOW_BLOCK_NAME_DETAIL"],
					"RSFLYAWAY_BLOCK_NAME_IS_LINK"		=> $arParams["RSFLYAWAY_BLOCK_NAME_IS_LINK_DETAIL"],
					"RSFLYAWAY_USE_OWL"				=> $arParams["RSFLYAWAY_USE_OWL_DETAIL"],
					"RSFLYAWAY_OWL_CHANGE_SPEED"		=> $arParams["RSFLYAWAY_OWL_CHANGE_SPEED_DETAIL"],
					"RSFLYAWAY_OWL_CHANGE_DELAY"		=> $arParams["RSFLYAWAY_OWL_CHANGE_DELAY_DETAIL"],
					"RSFLYAWAY_OWL_PHONE"				=> $arParams["RSFLYAWAY_OWL_PHONE_DETAIL"],
					"RSFLYAWAY_OWL_TABLET"				=> $arParams["RSFLYAWAY_OWL_TABLET_DETAIL"],
					"RSFLYAWAY_OWL_MID"				=> $arParams["RSFLYAWAY_OWL_MID_DETAIL"],
					"RSFLYAWAY_OWL_PC"					=> $arParams["RSFLYAWAY_OWL_PC_DETAIL"],
					"RSFLYAWAY_COLS_IN_ROW"			=> $arParams["RSFLYAWAY_COLS_IN_ROW_DETAIL"],
					"RSFLYAWAY_PROP_PUBLISHER_NAME"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_NAME_DETAIL"],
					"RSFLYAWAY_PROP_PUBLISHER_BLANK"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_BLANK_DETAIL"],
					"RSFLYAWAY_PROP_PUBLISHER_DESCR"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_DESCR_DETAIL"],
					"RSFLYAWAY_PROP_MARKER_TEXT"		=> $arParams["RSFLYAWAY_PROP_MARKER_TEXT_DETAIL"],
					"RSFLYAWAY_PROP_MARKER_COLOR"		=> $arParams["RSFLYAWAY_PROP_MARKER_COLOR_DETAIL"],
					"RSFLYAWAY_PROP_ACTION_DATE"		=> $arParams["RSFLYAWAY_PROP_ACTION_DATE_DETAIL"],
					"RSFLYAWAY_PROP_FILE"				=> $arParams["RSFLYAWAY_PROP_FILE_DETAIL"],
					"RSFLYAWAY_LINK"					=> $arParams["RSFLYAWAY_LINK_DETAIL"],
					"RSFLYAWAY_BLANK"					=> $arParams["RSFLYAWAY_BLANK_DETAIL"],
					"RSFLYAWAY_SHOW_DATE"				=> $arParams["RSFLYAWAY_SHOW_DATE_DETAIL"],
					"RSFLYAWAY_AUTHOR_NAME"			=> $arParams["RSFLYAWAY_AUTHOR_NAME_DETAIL"],
					"RSFLYAWAY_AUTHOR_JOB"				=> $arParams["RSFLYAWAY_AUTHOR_JOB_DETAIL"],
					"RSFLYAWAY_SHOW_BUTTON"			=> $arParams["RSFLYAWAY_SHOW_BUTTON_DETAIL"],
					"RSFLYAWAY_BUTTON_CAPTION"			=> $arParams["RSFLYAWAY_BUTTON_CAPTION_DETAIL"],
					"RSFLYAWAY_PROP_VACANCY_TYPE"		=> $arParams["RSFLYAWAY_PROP_VACANCY_TYPE_DETAIL"],
					"RSFLYAWAY_PROP_SIGNATURE"			=> $arParams["RSFLYAWAY_PROP_SIGNATURE_DETAIL"],
					"RSFLYAWAY_SHOW_RELATED_PRODUCTS" => $arParams['RSFLYAWAY_SHOW_RELATED_PRODUCTS']
				),
				$component
			);
		}
	?></div><?
?></div><?

if( IsModuleInstalled('subscribe') && $arParams['RSFLYAWAY_DETAIL_USE_SUBSCRIBE']=='Y') {
	?><div class="row"><div class="col col-md-9"><?
	$APPLICATION->IncludeComponent(
		"bitrix:subscribe.form",
		"detail",
		array(
			"COMPONENT_TEMPLATE" => "detail",
			"USE_PERSONALIZATION" => "Y",
			"SHOW_HIDDEN" => "N",
			"PAGE" => $arParams['RSFLYAWAY_DETAIL_SUBSCRIBE_PAGE'],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"RSFLYAWAY_DETAIL_SUBSCRIBE_NOTE" => $arParams["RSFLYAWAY_DETAIL_SUBSCRIBE_NOTE"],
		),
		$component,
		array('HIDE_ICONS'=>'Y')
	);
	?></div></div><?
}
