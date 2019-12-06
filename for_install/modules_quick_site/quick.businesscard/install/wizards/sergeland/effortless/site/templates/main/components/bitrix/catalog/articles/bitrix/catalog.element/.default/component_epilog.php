<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION, $MESS;
@include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/component_epilog.php");
?><br>
<h1 class="mb-30"><?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS")?> (<span id="comments-quantity">0</span>)</h2>	
<div id="results-comments">
	<div class="alert alert-danger" id="beforesend-comments">
		<?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_BEFORESEND")?>
	</div>
	<div class="alert alert-danger" id="error-comments">
		<?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_ERROR")?>
	</div>
	<div class="alert alert-success" id="success-comments">
		<?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_SUCCESS")?>
	</div>
</div>
<img src="<?=SITE_DIR?>images/loading.gif" alt="Loading" id="form-loading-comments" class="pull-right" />
<div class="clearfix"></div>
<div class="row">
	<div class="comments-form">
		<form name="COMMENTS" action="<?=SITE_DIR?>include/" method="POST" role="form">
			<input type="hidden" name="COMMENTS[SITE_ID]" value="<?=SITE_ID?>"/>
			<input type="hidden" name="COMMENTS[IBLOCK_ID]" value="<?=$arParams["LINK_IBLOCK_ID"]?>"/>
			<input type="hidden" name="COMMENTS[ID]" value="<?=$arResult["ID"]?>"/>
			<input type="hidden" name="COMMENTS[TITLE]" value="<?=$arResult["NAME"]?>"/>					
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label><?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_NAME_PLACEHOLDER")?></label>
					<input type="text" name="COMMENTS[NAME]" class="form-control req">
					<i class="fa fa-user form-control-feedback"></i>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group has-feedback">
					<label><?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_EMAIL_PLACEHOLDER")?></label>
					<input type="email" name="COMMENTS[EMAIL]" class="form-control req">
					<i class="fa fa-envelope form-control-feedback"></i>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group has-feedback">
					<label><?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_COMMENT_PLACEHOLDER")?></label>
					<textarea name="COMMENTS[COMMENT]" class="form-control req" rows="8"></textarea>
					<i class="fa fa-envelope-o form-control-feedback"></i>
				</div>
			</div>
			<div class="col-sm-12">
				<input type="submit" value="<?=GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENTS_SEND")?>" class="btn btn-default pull-right">
			</div>
		</form>
	</div>
</div>
<div class="clearfix"></div>
<?global ${$arParams["FILTER_NAME"]."COMMENTS"}; ${$arParams["FILTER_NAME"]."COMMENTS"} = array("PROPERTY_".$arParams["LINK_PROPERTY_SID"] => $arResult["ID"]);
$APPLICATION->IncludeComponent( "bitrix:catalog.section", "comments-articles",
	array(
		"IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $arParams["LINK_ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["LINK_ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => $arParams["LINK_ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["LINK_ELEMENT_SORT_ORDER2"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"SHOW_ALL_WO_SECTION" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"]."COMMENTS",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $arParams["LINK_PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

		/*
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		*/
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
	),
	$component
);
?>