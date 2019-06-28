<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?$sUniqueID = 'startshop_catalog_element_'.spl_object_hash($this);?>
<?
    global $APPLICATION;
	global $options;

    $this->setFrameMode(true);
    $frame = $this->createFrame()->begin();


    $arFlags = array(
        'LEFT_MENU' => $options['CATALOG_PRODUCT_MENU']['ACTIVE_VALUE'] == "Y" ? true : false,
        'SLIDER_SHOW' => $arParams['SHOW_SLIDER_IN_ELEMENT'] == "Y" ? true : false,
        'PROPERTIES_MINIMAL_SHOW' => $options['CATALOG_PRODUCT_MIN_PROPERTIES']['ACTIVE_VALUE'] == "Y" ? true : false,
        'ADAPTABLE' => $options['ADAPTIV']['ACTIVE_VALUE'] == "Y" ? true : false,
		'SHOW_PRODUCT_OF_DAY' => !empty($arResult['PROPERTIES']['CML2_DAY_PROD']['VALUE'])
    );
?>
<div class="startshop-catalog<?=$arFlags['ADAPTABLE'] ? ' adaptiv' : ''?>" id="<?=$sUniqueID?>">
	<?if ($arFlags['LEFT_MENU']):?>
		<div class="left_col">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog_vertical", array(
				"ROOT_MENU_TYPE" => "catalog",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_TIME" => "3600",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "2",
				"CHILD_MENU_TYPE" => "catalog",
				"USE_EXT" => "Y",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N",
				"HIDE_CATALOG" => "Y",
				"COUNT_ITEMS_CATALOG" => "8"
				),
				false
			);?> 
			<div class="clear"></div>
		</div>
		<div class="right_col">
	<?endif;?>
	<div class="startshop-item<?=$arFlags['LEFT_MENU']?' with-menu':''?>">
		<?if ($arFlags['SHOW_PRODUCT_OF_DAY']):?>
    		<?include('parts/ProductOfDay.php')?>
    	<?endif;?>
	<?reset($arResult['MORE_PHOTO']);?>
		<div class="startshop-row">
			<?include('parts/Slider.php')?>
			<div class="startshop-information">
				<div class="startshop-row">
					<?if (!empty($arResult['TEMPLATE_PROPERTIES']['ARTICLE']['VALUE'])):?>
						<div class="startshop-article"><?=GetMessage('SH_CE_DEFAULT_CML2_ARTICLE')?>: <?=$arResult['TEMPLATE_PROPERTIES']['ARTICLE']['VALUE']?></div>
					<?endif;?>
					<div class="startshop-state available StartShopOffersStateAvailable" style="<?=!$arResult['STARTSHOP']['AVAILABLE'] ? 'display: none;' : ''?>">
						<div class="startshop-icon"></div>
						<?=GetMessage('SH_CE_DEFAULT_PRODUCT_AVAILABLE')?>
						<span class="StartShopOffersQuantity" <?=$arResult['STARTSHOP']['QUANTITY']['VALUE'] == 0?'style="display: none;"':''?>><?=$arResult['STARTSHOP']['QUANTITY']['VALUE']?></span>
					</div>
					<div class="startshop-state unavailable StartShopOffersStateUnavailable" style="<?=$arResult['STARTSHOP']['AVAILABLE'] ? 'display: none;' : ''?>">
						<div class="startshop-icon"></div>
						<?=GetMessage('SH_CE_DEFAULT_PRODUCT_NOT_AVAILABLE')?>
					</div>
				</div>
				<div class="startshop-indents-vertical indent-25"></div>
				<div class="startshop-row">
                    <?if (!empty($arResult['STARTSHOP']['OFFERS'])):?>
					    <?include('parts/OffersProperties.php')?>
                        <div class="startshop-indents-vertical indent-20"></div>
                    <?endif;?>
					<?include('parts/Order.php')?>
				</div>
			</div>
			<?if ($arFlags['LEFT_MENU']):?><div class="clear"></div><?endif;?>
			<div class="startshop-information with-menu">
				<?if (!empty($arResult['PREVIEW_TEXT'])):?>
					<div class="startshop-indents-vertical indent-25"></div>
					<div class="startshop-row">
						<div class="startshop-description startshop-text-default">
							<?=$arResult['PREVIEW_TEXT']?>
						</div>
					</div>
				<?endif;?>
				<?if ($arFlags['PROPERTIES_MINIMAL_SHOW']):?>
					<?include('parts/PropertiesMinimal.php')?>
				<?endif;?>
			</div>
			<div class="clear"></div>
		</div>
		<?
		$properties = $arResult['DISPLAY_PROPERTIES'];
		unset($properties['ARTICLE']); // Удаляем артикул		
		?>
		<?if ($options['CATALOG_PRODUCT_VIEW']['ACTIVE_VALUE'] == 'WITH_TABS'):?>
		<div class="startshop-indents-vertical indent-50"></div>
		<div class="startshop-row">
			<?include('parts/ViewWithTabs.php')?>
		</div>
		<?else:?>
		<div class="startshop-row">
			<?include('parts/ViewWithoutTabs.php')?>
		</div>
		<?endif;?>
		<?if (is_array($arResult["PROPERTIES"]["CML2_ACCOMPANYING"]["VALUE"]) && count($arResult["PROPERTIES"]["CML2_ACCOMPANYING"]["VALUE"]) > 0):?>
		<div class="startshop-indents-vertical indent-50"></div>
		<div class="startshop-row">
			<div id="expandables" class="startshop-item_description">
				<?$GLOBALS["arrFilter"] = array("ID" => $arResult["PROPERTIES"]["CML2_ACCOMPANYING"]["VALUE"]);?> 		 	
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					"slider",
					Array(
						"AJAX_MODE" => "N",
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => "",
						"SECTION_CODE" => "",
						'TITLE' => GetMessage('PRODUCT_GOING_GOODS'),
						"SECTION_USER_FIELDS" => array(),
						"ELEMENT_SORT_FIELD" => "sort",
						"ELEMENT_SORT_ORDER" => "asc",
						"FILTER_NAME" => "arrFilter",
						"FLEXISEL_ID" => "accompanyingList",
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
		<?session_start();?>
		<?if (!is_array($_SESSION["VIEWED_PRODUCTS"])) $_SESSION["VIEWED_PRODUCTS"] = array();?>
        <?if (!in_array($arResult['ID'], $_SESSION["VIEWED_PRODUCTS"])):?>
            <?$_SESSION["VIEWED_PRODUCTS"][] = $arResult['ID']?>
        <?endif;?>
		<?if (is_array($_SESSION["VIEWED_PRODUCTS"]) && count($_SESSION["VIEWED_PRODUCTS"]) > 0):?>
			<div class="startshop-indents-vertical indent-50"></div>
			<div class="startshop-row">
				<?$GLOBALS["arrFilter"] = array("ID" => $_SESSION["VIEWED_PRODUCTS"])?> 		 	
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					"slider",
					Array(
						"AJAX_MODE" => "N",
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => "",
						"SECTION_CODE" => "",
						'TITLE' => GetMessage('PRODUCT_YOU_LOOKED'),
						"SECTION_USER_FIELDS" => array(),
						"ELEMENT_SORT_FIELD" => "sort",
						"ELEMENT_SORT_ORDER" => "asc",
						"FILTER_NAME" => "arrFilter",
						"FLEXISEL_ID" => "viewedList",
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
		<?if ($options['CATALOG_PRODUCT_VIEW']['ACTIVE_VALUE'] != 'WITH_TABS'):?>
			<?if($arParams['USE_REVIEW'] && is_numeric($arParams['REVIEWS_IBLOCK_ID'])):?>
				<div class="startshop-indents-vertical indent-50"></div>
				<div class="startshop-row">
					<div class="startshop-title"><?=GetMessage('REVIEWS_TITLE')?></div>
					<div class="startshop-indents-vertical indent-15"></div>
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
					<div class="clear"></div>
				</div>
			<?endif;?>
		<?endif;?>
	</div>
	<div style="clear: both;"></div>
</div>
<?include('parts/OffersScript.php')?>
<?$frame->end()?>