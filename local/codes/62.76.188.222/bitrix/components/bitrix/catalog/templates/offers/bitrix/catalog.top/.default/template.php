<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-top">

<table cellpadding="0" cellspacing="0" border="0">
	<?foreach($arResult["ROWS"] as $arItems):?>
		<tr valign="top">
		<?foreach($arItems as $arElement):?>
		<?if(is_array($arElement)):?>
			<?
			$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
			?>
			<td width="<?=$arResult["TD_WIDTH"]?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td valign="top">
					<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
					<?endif?>
					</td>
					<td valign="top">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a><br />
						<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<small><?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?></small><br />
						<?endforeach?>
						<br />
						<?=$arElement["PREVIEW_TEXT"]?>
					</td>
				</tr>
				</table>
			</td>
		<?else:?>
			<td width="<?=$arResult["TD_WIDTH"]?>" rowspan="<?=$arResult["nRowsPerItem"]?>">
				&nbsp;
			</td>
		<?endif;?>
		<?endforeach?>
		</tr>
		<?if($arResult["bDisplayPrices"]):?>
			<tr valign="top">
			<?foreach($arItems as $arElement):?>
			<?if(is_array($arElement)):?>
				<td width="<?=$arResult["TD_WIDTH"]?>" class="data-cell">
				<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
					<?if($arPrice["CAN_ACCESS"]):?>
						<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
						<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
							<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
						<?else:?>
							<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
						<?endif?>
						</p>
					<?endif;?>
				<?endforeach;?>
				<?if(is_array($arElement["PRICE_MATRIX"])):?>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="data-table">
				<thead>
				<tr>
					<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) >= 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
						<td><?=GetMessage("CATALOG_QUANTITY") ?></td>
					<?endif;?>
					<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
						<td><?=$arType["NAME_LANG"] ?></td>
					<?endforeach?>
				</tr>
				</thead>
				<?foreach ($arElement["PRICE_MATRIX"]["ROWS"] as $ind => $arQuantity):?>
				<tr>
					<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) > 1 || count($arElement["PRICE_MATRIX"]["ROWS"]) == 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
						<th nowrap>
						<?if(IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
								echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
							elseif(IntVal($arQuantity["QUANTITY_FROM"]) > 0)
								echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
							elseif(IntVal($arQuantity["QUANTITY_TO"]) > 0)
								echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
							?>
						</th>
					<?endif;?>
					<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
						<td>
							<?if($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"])
								echo '<s>'.FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]).'</s> <span class="catalog-price">'.FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])."</span>";
							else
								echo '<span class="catalog-price">'.FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])."</span>";
							?>&nbsp;
						</td>
					<?endforeach?>
				</tr>
				<?endforeach?>
				</table>
				<?endif?>
				</td>
			<?endif;?>
			<?endforeach?>
			</tr>
		<?endif;?>
		<?if($arResult["bDisplayButtons"]):?>
			<tr valign="top">
			<?foreach($arItems as $arElement):?>
			<?if(is_array($arElement)):?>
				<td>
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex><a href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>&nbsp;</noindex>
				<?endif?>
				<?if($arElement["CAN_BUY"]):?>
					<input name="buy" type="button" value="<?= GetMessage("CATALOG_BUY") ?>" OnClick="window.location='<?echo CUtil::JSEscape($arElement["DETAIL_PAGE_URL"])."#buy"?>'" />
				<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
					<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
				<?endif?>
				</td>
			<?endif;?>
			<?endforeach?>
			</tr>
		<?endif;?>
	<?endforeach?>
</table>
</div>
