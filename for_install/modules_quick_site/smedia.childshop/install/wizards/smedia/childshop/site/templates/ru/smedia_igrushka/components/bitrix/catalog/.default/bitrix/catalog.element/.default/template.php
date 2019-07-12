<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
<script type="text/javascript">
$(function() {
	$('.catalog-detail-image a').fancybox({
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 600,
		'speedOut': 200,
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
<?endif;?> 

<div class="cards">

	<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
	<div class="album">
	    <div class="image">
		<?if (is_array($arResult['DETAIL_PICTURE_350'])):?>
		    <div class="catalog-detail-image">
				<a rel="catalog-detail-images" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>">
				    <img itemprop="image" src="<?=$arResult['DETAIL_PICTURE_350']['SRC']?>" alt="<?=$arResult["NAME"]?>" id="catalog_detail_image" width="<?=$arResult['DETAIL_PICTURE_350']["WIDTH"]?>" height="<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>" />
				</a>
			<?if($arResult["PROPERTIES"]["SALELEADER"]["VALUE"]){?>
			<div class="hit"></div>
			<?}?>
		</div>
		<?endif;?>
		</div>
		<ul id="jq-album">
			<?if(count($arResult["MORE_PHOTO"])>0):
				foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
				<li class="catalog-detail-image">
				    <a rel="catalog-detail-images" href="<?=$PHOTO["SRC"]?>" title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $arResult["NAME"])?>">
					    <img border="0" src="<?=$PHOTO["SRC_PREVIEW"]?>" width="<?=$PHOTO["PREVIEW_WIDTH"]?>" height="<?=$PHOTO["PREVIEW_HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" />
					</a>
				</li>
			    <?endforeach;
			endif?>
		</ul>
	</div>
    <?endif;?>
	
	<div class="prices">
        <h2><?=$arResult["NAME"]?></h2>
		
			    <?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
						<div class="catalog-item-offers">
						<?foreach($arResult["OFFERS"] as $arOffer):?>
							<?if(!empty($arParams["OFFERS_FIELD_CODE"]) || !empty($arOffer["DISPLAY_PROPERTIES"])):?>
							<table cellspacing="0">
							<?foreach($arParams["OFFERS_FIELD_CODE"] as $field_code):?>
								<tr><td class="catalog-item-offers-field"><span><?echo GetMessage("IBLOCK_FIELD_".$field_code)?>:</span></td><td><?
										echo $arOffer[$field_code];?></td></tr>
							<?endforeach;?>
							
							<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
								<tr><td class="catalog-item-offers-field"><span><?=$arProperty["NAME"]?>:</span></td><td><?
									if(is_array($arProperty["DISPLAY_VALUE"]))
										echo implode(" / ", $arProperty["DISPLAY_VALUE"]);
									else
										echo $arProperty["DISPLAY_VALUE"];?></td></tr>
							<?endforeach?>
							</table>
							<?endif;?>
							<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
								<?if($arPrice["CAN_ACCESS"]):?>
									<div class="catalog-detail-price-offer" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer"><label><?=GetMessage("CATALOG_PRICE")?></label>&nbsp;&nbsp;
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<s><span itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span></s> <span class="catalog-price" itemprop = "price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
									<?else:?>
										<span class="catalog-price" itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span>
									<?endif?>
									</div>
								<?endif;?>
							<?endforeach;?>
							<div class="catalog-item-links">
							<?/*if($arParams["USE_COMPARE"]):?>
								<noindex>
								<a href="<?echo $arOffer["COMPARE_URL"]?>" class="catalog-item-compare" onclick="return addToCompare(this, '<?=GetMessage("CATALOG_IN_COMPARE")?>');" rel="nofollow" id="catalog_add2compare_link_ofrs_<?=$arOffer['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></a>
								</noindex>
							<?endif*/?>
							<?if($arOffer["CAN_BUY"]):?>
								<a href="<?echo $arOffer["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_detail_image', 'list', '<?=GetMessage("CATALOG_IN_BASKET")?>');" id="catalog_add2cart_link_ofrs_<?=$arOffer['ID']?>"><?echo GetMessage("CATALOG_ADD_TO_BASKET")?></a>
							<?elseif(count($arResult["CAT_PRICES"]) > 0):?>
								<span class="catalog-item-not-available"><?=GetMessage("CATALOG_NOT_AVAILABLE")?></span>
							<?endif?>
							</div>
							<div class="catalog-detail-line"></div>
						<?endforeach;?>
						</div>
				<?else:?>
        <form action="<?=SITE_DIR?>include/addToCartAjax.php" method="post" enctype="multipart/form-data">
        	<div class="block">
            		<div class="block_body">				
					<?$minPriseArr=array();
							foreach($arResult["PRICES"] as $code=>$arPrice):								
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
									<s style="padding:0;"><?=$minPriseArr["PRINT_VALUE"]?></s>
								<?else:?>
									<strong><?=$minPriseArr["PRINT_VALUE"]?></strong>
								<?endif;?>						
							</p>
					<?endif;?>		
					
					<?if(!$arResult["CAN_BUY"] && (count($arResult["PRICES"]) > 0)):?>
					<span class="catalog-item-not-available"><!--noindex--><?=GetMessage("CATALOG_NOT_AVAILABLE");?><!--/noindex--></span>
					<?endif;?>
				
				    <?if($arResult["CAN_BUY"]):?>				   		    
					     <div class="button">					   
					    	    <input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="&nbsp;" onclick="return addToCartForm(this.form, 'catalog_detail_image', 'detail', '<?=GetMessage("CATALOG_IN_BASKET")?>', this);" id="catalog_add2cart_link">
					    </div>				  
				    <?endif;?>
            		</div>
		</div>				
                    <?if($arParams["USE_COMPARE"] == "Y"):?>
					<div style="clear:both"></div>
					<div class="button_srav"><a href="<?=$arResult["COMPARE_URL"]?>" class="catalog-item-compare" onclick="return addToCompare(this, '<?=GetMessage("CATALOG_IN_COMPARE")?>');" rel="nofollow" id="catalog_add2compare_link"></a></div>
					<?endif;?>
				<?endif;?>
        
		<div style="clear:both"></div>
		
		<?
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
		if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):?>
			<br/>
			<div class="text">
			    <?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>							
					<?if(in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>
						<div class="propCaption"><?=$arProperty["NAME"]?>:&nbsp;</div>	
					<?else:?>
						<?=$arProperty["NAME"]?>:&nbsp;
					<?endif?>							
					<?if(is_array($arProperty["DISPLAY_VALUE"]) && in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>						
						<div style="float:left;" class="stylized_select select<?=$pid?>">
							<select size="1" id="select_<?=$arResult['ID']?>_<?=$pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" onchange="this.parentNode.getElementsByTagName('input')[0].value = this.options[this.selectedIndex].innerHTML;checkDisableAddToCart(<?=$massToJs?>, 'catalog_add2cart_link', <?=$arResult['ID']?>, 'detail', '/include/addToCartAjax.php');">							
								<?$selectedVal=0;?>
								<?foreach($arResult["PRODUCT_PROPERTIES"][$pid]["VALUES"] as $k => $v):
									if($k == $arResult["PRODUCT_PROPERTIES"][$pid]["SELECTED"])
										$selectedVal=$v;?>									
									<option value="<?echo $k?>" <?if($k == $arResult["PRODUCT_PROPERTIES"][$pid]["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
								<?endforeach;?>
							</select>
							<div class="input_wrapper">
								<input type="text" value="<?=$selectedVal?>" name="noname">
							</div>
						</div>
					<?elseif( in_array($pid, $arParams["PRODUCT_PROPERTIES"]) ):?>
						<input type="hidden" id="input_<?=$arResult['ID']?>_<?=$pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?=$arProperty['VALUE_ENUM_ID'][0]?>">						
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
		         <input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="ADD2BASKET">
		         <input type="hidden" name="ajax_buy" value="1">
		         <input type="hidden" name="AJAX_CALL" value="Y">
		         <input type="hidden" name="ADD_PROPS" value="<?=implode(",",$arParams["PRODUCT_PROPERTIES"])?>">
			<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">					      	 
		</div>
        <?endif;?>
       </form>
    </div>

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
			$APPLICATION->IncludeComponent("smedia:store.catalog.top", "", array(
				"IBLOCK_TYPE" => "",
				"IBLOCK_ID" => "",
				"ELEMENT_SORT_FIELD" => "sort",
				"ELEMENT_SORT_ORDER" => "desc",
				"ELEMENT_COUNT" => $arParams["ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
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
				"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
				"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],
				"SHARPEN" => $arParams["SHARPEN"],
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

    <div style="clear:both"></div>
	<?if($arResult["DETAIL_TEXT"]):?>
	<div class="textall">
		<?=$arResult["DETAIL_TEXT"];?>
	</div>
	<?endif;?>
	
</div>