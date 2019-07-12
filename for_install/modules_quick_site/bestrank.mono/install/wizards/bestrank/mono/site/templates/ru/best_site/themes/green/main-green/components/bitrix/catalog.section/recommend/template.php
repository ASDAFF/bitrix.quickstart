<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (is_object($arResult["NAV_RESULT"]) &&  is_subclass_of($arResult["NAV_RESULT"], "CAllDBResult"))
   $dbresult =& $arResult["NAV_RESULT"];
$pagesCount = $dbresult->NavPageCount;
if($pagesCount<$_REQUEST["PAGEN_1"]) {
	die;
}
?>
 

<?if (!empty($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]))
	$APPLICATION->SetTitle($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]);
else
	$APPLICATION->SetTitle($arResult["NAME"]);

if (!empty($arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"]))
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"]);
else
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["NAME"]);?>
<?
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>


<ul class="news lsnn">
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
 

	<li class="post R2D2"   id="<?=$this->GetEditAreaId($arElement['ID']);?>" style="">
			<?
			$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

			$sticker = "";
			if (array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"]))
			{
				foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
					if (array_key_exists($propertyCode, $arElement["PROPERTIES"]) && intval($arElement["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
					{
						$sticker .= "<div class=\"".ToLower($propertyCode)."\"></div>";
						//break;
					}
			}
			?>



			<?if(is_array($arElement["PREVIEW_IMG"])):?>
				<div class="img"><a class="link" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img class="picture item_img" border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a></div>
			<?elseif(is_array($arElement["PREVIEW_PICTURE"])):?>
				<div class="img"><a class="link" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img class="picture item_img" border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a></div>
			<?else:?>
				<div class="img"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:200px;" ></div></a></div>
			<?endif?>


			<div class="descr">			 
			<h3><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" title="<?=$arElement["NAME"]?>">
				<?=$arElement["NAME"]?></a></h3>


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
							<noindex><a href="<?echo $arElement["SUBSCRIBE_URL"]?>" rel="nofollow" class="subscribe_link" onclick="return addToSubscribe(this, '<?=GetMessage("CATALOG_IN_SUBSCRIBE")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?else:?>
                        	<noindex><a href="javascript:void(0)" rel="nofollow" class="subscribe_link" onclick="showAuthForSubscribe(this, <?=$arElement['ID']?>, '<?echo $arElement["SUBSCRIBE_URL"]?>')" id="catalog_add2cart_link_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?endif;?>
					<?endif;
				}
				?>

				<?if($arParams["DISPLAY_COMPARE"]):?>
				
					<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
					 
						<a class="bt4" href="javascript:void(0)" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arElement["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arElement["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'compare');">
							<?=GetMessage("CATALOG_COMPARE")?>
						</a>
					 
					<?else:?>
					
						<a class="bt4" href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow" onclick="return addToCompare(this, 'list', '<?=GetMessage("CATALOG_IN_COMPARE")?>', '<?echo $arElement["DELETE_COMPARE_URL"]?>');" id="catalog_add2compare_link_<?=$arElement['ID']?>">
							<?=GetMessage("CATALOG_COMPARE")?>
						</a>
					 
					<?endif?>
				
				<?endif?>

			</noindex>

		 

	</div>


		<?if($arElement["PREVIEW_TEXT"]):?>
			<p><?echo TruncateText(strip_tags($arElement["PREVIEW_TEXT"]), 50);?></p>
		<?endif;?>			

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

</div>



	</li>

 

	<?endforeach; ?>
</ul>
 
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>