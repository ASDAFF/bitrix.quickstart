<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (count($arResult['ITEMS']) < 1)
{
	echo '<p style="clear:both;">';
	ShowNote(GetMessage('EMPTY_RESULT'));
	echo "</p>";
	return;
}

$massToJs='[';
$i=1;
foreach($arParams["PRODUCT_PROPERTIES"] as $pid)
{
	$massToJs.="'".$pid."'";
	if($i!=count($arParams["PRODUCT_PROPERTIES"]))
		$massToJs.=",";
	$i++;
}
$massToJs.=']';
?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<div class="paginator" style="float:right;"><?=$arResult["NAV_STRING"];?></div>
<?endif;?>
<div style="clear:both;"></div>

<table class="list_card" width="100%" cellspacing="0" cellspadding="0">
<?$i=0;
foreach ($arResult['ITEMS'] as $key => $arElement):

	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));

	$bHasPicture = is_array($arElement['PREVIEW_IMG']);

	$sticker = "";
	if (array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"]))
	{
		foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
			if (array_key_exists($propertyCode, $arElement["PROPERTIES"]) && intval($arElement["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
				$sticker .= "&nbsp;<span class=\"sticker\">".$arElement["PROPERTIES"][$propertyCode]["NAME"]."</span>";
	}

    $i++;?>
<?=($i%2>0 ? "<tr>" : "")?>
<td width="50%" class="item <?=($i%2>0 ? "itemLeft" : "itemRight")?>">
<div id="<?=$this->GetEditAreaId($arElement['ID']);?>" itemscope itemtype = "http://schema.org/Product" class="<?=($i%2>0 ? "left" : "right")?>">
<div class="item_body">
    
	<div class="image">
	<?if($bHasPicture):?>
	    <a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
		    <img itemprop="image" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" />
		</a>
	<?endif;?>
		<?if($arElement["PROPERTIES"]["SALELEADER"]["VALUE"]){?>
		<div class="hit"></div>
		<?}?>
	</div>
<form action="<?=SITE_DIR?>include/addToCartAjax.php" method="post" enctype="multipart/form-data">	
	<div class="info">
        <h2><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" itemprop="name"><?=$arElement["NAME"]?></a></h2>
		<?if (is_array($arElement['DISPLAY_PROPERTIES']) && count($arElement['DISPLAY_PROPERTIES']) > 0):?>		    
		    <?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<?if(in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>
					<div class="propCaption"><?=$arProperty["NAME"]?>:&nbsp;</div>	
				<?else:?>
					<?=$arProperty["NAME"]?>:&nbsp;
				<?endif?>	
				<?if(is_array($arProperty["DISPLAY_VALUE"]) && in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>						
						<div style="float:left;" class="stylized_select select<?=$pid?>">
							<select id="select_<?=$arElement['ID']?>_<?=$pid?>" size="1" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" onchange="this.parentNode.getElementsByTagName('input')[0].value = this.options[this.selectedIndex].innerHTML;checkDisableAddToCart(<?=$massToJs?>, 'catalog_add2cart_link_<?=$arElement['ID']?>', <?=$arElement['ID']?>, 'list', '/include/addToCartAjax.php');">							
								<?$selectedVal=0;?>
								<?foreach($arElement["PRODUCT_PROPERTIES"][$pid]["VALUES"] as $k => $v):
									if( ($_REQUEST['arrFilter_pf'][$pid] && $k==$_REQUEST['arrFilter_pf'][$pid]) || (!$_REQUEST['arrFilter_pf'][$pid] && $k == $arElement["PRODUCT_PROPERTIES"][$pid]["SELECTED"]))
										$selectedVal=$v;?>									
									<option value="<?echo $k?>" <?if( (!$_REQUEST['arrFilter_pf'][$pid] &&  $k == $arElement["PRODUCT_PROPERTIES"][$pid]["SELECTED"] ) || ($_REQUEST['arrFilter_pf'][$pid] && $k==$_REQUEST['arrFilter_pf'][$pid])) echo '"selected"'?>><?echo $v?></option>
								<?endforeach;?>
							</select>
							<div class="input_wrapper">
								<input type="text" value="<?=$selectedVal?>" name="noname">
							</div>
						</div>
					<?elseif( in_array($pid, $arParams["PRODUCT_PROPERTIES"]) ):?>
						<input type="hidden" id="input_<?=$arElement['ID']?>_<?=$pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?=$arProperty['VALUE_ENUM_ID'][0]?>">						
						<div class="propCaption"><?echo $arProperty["DISPLAY_VALUE"];?></div>
					<?elseif(is_array($arProperty["DISPLAY_VALUE"])):		       
			        		echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
		       			elseif($pid=="MANUAL"):?>
						<a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a>
                	<?else:
			        echo $arProperty["DISPLAY_VALUE"];
		        endif;?>
		       <?if(in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>
				<div style="clear:both"></div>
			<?else:?>
				<br />
			<?endif?>	
	        <?endforeach;?>
	        
        <?endif;?>
        <p class="more"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=GetMessage("CATALOG_READ_MORE")?></a></p>
    </div>
    <div style="clear:both"></div>
    <div class="priceSravBlock">
	<?if(empty($arElement["OFFERS"])):?>
		<!--noindex-->
		<?if($arParams["DISPLAY_COMPARE"]):?>
		<div class="button_srav">
			<a href="<?echo $arElement["COMPARE_URL"]?>" class="catalog-item-compare" onclick="return addToCompare(this, '<?=GetMessage("CATALOG_IN_COMPARE")?>');" rel="nofollow" id="catalog_add2compare_link_<?=$arElement['ID']?>"></a>
		</div>
		<?endif;?>
		<!--noindex-->
	<?endif;?>

	<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
				<div class="catalog-item-offers">
				<?$i=0;?>
				<?foreach($arElement["OFFERS"] as $arOffer):?>
					<?if($i != 0):?>
					<div class="catalog-detail-line"></div>
					<?endif;?>
					<?$i++;?>
					<div class="catalog-item-links">	
					<?if($arOffer["CAN_BUY"]):?>
						<a href="<?echo $arOffer["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_ofrs_<?=$arOffer['ID']?>"><?echo GetMessage("CATALOG_ADD")?></a>
					<?elseif(count($arResult["PRICES"]) > 0):?>
						<span class="catalog-item-not-available"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></span>
					<?endif?>
					<?if($arParams["DISPLAY_COMPARE"]):?>
						<a href="<?echo $arOffer["COMPARE_URL"]?>" class="catalog-item-compare" onclick="return addToCompare(this, '<?=GetMessage("CATALOG_IN_COMPARE")?>');" rel="nofollow" id="catalog_add2compare_link_ofrs_<?=$arOffer['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></a>
					<?endif?>
					</div>
					<div class="table-offers">
					<?if(!empty($arParams["OFFERS_FIELD_CODE"]) || !empty($arOffer["DISPLAY_PROPERTIES"])):?>
					<table cellspacing="0">
					<?foreach($arParams["OFFERS_FIELD_CODE"] as $field_code):?>
						<tr><td class="catalog-item-offers-field"><span><?echo GetMessage("IBLOCK_FIELD_".$field_code)?>:</span></td><td><?
								echo $arOffer[$field_code];?></td></tr>
					<?endforeach;?>
					<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
						<tr><td class="catalog-item-offers-prop"><span><?=$arProperty["NAME"]?>:</td><td><?
							if(is_array($arProperty["DISPLAY_VALUE"]))
								echo implode(" / ", $arProperty["DISPLAY_VALUE"]);
							else
								echo $arProperty["DISPLAY_VALUE"];?></td></tr>
					<?endforeach?>
					</table>
					<?endif;?>
					<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
						<?if($arPrice["CAN_ACCESS"]):?>
							<div class="catalog-item-price" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">
							<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<span class="catalog-item-price" itemprop = "price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span> <s><span itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span></s>
							<?else:?>
								<span class="catalog-item-price" itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span>
							<?endif?>
							</div>
						<?endif;?>
					<?endforeach;?>
					</div>
				<?endforeach;?>
				</div>
	<?else:?>
		<?$minPriseArr=array();
							foreach($arElement["PRICES"] as $code=>$arPrice):								
								if($arPrice["CAN_ACCESS"]):
									if($minPriseArr["VALUE"]>$arPrice["VALUE"] || !$minPriseArr["VALUE"])
										$minPriseArr=$arPrice;								
								endif;
					endforeach;
			if($minPriseArr):?>				
					<p class="price">
						<?=GetMessage("CATALOG_PRICE")?>						
						<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
							<strong><?=$minPriseArr["PRINT_DISCOUNT_VALUE"]?></strong> 
							<br/><s style="padding:0;"><?=$minPriseArr["PRINT_VALUE"]?></s>
						<?else:?>
							<strong><?=$minPriseArr["PRINT_VALUE"]?></strong>
						<?endif;?>						
					</p>
			<?endif;?>			
	<?endif?>
</div>
<div class="buttonPropBlock">
	<?if(empty($arElement["OFFERS"])):?>		
			<div class="button">
				<?if ($arElement['CAN_BUY']):?>
					<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" class="catalog-item-buy" value="&nbsp;" onclick="return addToCartForm(this.form, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>', this);" id="catalog_add2cart_link_<?=$arElement['ID']?>">
				<?elseif (count($arResult["PRICES"]) > 0):?>
					<?=GetMessage('CATALOG_NOT_AVAILABLE')?>
				<?endif;?>
			</div>
			 <input type="hidden" name="ajax_buy" value="1">
			 <input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="ADD2BASKET">
		         <input type="hidden" name="AJAX_CALL" value="Y">
		         <input type="hidden" name="ADD_PROPS" value="<?=implode(",",$arParams["PRODUCT_PROPERTIES"])?>">
			<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arElement["ID"]?>">	
	<?endif;?>	
</div>	
<div style="clear:both"></div>
</form>
</div>
</div>    

</td>
<?if($i%2==0):?>
	</tr><tr><td class="itemBotBg"></td><td class="itemBotBgRight"></td></tr><tr><td class="space"></td><td class="space"></td></tr>
<?endif?>
<?endforeach;?>
<?if($i%2!=0):?>
	</tr><tr><td class="itemBotBg"></td><td></td></tr><tr><td class="space"></td><td class="space"></td></tr>
<?endif?>    
</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<div class="paginator"><?=$arResult["NAV_STRING"];?></div>
<?endif;?>
<?$arResult['TEST']="test"?>