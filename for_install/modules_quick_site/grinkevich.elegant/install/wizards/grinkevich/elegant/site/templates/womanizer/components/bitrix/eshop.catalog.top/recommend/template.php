<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0){ ?>
<?
if (empty($arParams["FLAG_PROPERTY_CODE"]))
	$arParams["FLAG_PROPERTY_CODE"] = rand();
?>

<?foreach($arResult["ITEMS"] as $key => $arItem){?>
	<div class="item" id="prod<?=$arItem["ID"]?>">
		<?if(!(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])) && !$arItem["CAN_BUY"]):?>
			<div class="badge notavailable"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></div>
		<?endif?>

		<?if ( is_array($arItem["PREVIEW_IMG"]) ){
			?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="img" style="background: #ffffff url('<?=$arItem["PREVIEW_IMG"]["SRC"]?>') center bottom no-repeat;"><div class="incart"></div></div></a><?
		}?>

		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["PROPERTIES"]["MANUFACTURER"]["VALUE"]?> <?=$arItem["NAME"]?></a>
		<div class="price">
			<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
			{
				if (count($arItem["OFFERS"]) > 1)
				{
					echo GetMessage("CR_PRICE_OT")."&nbsp;";
					echo $arItem["PRINT_MIN_OFFER_PRICE"];
				}
				else
				{
					foreach($arItem["OFFERS"] as $arOffer):?>
						<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
							<?if($arPrice["CAN_ACCESS"]):?>
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<?=$arPrice["PRINT_DISCOUNT_VALUE"]?><br>
										<span class="old_price"><?=$arPrice["PRINT_VALUE"]?></span><br>
										<?else:?>
										<?=$arPrice["PRINT_VALUE"]?>
									<?endif?>
							<?endif;?>
						<?endforeach;?>
					<?endforeach;
				}
			}
			else // if product doesn't have offers
			{
				if(count($arItem["PRICES"])>0 && $arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] == $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE']):
					foreach($arItem["PRICES"] as $code=>$arPrice):
						if($arPrice["CAN_ACCESS"]):
							?>
								<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
									<?=$arPrice["PRINT_DISCOUNT_VALUE"]?><br>
									<span itemprop = "price" class="old_price"><?=$arPrice["PRINT_VALUE"]?></span>
								<?else:?>
									<?=$arPrice["PRINT_VALUE"]?>
								<?endif;?>
							<?
						endif;
					endforeach;
				else:
					$price_from = '';
					if($arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] > $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'])
					{
						$price_from = GetMessage("CR_PRICE_OT")."&nbsp;";
					}
					CModule::IncludeModule("sale")
					?>
					<?=$price_from?><?=FormatCurrency($arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'], CSaleLang::GetLangCurrency(SITE_ID))?>
					<?
				endif;
			}
			?>
		</div>
	</div>
<?}?>

<?}?>