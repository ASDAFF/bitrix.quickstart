<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript">
$(function() {
	$('a.zoom_pic').fancybox({
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 600,
		'speedOut': 400,
		'overlayShow': false,
		'cyclic' : true,
		'padding': 20,
		'titlePosition': 'over',
		'onComplete': function() {
			$("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
		}
	});
});
</script>

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
<td valign="top">
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
<td align="right" valign="bottom" style="background: url(<?=$arImg["src"]?>) center no-repeat;" height="90px">

<?if($arElement["PROPERTIES"]["SPECIALOFFER"]["VALUE"][0]):?>
  <div class="sticker-spec"></div>
<?endif;?>

<a href="<?=$arPicture["SRC"];?>" class="zoom_pic" style="margin-right: 8px;"><img src="/upload/images/zoom.png" /></a>
</td>
<td width="1px;" style="background: url('<?=$templateFolder?>/images/right1.png') top right repeat-y;"></td>
</tr>
<tr height="8px" style="background: url('<?=$templateFolder?>/images/bot1.png') bottom left no-repeat;"><td colspan="3"></td></tr>
</table>
<?if($arElement["CAN_BUY"]):?>
				<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arElement["PRODUCT_PROPERTIES"])):?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
					<table border="0" cellspacing="0" cellpadding="0">
					<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
						<tr>
<td colspan="1" style="padding-top: 5px;" valign="bottom"><input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="" class="buy" title="<?=GetMessage("CATALOG_BUY")?>" >&nbsp;
</td><td valign="bottom"><a href="<?echo $arElement["ADD_URL"]?>" class="tobasket-list" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list','','<?=$count?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=$templateFolder?>/images/basket.png" title="<?=GetMessage("CATALOG_ADD")?>" /></a>
<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"];?>" value="BUY">
					<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arElement["ID"]?>">
</td>
						</tr>
					<?endif;?>
					</table>


					</form>
				<?else:?>
					<noindex>
					<a href="<?echo $arElement["ADD_URL"]?>" class="tobasket" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=$templateFolder?>/images/basket.png" /></a>
					</noindex>
				<?endif;?>
			<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>
</td>
<td width="5px"></td>
<?endif;?>

              <td align="left" valign="top">
        <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
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
						<span class="catalog-price2"><span class="text"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?> (<s><?=$arPrice["PRINT_VALUE"]?></s>)</span></span>
					<?else:?><span class="catalog-price2"><span class="text"><?=$arPrice["PRINT_VALUE"]?></span></span><?endif;?>
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