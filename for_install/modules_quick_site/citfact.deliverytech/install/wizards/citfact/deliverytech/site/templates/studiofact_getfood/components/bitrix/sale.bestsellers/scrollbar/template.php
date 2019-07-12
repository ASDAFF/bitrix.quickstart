<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
if (count($arResult["ITEMS"]) < 1) { return; }
if (count($arResult["ITEMS"]) > 0) {
	$rand = rand();
	?><div class="section_box">
		<? if (strlen($arParams["SECTION_NAME"]) > 0) { ?><div class="scrollSectionName"><?=$arParams["SECTION_NAME"];?></div><? } ?>
		<div class="scroll-standard">
			<div class="section" id="section_<?=$rand;?>"><?
				$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
				$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
				$arElementDeleteParams = array("CONFIRM" => GetMessage("CT_BCS_TPL_ELEMENT_DELETE_CONFIRM"));
				foreach ($arResult["ITEMS"] as $arItem) {
					$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $strElementEdit);
					$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $strElementDelete, $arElementDeleteParams);
					$strMainID = $this->GetEditAreaId($arItem["ID"]);
					$can_buy = 0;
					if ($arItem["CAN_BUY"] == "1" && count($arItem["OFFERS"]) < 1) { $can_buy = 1; }
					if (count($arItem["OFFERS"]) > 0) {
						foreach ($arItem["OFFERS"] as $arOffer) {
							if ($arOffer["CAN_BUY"] == "1") { $can_buy = 1; }
						}
					}
					?><div class="item_element good_box inline" id="<?=$strMainID;?>" data-id="<?=$arItem["ID"];?>">
						<div style="display: none;" itemscope itemtype="http://schema.org/Product">
							<meta itemprop="name" content="<?=$arItem["NAME"];?>" />
							<meta itemprop="description" content="<?=$arItem["PREVIEW_TEXT"];?>" />
							<meta itemprop="url" content="<?=$arItem["DETAIL_PAGE_URL"];?>" />
							<img itemprop="image" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" />
						</div>
						<div class="hover_box box<? if ($can_buy != "1") { echo ' disabled'; } ?>">
							<div class="icon_box"><?
								if (strlen($arItem["DISPLAY_PROPERTIES"]["NEWPRODUCT"]["VALUE"]) > 0) { echo '<div class="'.mb_strtolower($arItem["DISPLAY_PROPERTIES"]["NEWPRODUCT"]["CODE"]).'" title="'.$arItem["DISPLAY_PROPERTIES"]["NEWPRODUCT"]["NAME"].'"></div>'; }
								if (strlen($arItem["DISPLAY_PROPERTIES"]["SALELEADER"]["VALUE"]) > 0) { echo '<div class="'.mb_strtolower($arItem["DISPLAY_PROPERTIES"]["SALELEADER"]["CODE"]).'" title="'.$arItem["DISPLAY_PROPERTIES"]["SALELEADER"]["NAME"].'"></div>'; }
								if (strlen($arItem["DISPLAY_PROPERTIES"]["SPECIALOFFER"]["VALUE"]) > 0) { echo '<div class="'.mb_strtolower($arItem["DISPLAY_PROPERTIES"]["SPECIALOFFER"]["CODE"]).'" title="'.$arItem["DISPLAY_PROPERTIES"]["SPECIALOFFER"]["NAME"].'"></div>'; }
							?></div>
							<div class="img_box">
								<a href="<?=$arItem["DETAIL_PAGE_URL"];?>?open_popup=Y" class="zoom open_fancybox" rel="gallery"><?=GetMessage("STUDIOFACT_FAST_VIEW");?></a>
								<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>" class="image" <? if (strlen($arItem["PREVIEW_PICTURE"]["SRC"]) > 0) { ?>style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>');"<? } ?>></a>
							</div>
							<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" title="<?=$arItem["NAME"];?>" class="name"><?=$arItem["NAME"];?></a>
							<div class="price_box">
								<div class="fl"><span class="price"><? if (count($arItem["OFFERS"]) > 0) { echo GetMessage("SF_ISSET_OFFERS"); } ?><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?></span></div>
								<? if ($arItem["MIN_PRICE"]["DISCOUNT_DIFF"] > 0) { ?><div class="fl"><span class="old_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arItem["MIN_PRICE"]["PRINT_VALUE"]);?></span></div><? } ?>
								<? if ($can_buy == "1") { ?>
									<div class="buy_box fr">
										<a href="<?=$arItem["ADD_URL"];?>" class="buy <? if (count($arItem["OFFERS"]) > 0) { echo 'show_offers_basket_popup'; } else { echo 'show_basket_popup'; } ?> inline" data-name="<?=$arItem["NAME"];?>" data-img="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" data-id="<?=$arItem["ID"];?>" data-basket="<?=$arParams["BASKET_URL"];?>" data-price="<?=str_replace(GetMessage("STUDIOFACT_RUB"), '', $arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?>" data-gotobasket="<?=GetMessage("SF_GO_TO_BASKET_BUTTON");?>" data-gotoback="<?=GetMessage("SF_GO_TO_BACK_BUTTON");?>">
											<span class="buy_popup"><span></span><?=('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));?></span>
										</a>
									</div>
								<? } else {
									?><div class="buy_box fr"><?if (strlen($arParams["MESS_NOT_AVAILABLE"]) > 0) { echo $arParams["MESS_NOT_AVAILABLE"]; } else { echo GetMessage("CT_BCE_CATALOG_NOT_AVAILABLE"); } ?></div><?
								} ?>
								<div class="clear"></div>
							</div>
							<div class="scrollbar_offers_items">
								<? if (count($arItem["OFFERS"]) > 0) {
									?><div class="offers_item" id="skuId<?=$arItem["ID"];?>">
										<?
										foreach ($arResult["SKU_PROPS"] as $arSku) {
											if (count($arSku["VALUES"]) > 0 && count($arItem["SKU_THERE_ARE"][$arSku["ID"]]) > 0) {
												echo '<div class="offer_item" data-prop-id="'.$arSku["ID"].'"><div class="offer_name">'.$arSku["NAME"].':</div>';
													foreach ($arSku["VALUES"] as $value) {
														if ($value["ID"] > 0 && in_array($value["ID"], $arItem["SKU_THERE_ARE"][$arSku["ID"]])) {
															?><span class="offer_sku" data-prop-id="<?=$arSku["ID"];?>" data-prop-code="<?=$arSku["CODE"];?>" data-prop-value-id="<?=$value["ID"];?>" data-tree='<?=json_encode($arItem["SKU_TREE"]);?>'><?=(strlen($value["PICT"]["SRC"]) > 0 ? '<img src="'.$value["PICT"]["SRC"].'" title="'.$value["NAME"].'" alt="'.$value["NAME"].'" />' : $value["NAME"]);?></span><?
														}
													}
												echo '</div>';
											}
										}
										echo '<div class="offers_item_id" style="display: none;">';
											foreach ($arItem["SKU_MASSIVE"] as $id => $value) {
												?><div class="<?=$id;?>" data-id="<?=$value;?>"></div><?
											}
										echo '</div>';
										?>
									</div><?
								} ?>
								<div class="price_box main_preview_price offers_hide">
									<div class="pr">
										<span class="price"><? if (count($arItem["OFFERS"]) > 0) { echo GetMessage("SF_ISSET_OFFERS"); } ?><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?></span>
										<? if ($arItem["MIN_PRICE"]["DISCOUNT_DIFF"] > 0) { ?><span class="old_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arItem["MIN_PRICE"]["PRINT_VALUE"]);?></span><? } ?>
									</div>
									<? if ($arItem["CAN_BUY"]) { ?>
										<div class="nav_buttons"><a href="<?=$arItem["ADD_URL"];?>" target="_parent" class="button show_basket_popup inline" data-name="<?=$arItem["NAME"];?>" data-img="<?=$arItem["PREVIEW_PICTURE"]["SRC"];?>" data-id="<?=$arItem["ID"];?>" data-basket="<?=$arParams["BASKET_URL"];?>" data-price="<?=str_replace(GetMessage("STUDIOFACT_RUB"), '', $arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?>" data-gotobasket="<?=GetMessage("SF_GO_TO_BASKET_BUTTON");?>" data-gotoback="<?=GetMessage("SF_GO_TO_BACK_BUTTON");?>"><?=('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));?></a><a href="javascript: void(0);" class="button_white"><?=GetMessage("SF_GO_TO_BACK_BUTTON");?></a></div>
									<? } else {
										?><div class="nav_buttons"><?if (strlen($arParams["MESS_NOT_AVAILABLE"]) > 0) { echo $arParams["MESS_NOT_AVAILABLE"]; } else { echo GetMessage("CT_BCE_CATALOG_NOT_AVAILABLE"); } ?><a href="javascript: void(0);" class="button_white"><?=GetMessage("SF_GO_TO_BACK_BUTTON");?></a></div><?
									} ?>
								</div>
								<? if (count($arItem["OFFERS"]) > 0) {
									foreach ($arItem["OFFERS"] as $arOffer) { ?>
										<div class="price_box main_preview_price_<?=$arOffer["ID"];?> offers_hide" style="display: none;">
										<div class="pr">
											<span class="price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?></span>
											<? if ($arOffer["MIN_PRICE"]["DISCOUNT_DIFF"] > 0) { ?><span class="old_price"><?=str_replace(GetMessage("STUDIOFACT_RUB"), '<span class="rub">'.GetMessage("STUDIOFACT_R").'</span>', $arOffer["MIN_PRICE"]["PRINT_VALUE"]);?></span><? } ?>
										</div>
										<? if ($arOffer["CAN_BUY"]) { ?>
											<div class="nav_buttons"><a href="<?=$arOffer["ADD_URL"];?>" target="_parent" class="button show_basket_popup inline" data-name="<?=$arOffer["NAME"];?>" data-img="<?=(strlen($arOffer["PREVIEW_PICTURE"]["SRC"]) > 0 ? $arOffer["PREVIEW_PICTURE"]["SRC"] : $arItem["PREVIEW_PICTURE"]["SRC"]);?>" data-id="<?=$arOffer["ID"];?>" data-basket="<?=$arParams["BASKET_URL"];?>" data-price="<?=str_replace(GetMessage("STUDIOFACT_RUB"), '', $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]);?>" data-gotobasket="<?=GetMessage("SF_GO_TO_BASKET_BUTTON");?>" data-gotoback="<?=GetMessage("SF_GO_TO_BACK_BUTTON");?>"><?=('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCE_CATALOG_BUY'));?></a><a href="javascript: void(0);" class="button_white"><?=GetMessage("SF_GO_TO_BACK_BUTTON");?></a></div>
										<? } else {
											?><div class="nav_buttons"><?if (strlen($arParams["MESS_NOT_AVAILABLE"]) > 0) { echo $arParams["MESS_NOT_AVAILABLE"]; } else { echo GetMessage("CT_BCE_CATALOG_NOT_AVAILABLE"); } ?><a href="javascript: void(0);" class="button_white"><?=GetMessage("SF_GO_TO_BACK_BUTTON");?></a></div><?
										} ?>
										</div>
									<? }
								} ?>
							</div>
						</div>
					</div><?
				}
			?></div>
		</div>
		<div class="slide_scroll_left"></div>
		<div class="slide_scroll_right"></div>
	</div>

	<script type="text/javascript">
		$(document).ready(function () { adaptItemScroll ($("#section_<?=$rand;?>")); });
		$(window).resize(function() { adaptItemScroll ($("#section_<?=$rand;?>")); });
	</script>
<? } ?>