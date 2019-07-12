<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"]) > 0){?>
</div>
	<div class="r-col">
		<h2><?=$arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["NAME"]?></h2>
		<div class="goods-list">
			<?
			global $arRecPrFilter;
			$arRecPrFilter["ID"] = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"];
			$APPLICATION->IncludeComponent("bitrix:eshop.catalog.top", "recommend", array(
					"IBLOCK_TYPE" => "",
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"ELEMENT_SORT_FIELD" => "sort",
					"ELEMENT_SORT_ORDER" => "desc",
					//"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"DISPLAY_COMPARE" => "N",
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"FILTER_NAME" => "arRecPrFilter",
					"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
					"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
					"SHARPEN" => $arParams["SHARPEN"],
					"ELEMENT_COUNT" => 3,
				),
				false
			);
			?>
		</div>
	</div>
<?}else{?>
</div>	
<?}?>


