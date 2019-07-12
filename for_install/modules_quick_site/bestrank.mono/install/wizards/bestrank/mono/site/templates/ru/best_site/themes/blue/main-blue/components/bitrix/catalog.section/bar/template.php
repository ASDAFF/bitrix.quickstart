<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (is_object($arResult["NAV_RESULT"]) &&  is_subclass_of($arResult["NAV_RESULT"], "CAllDBResult"))
   $dbresult =& $arResult["NAV_RESULT"];
$pagesCount = $dbresult->NavPageCount;
if($pagesCount<$_REQUEST["PAGEN_1"]) {
	die;
}
?>

<?
	$curPage = $APPLICATION->GetCurPage(true);
?>

<?if (!empty($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]))
	$APPLICATION->SetTitle($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]);
elseif($arResult["NAME"])
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
<div class="listitem">

<ul class="lsnn">
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>

<?
	//echo "<pre>"; print_r($arElement); echo "</pre>";
?>

	<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0 || $cell==0){?>
		<li class="sep"></li>
	<?}?>

	<li class="itembg R2D2" style="width: <?=(intval(100/$arParams["LINE_ELEMENT_COUNT"])-1)?>%; <?=(($cell+1)%$arParams["LINE_ELEMENT_COUNT"]==0 ? 'padding-right: 0;' : 'padding-right: 1%;' )?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>" style="">
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



			<div class="img_div tac vam" style="width: <?=$arParams["DISPLAY_IMG_WIDTH"]?>px; height: <?=$arParams["DISPLAY_IMG_HEIGHT"]?>px;"><?if(is_array($arElement["PREVIEW_IMG"])):?>
				<a class="link" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img class="item_img" border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
			<?elseif(is_array($arElement["PREVIEW_PICTURE"])):?>
				<a class="link" href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img class="item_img" border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
			<?else:?>
				<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:200px;" ></div></a>
			<?endif?>

		<?if(!(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) && !$arElement["CAN_BUY"]):?>
        <div class="badge notavailable"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></div>
		<?elseif (strlen($sticker)>0):?>
		<div class="badge"><?=$sticker?></div>
		<?endif?>




			</div>

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


			<?if($arElement["PREVIEW_TEXT"] && ($curPage != SITE_DIR."index.php" && $curPage != SITE_DIR."catalog/index.php" )){?>
				<div class="preview_text"><?=strip_tags($arElement["PREVIEW_TEXT"])?></div>
			<?}?>

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

	<?if($cell == count($arResult["ITEMS"])-1){?>
		<li class="sep"></li>
	<?}?>

	<?endforeach; ?>
</ul>
</div><div style="clear:both;"></div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>