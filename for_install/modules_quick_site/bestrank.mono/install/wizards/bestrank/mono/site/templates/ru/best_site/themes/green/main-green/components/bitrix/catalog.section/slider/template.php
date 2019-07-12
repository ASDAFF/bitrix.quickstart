<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
 


<?if (count($arResult["ITEMS"] ) > 0):?>
<div class="viewed slider_wrapper"  id="<?=$arParams["SLIDER_ID"]?>_wrapper" >
	<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery('#<?=$arParams["SLIDER_ID"]?>').jcarousel({
	    	wrap: 'circular',
		scroll: 1,
		
	    });
	});	
		</script>
	 
	<?if(count($arResult["ITEMS"])<5){?>
	<style>
	.view-list #<?=$arParams["SLIDER_ID"]?>_wrapper .jcarousel-container-horizontal {
	    width: <?=(260*count($arResult["ITEMS"]))?>px;
	}
	</style>
	<?}?>

	<ul id="<?=$arParams["SLIDER_ID"]?>" class="slider jcarousel-skin-tango" >
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>

<?
	//echo "<pre>"; print_r($arElement); echo "</pre>";
?>

 

	<li class="R2D2" >
 



			<?if(is_array($arElement["PREVIEW_IMG"])):?>
				<div class="img" style="border:none;box-shadow:none;"><a  href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img  class="picture item_img"  border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a></div>
			<?elseif(is_array($arElement["PREVIEW_PICTURE"])):?>
				<div class="img" style="border:none;box-shadow:none;"><a  href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img  class="picture item_img"  border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a></div>
			<?else:?>
				
			<?endif?>
 




			<div class="info_div">
			 
			<h4><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="item_title" title="<?=$arElement["NAME"]?>">
				<span><?=TruncateText($arElement["NAME"], 120)?></span></a></h4>

			<?if($arElement["ARTNUMBER"]){?>
				<div class="article"><?=$arElement["ARTNUMBER"]["NAME"]?>: <?=$arElement["ARTNUMBER"]["VALUE"]?></div>
			<?}?>

				<!-- prices -->
				


				<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))  // Product has offers
				{
                    if ($arElement["MIN_PRODUCT_OFFER_PRICE"] > 0):
                    ?>
						<div class="price"><div class="item_price">
						<?if (count($arElement["OFFERS"]) > 1):?><?=GetMessage("CATALOG_PRICE_FROM")?>&nbsp;<?endif?>
						<?=$arElement["MIN_PRODUCT_OFFER_PRICE_PRINT"];?>
						</div></div>
					<?endif;?>
                    
					<?
				}
				else  // Product doesn't have offers
				{
				?>
					<?if ($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] < $arElement["MIN_PRODUCT_PRICE"] && $arElement["MIN_PRODUCT_PRICE"] > 0 && $arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0):?>
						<div class="price"><div class="item_price">
							<?if($arElement["MIN_PRODUCT_DISCOUNT_PRICE"] > 0):?><?=$arElement["MIN_PRODUCT_DISCOUNT_PRICE_PRINT"];?><?endif?>
							<s><?if($arElement["MIN_PRODUCT_PRICE"] > 0) echo $arElement["MIN_PRODUCT_PRICE_PRINT"];?></s>
						</div></div>
					<?elseif ($arElement["MIN_PRODUCT_PRICE"] > 0):?>
						<div class="price"><div class="item_price">
							<?if($arElement["MIN_PRODUCT_PRICE"] > 0) echo $arElement["MIN_PRODUCT_PRICE_PRINT"];?>
						</div></div>
					<?endif?>

					<? 
				}
				?>



			<div class="buy"><noindex>
				<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))  // Product has offers
				{
                    ?>
                    <a href="javascript:void(0)" class="buy_button bt3 addtoCart" id="catalog_add2cart_offer_link_<?=$arElement['ID']?>" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arElement["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arElement["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'cart');"><?echo GetMessage("CATALOG_BUY")?></a>
					<?
				}
				else  // Product doesn't have offers
				{
				?>
					

					<?if($arElement["CAN_BUY"]):?>
						<a href="<?echo $arElement["ADD_URL"]?>" rel="nofollow" class="bt3 addtoCart" onclick="return addToCart(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', 'noCart');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><?=GetMessage("CATALOG_BUY")?></a>
					<?elseif ( $arNotify[SITE_ID]['use'] == 'Y'):?>
						<?if ($USER->IsAuthorized()):?>
							<noindex><a href="<?echo $arElement["SUBSCRIBE_URL"]?>" rel="nofollow" class="bt2 subscribe_link" onclick="return addToSubscribe(this, '<?=GetMessage("CATALOG_IN_SUBSCRIBE")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?else:?>
                        	<noindex><a href="javascript:void(0)" rel="nofollow" class="subscribe_link" onclick="showAuthForSubscribe(this, <?=$arElement['ID']?>, '<?echo $arElement["SUBSCRIBE_URL"]?>')" id="catalog_add2cart_link_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?endif;?>
					<?endif;
				}
				?>

				<?if($arParams["DISPLAY_COMPARE"]):?>
				
					<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
					 
						<a class="bt4"  href="javascript:void(0)" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arElement["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arElement["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'compare');">
							<?=GetMessage("CATALOG_COMPARE")?>
						</a>
					 
					<?else:?>
					
						<a class="bt4"  href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow" onclick="return addToCompare(this, 'list', '<?=GetMessage("CATALOG_IN_COMPARE")?>', '<?echo $arElement["DELETE_COMPARE_URL"]?>');" id="catalog_add2compare_link_<?=$arElement['ID']?>">
							<?=GetMessage("CATALOG_COMPARE")?>
						</a>
					 
					<?endif?>
				
				<?endif?>

			</noindex></div><div style=" "></div>

 

			<?if(count($arElement["DISPLAY_PROPERTIES"])>0){?>
				<div class="properties">			
						<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?><br />
						<?endforeach?>
				</div>
			<?}?>
		 

	</div></li> 

	<?endforeach; ?>
</ul>
</div>
<?endif;?>

