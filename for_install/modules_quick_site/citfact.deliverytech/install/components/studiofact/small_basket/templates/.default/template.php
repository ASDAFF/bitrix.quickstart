<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->createFrame()->begin("");  ?>
<a id="small_basket" class="radius5 fr" href="<?=$arParams["PATH_TO_BASKET"];?>">
	<span class="mobile"><? if ($arResult["NUM_PRODUCTS"] > 0) { ?><span class="quant inline"><?=$arResult["NUM_PRODUCTS"];?></span><? } ?><span class="icon inline"></span></span>
	<span class="desktop"><? if ($arResult["NUM_PRODUCTS"] > 0) { ?><span class="quant inline"><?=$arResult["NUM_PRODUCTS"];?></span><? } else { ?><span class="icon inline"></span><? } ?><span class="text inline"><?=GetMessage("SF_SMALL_BASKET");?></span></span>
</a>
<? if (count($arResult["BASKET_ITEMS"]) > 0) { ?>
	<div class="small_basket_hover_block box<? if ($_REQUEST["SMALL_BASKET_OPEN"] == "Y") { echo ' active'; } ?>">
		<table class="small_basket_hover_table">
			<? foreach ($arResult["BASKET_ITEMS"] as $arItem) {
				?><tr>
					<td class="small_basket_hover_img">
						<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { echo '<a href="'.$arItem["DETAIL_PAGE_URL"].'" title="'.$arItem["NAME"].'">'; } ?>
							<? if (strlen($arResult["PRODUCTS_IMAGES"][$arItem["PRODUCT_ID"]]) > 0) {
								echo '<img class="radius5" src="'.$arResult["PRODUCTS_IMAGES"][$arItem["PRODUCT_ID"]].'" title="'.$arItem["NAME"].'" alt="'.$arItem["NAME"].'" />';
							} else {
								echo '<img src="'.SITE_TEMPLATE_PATH.'/images/no-img.png" title="'.$arItem["NAME"].'" alt="'.$arItem["NAME"].'" />';
							} ?>
						<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { echo '</a>'; } ?>
					</td>
					<td class="small_basket_hover_name">
						<div class="name"><? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { echo '<a href="'.$arItem["DETAIL_PAGE_URL"].'" title="'.$arItem["NAME"].'">'; } ?>
							<? echo $arItem["NAME"]; ?>
						<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { echo '</a>'; } ?></div>
						<div class="props">
							<? foreach ($arItem["PROPS"] as $arValue) {
								if ($arValue["CODE"] != "CATALOG.XML_ID" && $arValue["CODE"] != "PRODUCT.XML_ID") {
									echo '<b>'.$arValue["NAME"].':</b> '.$arValue["VALUE"].'<br />';
								}
							} ?>
						</div>
						<div class="small_basket_hover_price"><?=GetMessage("SF_SMALL_BASKET_PRICE");?>: <?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", $arItem["PRICE_FORMATED"]);?></div>
						<div class="item_quantity small_basket_hover_quantity">
							<a class="minus" href="javascript: void(0);">-</a><input type="text" value="<?=intVal($arItem["QUANTITY"]);?>" name="QUANTITY_<?=$arItem["ID"]?>" id="QUANTITY_<?=$arItem["ID"]?>"<?=(isset($arItem["AVAILABLE_QUANTITY"]) ? " data-max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "");?> /><a class="plus" href="javascript: void(0);">+</a>
						</div>
					</td>
					<td class="small_basket_hover_delete"><a href="javascript:void(0);" class="small_basket_hover_delete_action" data-id="<?=$arItem["ID"];?>">x</a></td>
				</tr><?
			} ?>
		</table>
		<a href="<?=$arParams["PATH_TO_BASKET"];?>" class="radius5 small_basket_hover_to_basket inline"><?=GetMessage("SF_SMALL_TO_BASKET");?></a>
		<a href="javascript: void(0);" class="radius5 button small_basket_hover_buy inline"><?=GetMessage("SF_SMALL_BUY");?></a>
		<div class="order_by_click">
			<label for="SMALL_BASKET_ORDER_PHONE"><?=GetMessage("SF_SMALL_BUY_LABEL");?></label>
			<input type="text" name="SMALL_BASKET_ORDER_PHONE" id="SMALL_BASKET_ORDER_PHONE" />
			<a href="javascript: void(0);" class="radius5 button small_basket_hover_buy_go inline"><?=GetMessage("SF_SMALL_BUY_GO");?></a>
		</div>
	</div>
<? } ?>