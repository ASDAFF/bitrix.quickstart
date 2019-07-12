<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><?$ElementID = $APPLICATION->IncludeComponent(
	'bitrix:catalog.element',
	'gopro',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
		'META_KEYWORDS' => $arParams['DETAIL_META_KEYWORDS'],
		'META_DESCRIPTION' => $arParams['DETAIL_META_DESCRIPTION'],
		'BROWSER_TITLE' => $arParams['DETAIL_BROWSER_TITLE'],
		'BASKET_URL' => $arParams['BASKET_URL'],
		'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
		'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
		'SECTION_ID_VARIABLE' => $arParams['SECTION_ID_VARIABLE'],
		'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
		'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'SET_STATUS_404' => $arParams['SET_STATUS_404'],
		'PRICE_CODE' => $arParams['PRICE_CODE'],
		'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
		'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],
		'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
		'PRICE_VAT_SHOW_VALUE' => $arParams['PRICE_VAT_SHOW_VALUE'],
		'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
		'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
		'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['ADD_PROPERTIES_TO_BASKET']) ? $arParams['ADD_PROPERTIES_TO_BASKET'] : ''),
		'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
		'LINK_IBLOCK_TYPE' => $arParams['LINK_IBLOCK_TYPE'],
		'LINK_IBLOCK_ID' => $arParams['LINK_IBLOCK_ID'],
		'LINK_PROPERTY_SID' => $arParams['LINK_PROPERTY_SID'],
		'LINK_ELEMENTS_URL' => $arParams['LINK_ELEMENTS_URL'],
		
		'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
		'OFFERS_FIELD_CODE' => $arParams['DETAIL_OFFERS_FIELD_CODE'],
		'OFFERS_PROPERTY_CODE' => $arParams['DETAIL_OFFERS_PROPERTY_CODE'],
		'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
		'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
		'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
		'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
		
		'LIST_OFFERS_FIELD_CODE' => $arParams['LIST_OFFERS_FIELD_CODE'],
		'LIST_OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
		'LIST_OFFERS_LIMIT' => $arParams['LIST_OFFERS_LIMIT'],
		
		'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
		'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
		'SECTION_ID' => $arResult['VARIABLES']['SECTION_ID'],
		'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
		'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['element'],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
		'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],
		'USE_COMPARE' => $arParams['USE_COMPARE'],
		// goPro params
		'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
		'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
		'USE_FAVORITE' => $arParams['USE_FAVORITE'],
		'USE_SHARE' => $arParams['USE_SHARE'],
		'OFF_MEASURE_RATION' => $arParams['OFF_MEASURE_RATION'],
		'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
		'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
		'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
		'PROPS_ATTRIBUTES_COLOR' => $arParams['PROPS_ATTRIBUTES_COLOR'],
		// store
		'STORES_TEMPLATE' => $arParams['STORES_TEMPLATE'],
		'USE_STORE' => $arParams['USE_STORE'],
		"STORE_PATH" => $arParams['STORE_PATH'],
		'MAIN_TITLE' => $arParams['MAIN_TITLE'],
		'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
		'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
		"STORES" => $arParams['STORES'],
		"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
		"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
		"USER_FIELDS" => $arParams['USER_FIELDS'],
		"FIELDS" => $arParams['FIELDS'],
		// element
		'PROPS_TABS' => $arParams['PROPS_TABS'],
		'USE_CHEAPER' => $arParams['USE_CHEAPER'],
		'USE_BLOCK_MODS' => $arParams['USE_BLOCK_MODS'],
		'DETAIL_TABS_VIEW' => $arParams['DETAIL_TABS_VIEW'],
		'SHOW_PREVIEW_TEXT' => $arParams['SHOW_PREVIEW_TEXT'],
		// seo
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "N",
	),
	$component
);?><?

?><div class="clear"></div><?

// MODS
if($arParams['USE_BLOCK_MODS']=='Y') {
	$obCache = new CPHPCache();
	if($obCache->InitCache(36000, serialize($arFilter) ,'/iblock/catalog')) {
		$arCurIBlock = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$arCurIBlock = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
		if(defined('BX_COMP_MANAGED_CACHE')) {
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache('/iblock/catalog');
			if($arCurIBlock) {
				$CACHE_MANAGER->RegisterTag('iblock_id_'.$arParams['IBLOCK_ID']);
			}
			$CACHE_MANAGER->EndTagCache();
		} else {
			if(!$arCurIBlock) {
				$arCurIBlock = array();
			}
		}
		$obCache->EndDataCache($arCurIBlock);
	}
	?><div class="mods"><!-- mods --><?
		global $modFilter,$JSON;
		$modFilter = array('PROPERTY_'.$arCurIBlock['OFFERS_PROPERTY_ID']=>$ElementID);
		?><h3 class="title2"><?=$arParams['MODS_BLOCK_NAME']?></h3><?
global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
$APPLICATION->IncludeComponent('redsign:catalog.sorter', 'gopro', array(
	'ALFA_ACTION_PARAM_NAME' => 'alfaction',
	'ALFA_ACTION_PARAM_VALUE' => 'alfavalue',
	'ALFA_CHOSE_TEMPLATES_SHOW' => 'Y',
	'ALFA_CNT_TEMPLATES' => '3',
	'ALFA_DEFAULT_TEMPLATE' => 'table',
	'ALFA_CNT_TEMPLATES_0' => $arParams['SORTER_TEMPLATE_NAME_3'],
	'ALFA_CNT_TEMPLATES_NAME_0' => 'table',
	'ALFA_CNT_TEMPLATES_1' => $arParams['SORTER_TEMPLATE_NAME_2'],
	'ALFA_CNT_TEMPLATES_NAME_1' => 'gallery',
	'ALFA_CNT_TEMPLATES_2' => $arParams['SORTER_TEMPLATE_NAME_1'],
	'ALFA_CNT_TEMPLATES_NAME_2' => 'showcase',
	'ALFA_SORT_BY_SHOW' => 'N',
	'ALFA_OUTPUT_OF_SHOW' => 'N',
	'AJAXPAGESID' => 'ajaxpages_mods',
	),
	false
);
		?><div class="clear"></div><?
		?><div id="ajaxpages_mods" class="ajaxpages_gmci"><!-- ajaxpages_gmci --><?
global $APPLICATION,$JSON;
$IS_SORTERCHANGE = 'N';
if($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['sorterchange']=='ajaxpages_mods') {
	$IS_SORTERCHANGE = 'Y';
	$JSON['TYPE'] = 'OK';
}
$IS_AJAXPAGES = 'N';
if($_REQUEST['ajaxpages']=='Y' && $_REQUEST['ajaxpagesid']=='ajaxpages_mods') {
	$IS_AJAXPAGES = 'Y';
	$JSON['TYPE'] = 'OK';
}
			$APPLICATION->IncludeComponent(
				'bitrix:catalog.section',
				'gopro',
				array(
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
					'IBLOCK_ID' => $arCurIBlock['OFFERS_IBLOCK_ID'],
					'ELEMENT_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],//$arParams['ELEMENT_SORT_FIELD'],
					'ELEMENT_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],//$arParams['ELEMENT_SORT_ORDER'],
					'ELEMENT_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],//$arParams['ELEMENT_SORT_FIELD2'],
					'ELEMENT_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],//$arParams['ELEMENT_SORT_ORDER2'],
					'PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
					'META_KEYWORDS' => $arParams['LIST_META_KEYWORDS'],
					'META_DESCRIPTION' => $arParams['LIST_META_DESCRIPTION'],
					'BROWSER_TITLE' => $arParams['LIST_BROWSER_TITLE'],
					'INCLUDE_SUBSECTIONS' => 'N',
					'BASKET_URL' => $arParams['BASKET_URL'],
					'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
					'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PRODUCT_PROPS_VARIABLE' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'FILTER_NAME' => 'modFilter',
					'CACHE_TYPE' => $arParams['CACHE_TYPE'],
					'CACHE_TIME' => $arParams['CACHE_TIME'],
					'CACHE_FILTER' => $arParams['CACHE_FILTER'],
					'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
					'SET_TITLE' => 'N',
					'SET_STATUS_404' => 'N',
					'DISPLAY_COMPARE' => 'N',
					'PAGE_ELEMENT_COUNT' => '100',
					'LINE_ELEMENT_COUNT' => $arParams['LINE_ELEMENT_COUNT'],
					'PRICE_CODE' => $arParams['PRICE_CODE'],
					'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
					'SHOW_PRICE_COUNT' => $arParams['SHOW_PRICE_COUNT'],

					'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
					'USE_PRODUCT_QUANTITY' => $arParams['~USE_PRODUCT_QUANTITY'],
					'ADD_PROPERTIES_TO_BASKET' => (isset($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : ''),
					'PARTIAL_PRODUCT_PROPERTIES' => (isset($arParams['PARTIAL_PRODUCT_PROPERTIES']) ? $arParams['PARTIAL_PRODUCT_PROPERTIES'] : ''),
					'PRODUCT_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],

					'DISPLAY_TOP_PAGER' => 'N',
					'DISPLAY_BOTTOM_PAGER' => 'N',
					'PAGER_TITLE' => $arParams['PAGER_TITLE'],
					'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
					'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
					'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
					'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
					'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
					'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
					'CURRENCY_ID' => $arParams['CURRENCY_ID'],
					// ajaxpages
					'AJAXPAGESID' => 'ajaxpages_mods',
					'IS_AJAXPAGES' => $IS_AJAXPAGES,
					'IS_SORTERCHANGE' => $IS_SORTERCHANGE,
					// goPro params
					'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
					'PROP_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
					'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
					'USE_FAVORITE' => 'N',
					'USE_SHARE' => 'N',
					'SHOW_ERROR_EMPTY_ITEMS' => 'N',
					'OFF_MEASURE_RATION' => $arParams['OFF_MEASURE_RATION'],
					'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
					'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
					'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
					// store
					'USE_STORE' => $arParams['USE_STORE'],
					'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
					'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
					'MAIN_TITLE' => $arParams['MAIN_TITLE'],
					// -----
					'BY_LINK' => 'Y',
					'DONT_SHOW_LINKS' => 'Y',
					'VIEW' => $alfaCTemplate,
					'COLUMNS5' => 'Y',
				),
				$component,
				array('HIDE_ICONS'=>'Y')
			);
if($IS_AJAXPAGES=='Y' || $IS_SORTERCHANGE=='Y') {
	$APPLICATION->RestartBuffer();
	if(SITE_CHARSET!='utf-8') {
		$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
		$json_str_utf = json_encode($data);
		$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
		echo $json_str;
	} else {
		echo json_encode($JSON);
	}
	die();
}
		?></div><!-- /ajaxpages_gmci --><?
	?></div><!-- /mods --><?
	?><script>
	if( $('#ajaxpages_mods').find('.js-element').length<1 ) {
		$('.mods').hide();
	}
	</script><?
}

// bigdata
if( $arParams['USE_BLOCK_BIGDATA']=='Y' ) {
	$obCache = new CPHPCache();
	if($obCache->InitCache(36000, serialize($arFilter) ,'/iblock/catalog')) {
		$arCurIBlock = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$arCurIBlock = CIBlockPriceTools::GetOffersIBlock($arParams['IBLOCK_ID']);
		if(defined('BX_COMP_MANAGED_CACHE')) {
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache('/iblock/catalog');
			if($arCurIBlock) {
				$CACHE_MANAGER->RegisterTag('iblock_id_'.$arParams['IBLOCK_ID']);
			}
			$CACHE_MANAGER->EndTagCache();
		} else {
			if(!$arCurIBlock) {
				$arCurIBlock = array();
			}
		}
		$obCache->EndDataCache($arCurIBlock);
	}
	?><div class="bigdata js-bigdata" style="display:none;"><!-- /bigdata --><?
		?><h3 class="title2"><?=$arParams['BIGDATA_BLOCK_NAME']?></h3><?
global $alfaCTemplate, $alfaCSortType, $alfaCSortToo, $alfaCOutput;
$APPLICATION->IncludeComponent('redsign:catalog.sorter', 'gopro', array(
	'ALFA_ACTION_PARAM_NAME' => 'alfaction',
	'ALFA_ACTION_PARAM_VALUE' => 'alfavalue',
	'ALFA_CHOSE_TEMPLATES_SHOW' => 'Y',
	'ALFA_CNT_TEMPLATES' => '3',
	'ALFA_DEFAULT_TEMPLATE' => 'table',
	'ALFA_CNT_TEMPLATES_0' => $arParams['SORTER_TEMPLATE_NAME_3'],
	'ALFA_CNT_TEMPLATES_NAME_0' => 'table',
	'ALFA_CNT_TEMPLATES_1' => $arParams['SORTER_TEMPLATE_NAME_2'],
	'ALFA_CNT_TEMPLATES_NAME_1' => 'gallery',
	'ALFA_CNT_TEMPLATES_2' => $arParams['SORTER_TEMPLATE_NAME_1'],
	'ALFA_CNT_TEMPLATES_NAME_2' => 'showcase',
	'ALFA_SORT_BY_SHOW' => 'N',
	'ALFA_OUTPUT_OF_SHOW' => 'N',
	'AJAXPAGESID' => 'ajaxpages_bigdata',
	),
	false
);
		?><div class="clear"></div><?
		?><div id="ajaxpages_bigdata" class="ajaxpages_gmci"><!-- /ajaxpages_gmci --><?
global $APPLICATION,$JSON;
$IS_SORTERCHANGE = 'N';
if($_REQUEST['AJAX_CALL']=='Y' && $_REQUEST['sorterchange']=='ajaxpages_bigdata') {
	$IS_SORTERCHANGE = 'Y';
	$JSON['TYPE'] = 'OK';
}
$IS_AJAXPAGES = 'N';
if($_REQUEST['ajaxpages']=='Y' && $_REQUEST['ajaxpagesid']=='ajaxpages_bigdata') {
	$IS_AJAXPAGES = 'Y';
	$JSON['TYPE'] = 'OK';
}
			?><?$APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", "gopro", array(
					"LINE_ELEMENT_COUNT" => 5,
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_cbdp",
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
					"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
					"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
					"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
					"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
					"SHOW_NAME" => "Y",
					"SHOW_IMAGE" => "Y",
					"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
					"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
					"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
					"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
					"PAGE_ELEMENT_COUNT" => 5,
					"SHOW_FROM_SECTION" => "N",
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"DEPTH" => "2",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
					"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => $arParams['ADD_PICT_PROP'],
					"LABEL_PROP_".$arParams["IBLOCK_ID"] => "-",
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
					"SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
					"ID" => $ElementID,
					"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
					"PROPERTY_CODE_".$arCurIBlock['OFFERS_IBLOCK_ID'] => $arParams['LIST_OFFERS_PROPERTY_CODE'],
					"RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : ''),
					/////////////////////////////////////
					'DISPLAY_COMPARE' => $arParams['USE_COMPARE'],
					// ajaxpages
					'AJAXPAGESID' => 'ajaxpages_bigdata',
					'IS_AJAXPAGES' => $IS_AJAXPAGES,
					'IS_SORTERCHANGE' => $IS_SORTERCHANGE,
					// goPro params
					'PROP_MORE_PHOTO' => $arParams['PROP_MORE_PHOTO'],
					'PROP_ARTICLE' => $arParams['PROP_ARTICLE'],
					'PROP_ACCESSORIES' => $arParams['PROP_ACCESSORIES'],
					'USE_FAVORITE' => $arParams['USE_FAVORITE'],
					'USE_SHARE' => $arParams['USE_SHARE'],
					'SHOW_ERROR_EMPTY_ITEMS' => $arParams['SHOW_ERROR_EMPTY_ITEMS'],
					'EMPTY_ITEMS_HIDE_FIL_SORT' => 'Y',
					'USE_AUTO_AJAXPAGES' => $arParams['USE_AUTO_AJAXPAGES'],
					'OFF_MEASURE_RATION' => $arParams['OFF_MEASURE_RATION'],
					// showcase
					'OFF_SMALLPOPUP' => $arParams['OFF_SMALLPOPUP'],
					// SKU
					'PROP_SKU_MORE_PHOTO' => $arParams['PROP_SKU_MORE_PHOTO'],
					'PROP_SKU_ARTICLE' => $arParams['PROP_SKU_ARTICLE'],
					'PROPS_ATTRIBUTES' => $arParams['PROPS_ATTRIBUTES'],
					'PROPS_ATTRIBUTES_COLOR' => $arParams['PROPS_ATTRIBUTES_COLOR'],
					// store
					'USE_STORE' => $arParams['USE_STORE'],
					'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
					'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
					'MAIN_TITLE' => $arParams['MAIN_TITLE'],
					// view
					'VIEW' => $alfaCTemplate,
					// columns
					'COLUMNS5' => 'Y',
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
if($IS_AJAXPAGES=='Y' || $IS_SORTERCHANGE=='Y') {
	$APPLICATION->RestartBuffer();
	if(SITE_CHARSET!='utf-8') {
		$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
		$json_str_utf = json_encode($data);
		$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
		echo $json_str;
	} else {
		echo json_encode($JSON);
	}
	die();
}
		?></div><!-- /ajaxpages_gmci --><?
	?></div><!-- /bigdata --><?
}
// /bigdata

// tabs
?><?
?><div class="detailtabs <?if($arParams['DETAIL_TABS_VIEW']=='anchor'):?>anchor<?else:?>tabs<?endif;?>"><?
	?><div class="headers clearfix"><?
		$APPLICATION->ShowViewContent('TABS_HTML_HEADERS');
		if( $arParams['USE_REVIEW']=='Y' && IsModuleInstalled('forum') )
		{
			?><a class="switcher" href="#review"><?=GetMessage('TABS_REVIEW')?></a><?
		}
	?></div><?
	?><div class="contents"><?
		$APPLICATION->ShowViewContent('TABS_HTML_CONTENTS');
		if( $arParams['USE_REVIEW']=='Y' && IsModuleInstalled('forum') ) {
			?><div class="content selected review" id="review"><?
				?><a class="switcher" href="#review"><?=GetMessage('TABS_REVIEW')?></a><?
				?><div class="contentbody clearfix"><?
					?><a class="add2review btn3" href="#addreview"><?=GetMessage('ADD_REVIEW')?></a><?
					?><?$APPLICATION->IncludeComponent(
						'bitrix:forum.topic.reviews',
						'gopro',
						Array(
							"URL_TEMPLATES_DETAIL" => $arParams["REVIEWS_URL_TEMPLATES_DETAIL"],			
							"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
							"FORUM_ID" => $arParams["FORUM_ID"],
							'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
							'IBLOCK_ID' => $arParams['IBLOCK_ID'],
							'ELEMENT_ID' => $ElementID,
							"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
							"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
							"PAGE_NAVIGATION_TEMPLATE" => $arParams["PAGE_NAVIGATION_TEMPLATE"],
							"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
							"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							'AJAX_POST' => 'N',
							'AJAX_MODE' => 'N',
						),
						$component,
						array('HIDE_ICONS' => 'Y')
					);?><?
				?></div><?
			?></div><?
		}
	?></div><!-- /contents --><?
?></div>