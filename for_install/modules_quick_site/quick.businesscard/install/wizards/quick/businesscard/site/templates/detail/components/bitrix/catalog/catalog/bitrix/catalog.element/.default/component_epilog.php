<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION, $MESS;
@include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/component_epilog.php");
?>
<div class="sidebar mt-40">
	<div class="side product-item">
		<div class="tabs-style-2">
			<ul class="nav nav-tabs" role="tablist">
				<li><a href="#descriprion" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_TAB_DESCRIPTION")?></a></li>
				<li class="active"><a href="#specifications" role="tab" data-toggle="tab"><i class="fa fa-files-o"></i><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_TAB_SPECIFICATIONS")?></a></li>
				<li><a href="#documents" role="tab" data-toggle="tab"><i class="fa fa-file-word-o"></i><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_TAB_DOCUMENTS")?></a></li>
				<li><a href="#comments" role="tab" data-toggle="tab"><i class="fa fa-star"></i>(<span id="comments-quantity">0</span>) <?=GetMessage("QUICK_BUSINESSCARD_CATALOG_TAB_COMMENTS")?></a></li>
			</ul>
			<div class="tab-content padding-top-clear padding-bottom-clear">
				<div class="tab-pane fade" id="descriprion">
					<?=$arResult["DETAIL_TEXT"]?>
				</div>
				<div class="tab-pane fade in active" id="specifications">
				<?if(!empty($arResult["PROPERTIES"]["SPECIFICATION_NAME"]["VALUE"])):?>
					<dl class="dl-horizontal space-top">
					<?foreach($arResult["PROPERTIES"]["SPECIFICATION_NAME"]["VALUE"] as $index=>$NAME):?>
						<dt><?=$NAME?></dt>
						<dd><?=$arResult["PROPERTIES"]["SPECIFICATION_VALUE"]["VALUE"][$index]?></dd>
					<?endforeach?>
					</dl>
				<?endif?>
				</div>
				<div class="tab-pane fade" id="documents">
				<?if(!empty($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"])):?>
					<div class="space-top doc">
						<?$count = count($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"]);
						foreach($arResult["PROPERTIES"]["DOCUMENTS"]["ITEMS"] as $arItem):?>
							<?if($cell%2 == 0):?>
							<div class="row">
							<?endif?>
								<div class="<?if($count>1):?>col-sm-6<?else:?>col-sm-12<?endif?>">
									<i class="fa <?=$arItem["PROPERTIES"]["ICON"]["VALUE"]?>"></i><a href="<?=$arItem["PROPERTIES"]["FILE"]["SRC"]?>" target="_blank"><?=$arItem["NAME"]?></a><?if(!empty($arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"])):?><span class="file-type">[<?=$arItem["PROPERTIES"]["DESCRIPTION"]["VALUE"]?>]</span><?endif?>
								</div>
							<?$cell++;
							if($cell%2 == 0 || $count == $cell):?>
							</div>
							<?endif?>
						<?endforeach?>
					</div>
				<?endif?>
				</div>
				<div class="tab-pane fade" id="comments">
					<div class="comments-form">
						<div class="col-md-12">
							<div id="results-comments">
								<div class="alert alert-danger" id="beforesend-comments">
									<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_BEFORESEND")?>
								</div>
								<div class="alert alert-danger" id="error-comments">
									<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_ERROR")?>
								</div>
								<div class="alert alert-success" id="success-comments">
									<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_SUCCESS")?>
								</div>
							</div>
							<img src="<?=SITE_DIR?>images/loading.gif" alt="Loading" id="form-loading-comments" class="pull-right" />
							<div class="clearfix"></div>
						</div>
						<form name="COMMENTS" action="<?=SITE_DIR?>include/" method="POST" role="form">
							<input type="hidden" name="COMMENTS[SITE_ID]" value="<?=SITE_ID?>"/>
							<input type="hidden" name="COMMENTS[IBLOCK_ID]" value="<?=$arParams["LINK_COMMENTS_IBLOCK_ID"]?>"/>
							<input type="hidden" name="COMMENTS[ID]" value="<?=$arResult["ID"]?>"/>
							<input type="hidden" name="COMMENTS[TITLE]" value="<?=$arResult["NAME"]?>"/>					
							<div class="col-sm-6">
								<div class="form-group has-feedback">
									<label><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_NAME_PLACEHOLDER")?></label>
									<input type="text" name="COMMENTS[NAME]" class="form-control req">
									<i class="fa fa-user form-control-feedback"></i>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group has-feedback">
									<label><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_EMAIL_PLACEHOLDER")?></label>
									<input type="email" name="COMMENTS[EMAIL]" class="form-control req">
									<i class="fa fa-envelope form-control-feedback"></i>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_STARS_PLACEHOLDER")?></label>
									<select name="COMMENTS[STARS]" class="form-control req">
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1</option>
									</select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group has-feedback">
									<label><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_COMMENT_PLACEHOLDER")?></label>
									<textarea name="COMMENTS[COMMENT]" class="form-control req" rows="8"></textarea>
									<i class="fa fa-envelope-o form-control-feedback"></i>
								</div>
							</div>
							<div class="col-sm-12">
								<input type="submit" value="<?=GetMessage("QUICK_BUSINESSCARD_CATALOG_COMMENTS_SEND")?>" class="btn btn-default pull-right">
							</div>
						</form>
					</div>
					<div class="clearfix"></div>
					<?global ${$arParams["FILTER_NAME"]."COMMENTS"}; ${$arParams["FILTER_NAME"]."COMMENTS"} = array("PROPERTY_".$arParams["LINK_COMMENTS_PROPERTY_SID"] => $arResult["ID"]);
					$APPLICATION->IncludeComponent( "bitrix:catalog.section", "comments-catalog",
						array(
							"IBLOCK_TYPE" => $arParams["LINK_COMMENTS_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["LINK_COMMENTS_IBLOCK_ID"],
							"ELEMENT_SORT_FIELD" => $arParams["LINK_COMMENTS_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["LINK_COMMENTS_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["LINK_COMMENTS_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["LINK_COMMENTS_ELEMENT_SORT_ORDER2"],
							"PROPERTY_CODE" => array(0=>"ID", 1=>"EMAIL", 3=>"STARS",),
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
							"PAGE_ELEMENT_COUNT" => $arParams["LINK_COMMENTS_PAGE_ELEMENT_COUNT"],
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
						false
					);
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?if(!empty($arResult["PROPERTIES"]["USE_SHARE"]["VALUE"])):?>
<script type="text/javascript">
(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
	var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
	s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
	s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
	var h=d[g]('body')[0];
	h.appendChild(s);
}})();
</script>
<div class="pluso pull-right" 
		data-url="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["DETAIL_PAGE_URL"]?>" 
		data-image="http://<?=$_SERVER["SERVER_NAME"]?><?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" 
		data-description="<?=htmlspecialchars($arResult["PREVIEW_TEXT"], ENT_QUOTES)?>" 
		data-title="<?=htmlspecialchars($arResult["NAME"], ENT_QUOTES)?>" 
		data-background="none;" 
		data-options="small,square,line,horizontal,nocounter,sepcounter=1,theme=14" 
		data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir">
</div>
<?endif?>
<div class="clearfix"></div>
<?if(!empty($arResult["PROPERTIES"]["MORE_PRODUCTS"]["ITEMS"])):?>
<?if(!empty($arResult["PROPERTIES"]["MORE_PRODUCTS_HEADER"]["VALUE"])):?><h2 class="underline"><?=$arResult["PROPERTIES"]["MORE_PRODUCTS_HEADER"]["~VALUE"]?></h2><?endif?>
<div class="owl-carousel <?if(!empty($arResult["PROPERTIES"]["MORE_PRODUCTS_AUTOPLAY"]["VALUE"])):?>carousel-autoplay-items-3<?else:?>carousel-items-3<?endif?> catalog-grid">
<?foreach($arResult["PROPERTIES"]["MORE_PRODUCTS"]["ITEMS"] as $cell=>$arItem):?>
	<div class="listing-item <?if($cell<3 && empty($arResult["PROPERTIES"]["MORE_PRODUCTS_AUTOPLAY"]["VALUE"])):?>object-non-visible<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" <?if($cell<3 && empty($arResult["PROPERTIES"]["MORE_PRODUCTS_AUTOPLAY"]["VALUE"])):?>data-animation-effect="fadeInLeft" data-effect-delay="<?=(200-$cell*100)?>"<?endif?>>
		<div class="overlay-container pic">
			<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
			<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
				<i class="fa fa-plus"></i>
			</a>
			<?else:?>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-image pic"></i></a>
			<?endif?>
			<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"]) || !empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?>
			<div class="tags">
				<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
				<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_NEW")?></span><?endif?>
			</div>
			<?endif?>
			<div class="status">
				<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_PRESENCE")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_EXPECTED")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_UNDER")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("QUICK_BUSINESSCARD_CATALOG_UNAVAILABLE")?></span><?endif?>
			</div>
		</div>
		<div class="listing-item-body clearfix">
			<h3 class="title <?if(empty($arItem["PREVIEW_TEXT"])):?>mb-15<?endif?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
			<?if(!empty($arItem["PREVIEW_TEXT"])):?><div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?></div><?endif?>
			<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
			<div class="pull-right">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white"><i class="fa fa-shopping-cart"></i></a>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>