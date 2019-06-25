<div id="tabs" class="uni-tabs" style="position: static;">
	<ul class="tabs">
		<?if (strlen($arResult['DETAIL_TEXT']) > 0):?>
			<li class="tab"><a href="#description"><?=GetMessage("PRODUCT_DESCRIPTION")?></a></li>
		<?endif;?>
		<?if (count($properties) > 0 && is_array($properties)):?>
			<li class="tab"><a href="#properties"><?=GetMessage("PRODUCT_PROPERTIES")?></a></li>
		<?endif;?>
		<?if (is_array($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) && count($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) > 0):?>
			<li class="tab"><a href="#expandables"><?=GetMessage("PRODUCT_ACCESSORIES")?></a></li>
		<?endif;?>
		<?if(is_numeric($arParams['REVIEWS_IBLOCK_ID'])):?>
			<li class="tab"><a href="#reviews"><?=GetMessage("PRODUCT_REVIEWS")?></a></li>
		<?endif;?>
		<?if (!empty($arResult['PROPERTIES']['CML2_DOCUMENTS']['VALUE'])):?>
			<li class="tab"><a href="#documents"><?=GetMessage("PRODUCT_DOCUMENTS")?></a></li>
		<?endif;?>
		<div class="bottom-line"></div>
	</ul>
	<div class="clear"></div>
	<?if (strlen($arResult['DETAIL_TEXT']) > 0): // ?етальное описание?>
		<div id="description" class="startshop-description uni-text-default">
			<?=$arResult['DETAIL_TEXT']?>
		</div>
	<?endif;?>
	<?if (count($properties) > 0 && is_array($properties)):?>
		<div id="properties" class="startshop-item_description">
			<div class="startshop-properties">
				<?foreach ($properties as $property):?>
					<div class="startshop-property">
						<div class="startshop-name">
							<?=$property['NAME']?>
						</div>
						<?if (!is_array($property['VALUE'])) {?>
						<div class="startshop-value">
							<?=$property['VALUE']?>
						</div>
						<?} else {?>
							<div class="startshop-value">
							<?=implode(', ', $property['VALUE'])?>
						</div>
						<?}?>
					</div>
				<?endforeach;?>
			</div>
		</div>
	<?endif;?>
	<?if (is_array($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) && count($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) > 0):?>
		<div id="expandables" class="startshop-item_description">
			<?$GLOBALS["arrFilter"] = array("ID" => $arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]);?> 		 	
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				"slider",
				Array(
					"AJAX_MODE" => "N",
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_USER_FIELDS" => array(),
					"ELEMENT_SORT_FIELD" => "sort",
					"ELEMENT_SORT_ORDER" => "asc",
					"FILTER_NAME" => "arrFilter",
					"FLEXISEL_ID" => "expandablesList",
					"INCLUDE_SUBSECTIONS" => "Y",
					"SHOW_ALL_WO_SECTION" => "Y",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"BASKET_URL" => "/personal/cart/",
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"PRODUCT_QUANTITY_VARIABLE" => "quantity",
					"PRODUCT_PROPS_VARIABLE" => "prop",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"META_KEYWORDS" => "-",
					"META_DESCRIPTION" => "-",
					"BROWSER_TITLE" => "-",
					"ADD_SECTIONS_CHAIN" => "N",
					"DISPLAY_COMPARE" => "N",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"PAGE_ELEMENT_COUNT" => "10",
					"LINE_ELEMENT_COUNT" => $options['CATALOG_PRODUCT_MENU']['ACTIVE_VALUE'] == "Y"?'4':'6',
					"PROPERTY_CODE" => array(0=>"HIT",1=>"RECOMMEND",2=>"NEW",3=>"",),
					"OFFERS_FIELD_CODE" => array("ID"),
					"OFFERS_PROPERTY_CODE" => array(),
					"OFFERS_SORT_FIELD" => "sort",
					"OFFERS_SORT_ORDER" => "asc",
					"OFFERS_LIMIT" => "2",
					"PRICE_CODE" => array(0=>"BASE"),
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => "1",
					"PRICE_VAT_INCLUDE" => "Y",
					"USE_PRODUCT_QUANTITY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "N",
					"PAGER_TITLE" => "",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "shop",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"CONVERT_CURRENCY" => "N",
					"OFFERS_CART_PROPERTIES" => array(),
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N"
				)
			);?>
		</div>
	<?endif;?>
	<?if($arParams['USE_REVIEW'] && is_numeric($arParams['REVIEWS_IBLOCK_ID'])):?>
		<div id="reviews" class="startshop-item_description">
			<?$APPLICATION->IncludeComponent(
				"intec:reviews", 
				"reviews", 
				array(
					"IBLOCK_TYPE" => $arParams['REVIEWS_IBLOCK_TYPE'],
					"IBLOCK_ID" => $arParams['REVIEWS_IBLOCK_ID'],
					"ELEMENT_ID" => $arResult['ID'],
					"DISPLAY_REVIEWS_COUNT" => $arParams['MESSAGES_PER_PAGE']
				),
				$component
			);?>
		</div>
	<?endif;?>
	<?if (!empty($arResult['PROPERTIES']['CML2_DOCUMENTS']['VALUE'])):?>
		<div id="documents" class="startshop-item_description">
			<?include('Documents.php')?>
		</div>
	<?endif;?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#tabs").tabs({
				show: function(event, ui) { $(window).trigger('resize'); }
			});
		})
	</script>
</div>