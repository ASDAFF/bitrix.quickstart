<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<table class="catalog-detail" cellspacing="0">
		<tr>

		<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
			<td class="catalog-detail-image">
			<?if (is_array($arResult['DETAIL_PICTURE_350'])):?>
				<div class="catalog-detail-image" id="catalog-detail-main-image">
					<img src="<?=$arResult['DETAIL_PICTURE_350']['SRC']?>" alt="<?=$arResult["NAME"]?>" width="<?=$arResult['DETAIL_PICTURE_350']["WIDTH"]?>" height="<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>" />
				</div>
			<?endif;?>
				<div class="catalog-detail-images">
			<?if(count($arResult["MORE_PHOTO"])>0):
				foreach($arResult["MORE_PHOTO"] as $PHOTO):
			?>
				<div class="catalog-detail-image"><img border="0" src="<?=$PHOTO["SRC_PREVIEW"]?>" width="<?=$PHOTO["PREVIEW_WIDTH"]?>" height="<?=$PHOTO["PREVIEW_HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" /></div>
			<?
				endforeach;
			endif?>

				</div>
			</td>
		<?endif;?>

			<td class="catalog-detail-desc">
			<?if($arResult["PREVIEW_TEXT"]):?>
				<?=$arResult["PREVIEW_TEXT"];?>
			<?endif;?>
				
				<div class="catalog-detail-price">
				<?foreach($arResult["PRICES"] as $code=>$arPrice):
					if($arPrice["CAN_ACCESS"]):
				?>
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<span class="catalog-detail-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span> <s><?=$arPrice["PRINT_VALUE"]?></s>
					<?else:?>
						<span class="catalog-detail-price"><?=$arPrice["PRINT_VALUE"]?></span>
					<?endif;?>
				<?
						break;
					endif;
				endforeach;
				?>
				</div>
				<?if($arResult["CAN_BUY"]):?>
				<div class="catalog-detail-buttons">
					<a href="<?=$arResult["BUY_URL"]?>" data-role="button" data-theme="c" data-inline="true"><?echo GetMessage("CATALOG_BUY")?></a>
					<a href="<?=$arResult["ADD_URL"]?>" data-ajax="false" data-role="button" data-theme="c" data-inline="true" id="baskett"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
				</div>
				<?endif;?>
				<div class="catalog-item-links">
					<?if(!$arResult["CAN_BUY"] && (count($arResult["PRICES"]) > 0)):?>
					<span class="catalog-item-not-available"><!--noindex--><?=GetMessage("CATALOG_NOT_AVAILABLE");?><!--/noindex--></span>
					<?endif;?>

					<?if($arParams["USE_COMPARE"] == "Y"):?>
					<a href="<?=$arResult["COMPARE_URL"]?>" class="catalog-item-compare" onclick="return addToCompare(this, '<?=GetMessage("CATALOG_IN_COMPARE")?>');" rel="nofollow" id="catalog_add2compare_link" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>
					<?endif;?>
				</div>
			</td>
		</tr>
	</table>
	
<?
if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):
?>
	<?$arProperty = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]?>
	
	<?if(count($arProperty["DISPLAY_VALUE"]) > 0):?>
	<div class="catalog-detail-recommends">
		<h4><?=$arProperty["NAME"]?></h4>
			<div class="catalog-detail-recommend">
			<?
			global $arRecPrFilter;
			$arRecPrFilter["ID"] = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"];
			$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "", array(
				"IBLOCK_TYPE" => "",
				"IBLOCK_ID" => "",
				"ELEMENT_SORT_FIELD" => "sort",
				"ELEMENT_SORT_ORDER" => "desc",
				"ELEMENT_COUNT" => $arParams["ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
				"DETAIL_URL" => $arParams["DETAIL_URL"],
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
				"ELEMENT_COUNT" => 30,
				),
				$component
			);
			?>
		</div>
	</div>
	<?unset($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"])?>
	<?endif;?>
<?endif;?>
<?
if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):
?>
	<div class="catalog-detail-properties">
		<h4><?=GetMessage('CATALOG_PROPERTIES')?></h4>
		<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<div class="catalog-detail-property">
				<span><?=$arProperty["NAME"]?></span>
				<b>
<?
		if(is_array($arProperty["DISPLAY_VALUE"])):
			echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
		elseif($pid=="MANUAL"):
?>
					<a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a>
<?
		else:
			echo $arProperty["DISPLAY_VALUE"];
		endif;
?>
				</b>
			</div>
	<?endforeach;?>
	</div>
<?endif;?>

<?if($arResult["DETAIL_TEXT"]):?>
	<h4><?=GetMessage('CATALOG_FULL_DESC')?></h4>
	<?=$arResult["DETAIL_TEXT"];?>
<?endif;?>