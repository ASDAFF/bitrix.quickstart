<?if (strlen($arResult['DETAIL_TEXT']) > 0): // Детальное описание?>
	<div class="startshop-indents-vertical indent-50"></div>
    <div class="startshop-row">
		<div class="startshop-title"><?=GetMessage('SH_CE_DEFAULT_SECTION_DESCRIPTION')?></div>
        <div class="startshop-indents-vertical indent-25"></div>
		<div id="description" class="startshop-item_description startshop-text-default">
			<?=$arResult['DETAIL_TEXT']?>
		</div>
		<div class="clear"></div>
	</div>
<?endif;?>
<?if (!empty($arResult['DISPLAY_PROPERTIES'])):?>
	<div class="startshop-indents-vertical indent-50"></div>
	<div class="startshop-row">
		<div class="startshop-title"><?=GetMessage('SH_CE_DEFAULT_SECTION_PROPERTIES')?></div>
		<div class="startshop-indents-vertical indent-25"></div>
		<div id="properties" class="startshop-item_description">
			<div class="startshop-properties">
				<?foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty):?>
					<div class="startshop-property">
						<div class="startshop-name">
							<?=$arProperty['NAME']?>
						</div>
						<?if (!is_array($arProperty['VALUE'])):?>
							<div class="startshop-value">
								<?=$arProperty['VALUE']?>
							</div>
						<?else:?>
							<div class="startshop-value">
								<?=implode(', ', $arProperty['VALUE'])?>
							</div>
						<?endif;?>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?endif;?>

<?if (!empty($arResult['PROPERTIES']['CML2_DOCUMENTS']['VALUE'])):?>
	<div class="startshop-indents-vertical indent-50"></div>
	<div class="startshop-row">
		<div class="startshop-title"><?=GetMessage('DOCUMENTS_TITLE')?></div>
		<div class="startshop-indents-vertical indent-25"></div>
		<?include('Documents.php')?>
		<div class="clear"></div>
	</div>
<?endif;?>
<?if (is_array($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) && count($arResult["PROPERTIES"]["CML2_ACCESORIES"]["VALUE"]) > 0):?>
	<div class="startshop-indents-vertical indent-15"></div>
	<div class="startshop-row">
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
					'TITLE' => GetMessage('PRODUCT_ACCESSORIES'),
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
		<div class="clear"></div>
	</div>
	<?endif;?>