<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $count;?>
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

<table cellpadding="0" cellspacing="0" border="0" width="100%" id="tableHeight" height="160px">
	<tr><td class="secel-block-top">&nbsp;</td></tr>
	<tr><td class="secel-block-bg" valign="top">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?if(empty($arResult["ITEMS"])){?>
  <div style="float:left; width: 5px; height: 142px;"></div>
<?}?>
<?$count = -1;?>
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
<?$count++;?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr valign="top">
		<?endif;?>

		<td width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%" id="<?=$this->GetEditAreaId($arElement['ID']);?>">

       <table class="content" cellspacing="5" cellpadding="0" border="0">
       <tr>
<?if(is_array($arElement["PREVIEW_PICTURE"])||is_array($arElement["DETAIL_PICTURE"])):?>
<td valign="top">
<table width="110px" height="110px" cellspacing="0" cellpadding="0">
<tr><td colspan="3" height="8px" style="background: url('<?=$templateFolder?>/images/top1.png') left top no-repeat;"></td></tr>
<tr>
<td width="1px" style="background: url('<?=$templateFolder?>/images/left.png') top left repeat-y;"></td>
<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
          <?$arPicture = $arElement["DETAIL_PICTURE"];
$arImg = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"]["ID"], Array("width"=>"90","height"=>"90"), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>

        <?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
          <?$arPicture = $arElement["DETAIL_PICTURE"];
$arImg = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"]["ID"], Array("width"=>"90","height"=>"90"), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>

        <?endif;?>
<td align="right" valign="bottom" style="background: url(<?=$arImg["src"]?>) center no-repeat;" height="90px">
	<div style="position:relative;">
		<div class="buy-btn"><a href="<?echo $arElement["ADD_URL"]?>" class="tobasket-list" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list','','<?=$count?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=$templateFolder?>/images/basket.png" title="Добавить в корзину" /></a></div>
		<div class="zoom-btn"><a href="<?=$arPicture["SRC"];?>" class="zoom_pic" style="margin-right: 8px;"><img src="/upload/images/zoom.png" title="Увеличить" /></a></div>
	</div>

<?if($arElement["PROPERTIES"]["SPECIALOFFER"]["VALUE"][0]):?>
  <div class="sticker-spec"></div>
<?endif;?>

</td>
<td width="1px;" style="background: url('<?=$templateFolder?>/images/right1.png') top right repeat-y;"></td>
</tr>
<tr height="8px" style="background: url('<?=$templateFolder?>/images/bot1.png') bottom left no-repeat;"><td colspan="3"></td></tr>
</table>
<?if($arElement["CAN_BUY"]):?>
				<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arElement["PRODUCT_PROPERTIES"])):?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
					<table border="0" cellspacing="0" cellpadding="0">
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
				<?endif;?>
			<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>
 </td>
<?endif;?>

              <td align="left" valign="top">
<?if(strlen($arElement["NAME"])>50){?>
        <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=substr($arElement["NAME"],0,47)."...";?></a>
<?}else{?>
        <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"];?></a>
<?}?>
                 <br />
                 <?if(is_array($arElement["DISPLAY_PROPERTIES"])):?>
                   <?foreach($arElement["DISPLAY_PROPERTIES"] as $arProp):?>
                     <?=$arProp["NAME"]?> : <?=$arProp["VALUE"]?><br />
                   <?endforeach;?>
                 <?endif;?>
                 <br />
                    <?foreach($arElement["PRICES"] as $code=>$arPrice):?>
				<?if($arPrice["CAN_ACCESS"]):?>
					<p style="margin:0; padding:0; font-family:tahoma; font-size:14px;">
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<?=GetMessage("ELEMENT_PRICE")?>: <span class="catalog-price"><?=$arPrice["DISCOUNT_VALUE"]?> <span style="font-family:rouble;">c</span> (<s><?=$arPrice["VALUE"]?> <span style="font-family:rouble;">c</span></s>)</span>
					<?else:?><?=GetMessage("ELEMENT_PRICE")?>: <span class="catalog-price"><?=$arPrice["VALUE"]?> <span style="font-family:rouble;">c</span></span><?endif;?>
					</p>
				<?endif;?>
			<?endforeach;?>
                  </td>
                  </tr>
            </table>

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
	<tr><td class="secel-block-bottom">&nbsp;</td></tr>
</table>
<br/>