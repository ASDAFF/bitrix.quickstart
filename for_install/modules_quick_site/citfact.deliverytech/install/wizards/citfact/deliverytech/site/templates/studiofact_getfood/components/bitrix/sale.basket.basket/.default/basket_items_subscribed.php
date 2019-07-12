<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;

foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
	if (in_array($arHeader["id"], array("TYPE"))) {
		$bPriceType = true;
		continue;
	} elseif ($arHeader["id"] == "PROPS") {
		$bPropsColumn = true;
		continue;
	} elseif ($arHeader["id"] == "DELAY") {
		$bDelayColumn = true;
		continue;
	} elseif ($arHeader["id"] == "DELETE") {
		$bDeleteColumn = true;
		continue;
	} elseif ($arHeader["id"] == "WEIGHT") {
		$bWeightColumn = true;
	}
endforeach; ?>
<div id="basket_items_subscribed">
	<div class="basket_items_block">
		<? if ($subscribeCount > 0) { ?>
			<table class="basket_items_table radius5">
				<thead>
					<tr>
						<td class="itemName" colspan="2"><?=GetMessage("SALE_NAME");?></td>
						<td class="itemPrice"><?=GetMessage("SALE_PRICE");?></td>
						<td class="itemQuant"><?=GetMessage("SALE_QUANTITY");?></td>
						<td class="itemPrice"><?=GetMessage("SALE_SUM");?></td>
						<td class="itemAction"></td>
					</tr>
				</thead>
				<tbody>
					<? foreach ($arResult["ITEMS"]["ProdSubscribe"] as $key => $arItem) { ?>
						<tr>
							<td class="itemImage">
								<? if (strlen($arItem["PREVIEW_PICTURE"]) > 0) {
									$picture = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], Array("width" => 150, "height" => 150), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
									$picture = $picture["src"];
								} else if (strlen($arItem["DETAIL_PICTURE"]) > 0) {
									$picture = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], Array("width" => 150, "height" => 150), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
									$picture = $picture["src"];
								} else { $picture = SITE_TEMPLATE_PATH."/images/no-img.png"; } ?>
								<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>"><? } ?>
								<img src="<?=$picture;?>" title="<?=$arItem["NAME"];?>" />
								<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?></a><? } ?>
							</td>
							<td class="itemName">
								<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>"><? } ?>
								<?=$arItem["NAME"];?>
								<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?></a><? } ?>
									<? $t = false; foreach ($arItem["PROPS"] as $val):
										/*if (is_array($arItem["SKU_DATA"])) {
											$bSkip = false;
											foreach ($arItem["SKU_DATA"] as $propId => $arProp) {
												if ($arProp["CODE"] == $val["CODE"]) {
													$bSkip = true;
													break;
												}
											}
											if ($bSkip)
												continue;
										}*/
										if (!$t) { echo '<div class="itemNameProps">'; }
										$t = true;
										echo "<b>".$val["NAME"].":</b>&nbsp;<span>".$val["VALUE"]."</span><br/>";
									endforeach; ?>
								<? if ($t) { ?></div><? } ?>
							</td>
							<td class="itemPrice">
								<div class="basket_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", $arItem["PRICE_FORMATED"]);?></div>
								<? if ($arItem["DISCOUNT_PRICE"] > 0) { ?><div class="basket_old_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", $arItem["FULL_PRICE_FORMATED"]);?></div><? } ?>
							</td>
							<td class="itemQuant">
								<?=$arItem["QUANTITY"];
								if (isset($arItem["MEASURE_TEXT"]))
									echo "&nbsp;".$arItem["MEASURE_TEXT"]; ?>
							</td>
							<td class="itemPrice">
								<div class="basket_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", SaleFormatCurrency($arItem["PRICE"]*$arItem["QUANTITY"], $arItem["CURRENCY"]));?></div>
							</td>
							<td class="itemAction">
								<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"><?=GetMessage("SALE_DELETE")?></a>
							</td>
						</tr>
					<? } ?>
				</tbody>
			</table>
			<div class="basket_items_blocks radius5">
				<? foreach ($arResult["ITEMS"]["ProdSubscribe"] as $key => $arItem) { ?>
					<div class="basket_items_blocks_item">
						<div class="itemName">
							<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>"><? } ?>
							<?=$arItem["NAME"];?>
							<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?></a><? } ?>
						</div>
						<div class="itemImage">
							<? if (strlen($arItem["PREVIEW_PICTURE"]) > 0) {
								$picture = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], Array("width" => 350, "height" => 350), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
								$picture = $picture["src"];
							} else if (strlen($arItem["DETAIL_PICTURE"]) > 0) {
								$picture = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], Array("width" => 350, "height" => 350), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
								$picture = $picture["src"];
							} else { $picture = SITE_TEMPLATE_PATH."/images/no-img.png"; } ?>
							<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>"><? } ?>
							<img src="<?=$picture;?>" title="<?=$arItem["NAME"];?>" />
							<? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0) { ?></a><? } ?>
						</div>
							<? $t = false; foreach ($arItem["PROPS"] as $val):
								/*if (is_array($arItem["SKU_DATA"])) {
									$bSkip = false;
									foreach ($arItem["SKU_DATA"] as $propId => $arProp) {
										if ($arProp["CODE"] == $val["CODE"]) {
											$bSkip = true;
											break;
										}
									}
									if ($bSkip)
										continue;
								}*/
								if (!$t) { echo '<div class="itemNameProps">'; }
								$t = true;
								echo "<b>".$val["NAME"].":</b>&nbsp;<span>".$val["VALUE"]."</span><br/>";
							endforeach; ?>
						<? if ($t) { ?></div><? } ?>
						<? if ($arItem["DISCOUNT_PRICE_PERCENT"] > 0) {
							echo '<div class="itemDiscount"><b>'.GetMessage("SALE_DISCOUNT").':</b> '.$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"].'</div>';
						} ?>
						<div class="itemPrice">
							<b><?=GetMessage("SALE_PRICE");?>: </b>
							<div class="basket_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", $arItem["PRICE_FORMATED"]);?></div>
							<? if ($arItem["DISCOUNT_PRICE"] > 0) { ?><div class="basket_old_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", $arItem["FULL_PRICE_FORMATED"]);?></div><? } ?>
						</div>
						<div class="itemQuant">
							<b><?=GetMessage("SALE_QUANTITY");?>: </b>
							<?=$arItem["QUANTITY"];
							if (isset($arItem["MEASURE_TEXT"]))
								echo "&nbsp;".$arItem["MEASURE_TEXT"]; ?>
						</div>
						<div class="itemPrice">
							<b><?=GetMessage("SALE_SUM");?>: </b>
							<div class="basket_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), "<span class=\"rub black\">".GetMessage("STUDIOFACT_R")."</span>", SaleFormatCurrency($arItem["PRICE"]*$arItem["QUANTITY"], $arItem["CURRENCY"]));?></div>
						</div>
						<div class="itemAction">
							<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"><?=GetMessage("SALE_DELETE")?></a>
						</div>
						<br />
					</div>
				<? } ?>
			</div>
		<? } else {
			echo "<br />".GetMessage("SALE_NO_ITEMS");
		} ?>
	</div>
</div>