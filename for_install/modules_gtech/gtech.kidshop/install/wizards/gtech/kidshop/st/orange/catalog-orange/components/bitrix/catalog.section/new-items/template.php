<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--<pre><?print_r($arResult);?></pre>-->

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:10px 0;">
	<tr><td class="new-block-top">&nbsp;</td></tr>
	<tr><td class="new-block-bg">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?$count = -1;?>
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
<?$count++;?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr>
		<?endif;?>

		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%" id="<?=$this->GetEditAreaId($arElement['ID']);?>">

       <table class="content" cellspacing="5" cellpadding="0" border="0">
       <tr valign="middle">
<?if(is_array($arElement["PREVIEW_PICTURE"])||is_array($arElement["DETAIL_PICTURE"])):?>
<td>
<table width="110px" height="110px" cellspacing="0" cellpadding="0">
<tr><td colspan="3" height="8px" style="background: url('<?=$templateFolder?>/images/top1.png') left top no-repeat;"></td></tr>
<tr>
<td width="1px" style="background: url('<?=$templateFolder?>/images/left.png') top left repeat-y;"></td>
<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
          <?$arPicture = $arElement["PREVIEW_PICTURE"];
$arImg = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"]["ID"], Array("width"=>"100","height"=>"100"), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>


        <?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
          <?$arPicture = $arElement["DETAIL_PICTURE"];
$arImg = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"]["ID"], Array("width"=>"90","height"=>"90"), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>

        <?endif;?>
<td align="right" valign="bottom" style="background: url(<?=$arImg["src"]?>) center no-repeat;">

<?if($arElement["PROPERTIES"]["SPECIALOFFER"]["VALUE"][0]):?>
  <div class="sticker-spec"></div>
<?endif;?>

<a href="<?=$arPicture["SRC"];?>" class="zoom_pic" style="margin-right: 8px;"><img src="/upload/images/zoom.png" /></a>
</td>
<td width="1px;" style="background: url('<?=$templateFolder?>/images/right1.png') top right repeat-y;"></td>
</tr>
<tr height="8px" style="background: url('<?=$templateFolder?>/images/bot1.png') bottom left;"><td colspan="3"></td></tr>
</table>
<?if($arElement["CAN_BUY"]):?>
				<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arElement["PRODUCT_PROPERTIES"])):?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
					<table border="0" cellspacing="0" cellpadding="0">
					<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
						<tr>
<td colspan="2" style="padding-top: 5px;" valign="bottom"><input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="" class="buy">


<a href="<?echo $arElement["ADD_URL"]?>" class="tobasket" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=$templateFolder?>/images/basket.png" /></a>
<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
					<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arElement["ID"]?>">
</td>
						</tr>
					<?endif;?>
					<?foreach($arElement["PRODUCT_PROPERTIES"] as $pid => $product_property):?>
						<tr valign="top">
							<td><?echo $arElement["PROPERTIES"][$pid]["NAME"]?>:</td>
							<td>
							<?if(
								$arElement["PROPERTIES"][$pid]["PROPERTY_TYPE"] == "L"
								&& $arElement["PROPERTIES"][$pid]["LIST_TYPE"] == "C"
							):?>
								<?foreach($product_property["VALUES"] as $k => $v):?>
									<label><input type="radio" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"checked"'?>><?echo $v?></label><br>
								<?endforeach;?>
							<?else:?>
								<select name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]">
									<?foreach($product_property["VALUES"] as $k => $v):?>
										<option value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
									<?endforeach;?>
								</select>
							<?endif;?>
							</td>
						</tr>
					<?endforeach;?>
					</table>


					</form>
				<?else:?>
					<noindex>
					<a href="<?echo $arElement["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>&nbsp;<a href="<?echo $arElement["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
					</noindex>
				<?endif;?>
			<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>
 </td>
<?endif;?>

              <td align="left" valign="top">
        <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
                 <br />
<?if($arElement["PREVIEW_TEXT"]):
  $prev = $arElement["PREVIEW_TEXT"];
  if(strlen($prev)>200){
    $prev = substr($arElement["PREVIEW_TEXT"],0,200)."...";
  }
  echo $prev;
elseif($arElement["DETAIL_TEXT"]):
  $prev = $arElement["DETAIL_TEXT"];
  if(strlen($prev)>200){
    $prev = substr($arElement["DETAIL_TEXT"],0,200)."...";
  }
  echo $prev;
endif;?>
<br />
                 <?if(is_array($arElement["DISPLAY_PROPERTIES"])):?>
                   <?foreach($arElement["DISPLAY_PROPERTIES"] as $arProp):?>
                     <?=$arProp["NAME"]?> : <?=$arProp["VALUE"]?><br />
                   <?endforeach;?>
                 <?endif;?>
                 <br />
                    <?foreach($arElement["PRICES"] as $code=>$arPrice):?>
				<?if($arPrice["CAN_ACCESS"]):?>
					<p style="margin: 0; padding: 0;">
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<span class="catalog-price"><span class="text"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?> (<s><?=$arPrice["PRINT_VALUE"]?></s>)</span></span>
					<?else:?><span class="catalog-price"><span class="text"><?=$arPrice["PRINT_VALUE"]?></span></span><?endif;?>
					</p>
				<?endif;?>
			<?endforeach;?>
			<?if(is_array($arElement["PRICE_MATRIX"])):?>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="data-table">
				<thead>
				<tr>
					<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) >= 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
						<td valign="top" nowrap><?= GetMessage("CATALOG_QUANTITY") ?></td>
					<?endif?>
					<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
						<td valign="top" nowrap><?= $arType["NAME_LANG"] ?></td>
					<?endforeach?>
				</tr>
				</thead>
				<?foreach ($arElement["PRICE_MATRIX"]["ROWS"] as $ind => $arQuantity):?>
				<tr>
					<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) > 1 || count($arElement["PRICE_MATRIX"]["ROWS"]) == 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
						<th nowrap><?
							if (IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
								echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
							elseif (IntVal($arQuantity["QUANTITY_FROM"]) > 0)
								echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
							elseif (IntVal($arQuantity["QUANTITY_TO"]) > 0)
								echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
						?></th>
					<?endif?>
					<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
						<td><?
							if($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"]):?>
								<s><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])?></s><span class="catalog-price"><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]);?></span>
							<?else:?>
								<span class="catalog-price"><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]);?></span>
							<?endif?>&nbsp;
						</td>
					<?endforeach?>
				</tr>
				<?endforeach?>
				</table><br />
			<?endif?>
                  </td>
                  </tr>
            </table>
            <div style="margin-bottom: 10px;"></div>
		</td>

		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
			</tr>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
			<?while(($cell++)%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
				<td>&nbsp;</td>
			<?endwhile;?>
			</tr>
		<?endif?>

</table>

	<tr><td class="new-block-bottom">&nbsp;</td></tr>
</table>
<br/>