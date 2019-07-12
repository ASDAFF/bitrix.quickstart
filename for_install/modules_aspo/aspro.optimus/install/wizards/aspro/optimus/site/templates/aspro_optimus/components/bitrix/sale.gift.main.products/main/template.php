<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$frame = $this->createFrame()->begin();

$injectId = 'sale_gift_main_products_'.rand();

$templateData['JS_OBJ'] = "BX.Sale['GiftMainProductsClass_{$component->getComponentId()}']";

// component parameters
$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParameters = $signer->sign(
	base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
	'bx.sale.gift.main.products'
);
$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.sale.gift.main.products');

?>
<div class="bx_item_list_you_looked_horizontal">
	<div id="<?= $injectId ?>" class="bx_sale_gift_main_products common_product wrapper_block">
		<?
		if($arResult['HAS_MAIN_PRODUCTS'])
		{
			global $searchFilter;
			$searchFilter = array();
			if($arResult['MAIN_ELEMENT_IDS'])
			{
				$searchFilter = array(
					"=ID" => $arResult['MAIN_ELEMENT_IDS'],
				);
			}
			?>
			<?if(empty($arParams['HIDE_BLOCK_TITLE']) || $arParams['HIDE_BLOCK_TITLE'] !== 'Y'){?>
				<div class="top_block">
					<div class="title_block"><? echo ($arParams['BLOCK_TITLE']? htmlspecialcharsbx($arParams['BLOCK_TITLE']) : GetMessage('SLB_TPL_TITLE_GIFT')) ?></div>
				</div>
			<?}?>
			<?
			$APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				"catalog_block_slider",
				array(
					"CUSTOM_CURRENT_PAGE" => $arParams["SGMP_CUR_BASE_PAGE"],
					"AJAX_MODE" => "N",
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],

					'SECTION_ID' => reset($arResult['MAIN_SECTION_IDS']),

					"ELEMENT_SORT_FIELD" => 'ID',
					"ELEMENT_SORT_ORDER" => 'DESC',
					//		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
					//		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
					"FILTER_NAME" => 'searchFilter',
					"SECTION_URL" => $arParams["SECTION_URL"],
					"DETAIL_URL" => $arParams["DETAIL_URL"],
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],

					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],

					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SET_TITLE" => $arParams["SET_TITLE"],
					"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),

					"ADD_PICT_PROP" => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),

					"LABEL_PROP" => (isset($arParams["LABEL_PROP"]) ? $arParams["LABEL_PROP"] : ""),
					"OFFER_ADD_PICT_PROP" => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
					"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : ""),
					"SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
					"SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
					"MESS_BTN_BUY" => (isset($arParams["MESS_BTN_BUY"]) ? $arParams["MESS_BTN_BUY"] : ""),
					"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["MESS_BTN_ADD_TO_BASKET"]) ? $arParams["MESS_BTN_ADD_TO_BASKET"] : ""),
					"MESS_BTN_DETAIL" => (isset($arParams["MESS_BTN_DETAIL"]) ? $arParams["MESS_BTN_DETAIL"] : ""),
					"MESS_NOT_AVAILABLE" => (isset($arParams["MESS_NOT_AVAILABLE"]) ? $arParams["MESS_NOT_AVAILABLE"] : ""),
					'ADD_TO_BASKET_ACTION' => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
					'SHOW_CLOSE_POPUP' => (isset($arParams["SHOW_CLOSE_POPUP"]) ? $arParams["SHOW_CLOSE_POPUP"] : ""),
					'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
					'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),

					"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
					"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],

					"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
					"SALE_STIKER" => $arParams["SALE_STIKER"],
					"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
					"DISPLAY_TYPE" => "block",
					"SHOW_RATING" => $arParams["SHOW_RATING"],
					"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
					"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],

					//self
					"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
					"CACHE_FILTER" => $arParams["CACHE_FILTER"],
					"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
					"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
					"BY_LINK" => "N",
					"DISPLAY_TOP_PAGER" => 'N',
					"DISPLAY_BOTTOM_PAGER" => 'N',
					"PAGER_TITLE" => $arParams["PAGER_TITLE"],
					"PAGER_TEMPLATE" => 'round',
					"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
					"PRODUCT_DISPLAY_MODE" => 'Y',
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_BASE_LINK_ENABLE" => "Y",
					"HIDE_SECTION_DESCRIPTION" => "Y",
					"SHOW_ALL_WO_SECTION" => "Y",
					"PAGER_BASE_LINK" => "/bitrix/components/bitrix/sale.gift.main.products/ajax.php",
				),
				$component,
				array('HIDE_ICONS' => 'Y')
			);
		}
		?>
	</div>
</div>
<script type="text/javascript">
BX(function () {
	BX.Sale['GiftMainProductsClass_<?= $component->getComponentId() ?>'] = new BX.Sale.GiftMainProductsClass({
		contextAjaxData:  {
			parameters:'<?=CUtil::JSEscape($signedParameters)?>',
			template: '<?=CUtil::JSEscape($signedTemplate)?>',
			site_id: '<?=CUtil::JSEscape(SITE_ID)?>'
		},
		injectId:  '<?= $injectId ?>',
		mainProductState:  '<?= $arResult['MAIN_PRODUCT_STATE'] ?>',
		isGift:  <?= $arResult['HAS_MAIN_PRODUCTS']? 'true' : 'false'; ?>,
		productId:  <?= $arParams['ELEMENT_ID']?: 'null'; ?>,
		offerId: <?= $arParams['OFFER_ID']?: 'null'; ?>
	});
	if(!$('.bx_item_list_you_looked_horizontal .all_wrapp').length){
		$('.bx_item_list_you_looked_horizontal').remove();
	}
});
BX.message({});
</script>
<?$frame->beginStub();?>
<?$frame->end();?>