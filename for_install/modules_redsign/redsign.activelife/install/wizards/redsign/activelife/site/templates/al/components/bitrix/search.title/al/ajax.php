<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['CATEGORIES'])){
	?>
    <div class="search_popup">
    <?php
	global $arrSearchFilter;
	$arrSearchFilter = array('ID'=>$arResult['EXT_SEARCH']['CATALOG']['ITEMS']);
    ?>
	<?$APPLICATION->IncludeComponent(
		'bitrix:catalog.section',
		'in_search_title',
		Array(
			'IBLOCK_TYPE' => '',
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'ELEMENT_SORT_FIELD' => 'SORT',//$arParams['ELEMENT_SORT_FIELD'],
			'ELEMENT_SORT_ORDER' => 'ASC',//$arParams['ELEMENT_SORT_ORDER'],
			'PROPERTY_CODE' => array($arParams['ADDITIONAL_PICT_PROP']),
			'META_KEYWORDS' => '',
			'META_DESCRIPTION' => '',
			'BROWSER_TITLE' => '',
			'INCLUDE_SUBSECTIONS' => 'Y',
			'BASKET_URL' => '',
			'ACTION_VARIABLE' => '',
			'PRODUCT_ID_VARIABLE' => '',
			'SECTION_ID_VARIABLE' => '',
			'PRODUCT_QUANTITY_VARIABLE' => '',
			'FILTER_NAME' => 'arrSearchFilter',
			'CACHE_TYPE' => 'N',
			'CACHE_TIME' => '0',
			'CACHE_FILTER' => '',
			'CACHE_GROUPS' => '',
			'SET_TITLE' => 'N',
			'SET_STATUS_404' => 'N',
			'DISPLAY_COMPARE' => 'N',
			'PAGE_ELEMENT_COUNT' => '10',//$arParams['PAGE_ELEMENT_COUNT']
			'LINE_ELEMENT_COUNT' => '',
			'PRICE_CODE' => $arParams['PRICE_CODE'],
			'USE_PRICE_COUNT' => 'N',
			'SHOW_PRICE_COUNT' => 'N',
			'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
			'USE_PRODUCT_QUANTITY' => 'N',
			'DISPLAY_TOP_PAGER' => 'N',
			'DISPLAY_BOTTOM_PAGER' => 'N',
			'PAGER_TITLE' => '',
			'PAGER_SHOW_ALWAYS' => 'N',
			'PAGER_TEMPLATE' => '',
			'PAGER_DESC_NUMBERING' => '',
			'PAGER_DESC_NUMBERING_CACHE_TIME' => '0',
			'PAGER_SHOW_ALL' => 'N',
			'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
			'OFFERS_FIELD_CODE' => $arParams['OFFERS_FIELD_CODE'],
			'OFFERS_PROPERTY_CODE' => $arParams['OFFERS_PROPERTY_CODE'],
			'OFFERS_SORT_FIELD' => 'catalog_PRICE_'.$arParams['SKU_PRICE_SORT_ID'],
			'OFFERS_SORT_ORDER' => 'ASC',
			'OFFERS_LIMIT' => '0',
			'SECTION_ID' => '',
			'SECTION_CODE' => '',
			'SECTION_URL' => '',
			'DETAIL_URL' => '',
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'BY_LINK' => 'Y',
			'ADDITIONAL_PICT_PROP' => $arParams['ADDITIONAL_PICT_PROP'],
			'OFFER_ADDITIONAL_PICT_PROP' => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
		),
		false
	);

	if(!empty($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'])){
		foreach($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock){
			?><div class="title_search_result-iblock"><?
			?><div class="title_search_result-iblock-around_title"><div class="title_search_result-iblock-title_line"></div><div class="title_search_result-iblock-title"><span><?=$arIblock['NAME']?></span></div></div><?
			foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem){
				?><div class="title_search_result-iblock-item"><a href="<?=$arItem['URL']?>"><?=$arItem['NAME']?></a></div><?
			}
			?></div><?
		}
		
	}
	////////////////// other
	if(!empty($arResult['EXT_SEARCH']['OTHER']['ITEMS'])){
		?><div class="search_popup__sep"></div><?
		foreach($arResult['EXT_SEARCH']['OTHER']['ITEMS'] as $arOther){
			?><div class="title_search_result-other-item"><a href="<?=$arOther['URL']?>"><?=$arOther['NAME']?></a></div><?
		}
	}
	?>
    </div>
    <?
}