<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (count($arResult["ITEMS"]) > 0):?>
<div class="vieweditems">
	<h5><?=GetMessage("LAST_VIEWED")?></h5>
    <ul class="lsnn">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<li class="view-item R2D2" itemscope itemtype = "http://schema.org/Product">
				<?if($arParams["VIEWED_IMAGE"]=="Y" ):?>
					<?if (is_array($arItem["PICTURE"])):?>
						<div><a class="link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PICTURE"]["src"]?>" class="item_img" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"></a></div>
					<?else:?>
						<div class="no-photo-div-small" style="height:71px; width:95px"></div>
					<?endif?>
				<?endif?>
				<h4>
				<?if($arParams["VIEWED_NAME"]=="Y"):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item_title" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a>
				<?endif?>
				<?if($arParams["VIEWED_PRICE"]=="Y"):?>
                    <?
					if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
					{
                    ?>
						<div class="price" <?if ($arParams["VIEWED_IMAGE"]=="N"):?>style="position:relative;left:-4px; top:0"<?endif?>>
						<span itemprop = "price"  class="model">
							<?if (count($arItem["OFFERS"]) > 1):?>
								<?=GetMessage("CR_PRICE_FROM")."&nbsp;";?>
							<?endif?>
							<?=$arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"];?>
						</span></div>

                    <?
                    }
                    else
                    {
                    ?>
                        <?if ($arItem["CAN_BUY"]=="Y"):?>
						<div class="price" <?if ($arParams["VIEWED_IMAGE"]=="N"):?>style="position:relative;left:-4px; top:0"<?endif?>>
							<span class="model" class="" itemprop = "price"><?=$arItem["PRICE_FORMATED"]?></span>
						</div>
                        <?endif?>
                    <?
                    }
                    ?>
				<?endif?>
				</h4>
				<?if($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
                    <?
                    if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
                    {
                    ?>
						<noindex><a href="javascript:void(0)" class="bt1" id="viewed_catalog_add2cart_offer_link_<?=$arItem['PRODUCT_ID']?>" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arItem["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arItem["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'cart');"><span></span><?echo GetMessage("PRODUCT_BUY")?></a></noindex>
                    <?
                    }
                    else
                    {
                    ?>
                        <?if ($arItem["CAN_BUY"]=="Y"):?>
						    <noindex><a class="bt1" href="<?=$arItem["ADD_URL"]?>" rel="nofollow" onclick="return addToCart(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', 'cart');" id="viewed_catalog_add2cart_link_<?=$arItem['PRODUCT_ID']?>"><span></span><?=GetMessage("PRODUCT_BUY")?></a></noindex>
                        <?endif?>
                    <?
                    }
                    ?>
				<?endif?>
			</li>
		<?endforeach;?>
	</ul>
</div>
<?endif;?>