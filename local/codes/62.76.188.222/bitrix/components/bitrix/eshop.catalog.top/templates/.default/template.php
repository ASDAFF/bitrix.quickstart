<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0): ?>
	<?
	$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
	$arNotify = unserialize($notifyOption);
	?>
	<?if ($arParams["FLAG_PROPERTY_CODE"] == "NEWPRODUCT"):?>
		<h3 class="newsale"><span></span><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
	<?elseif (strlen($arParams["FLAG_PROPERTY_CODE"]) > 0):?>
    	<h3 class="hitsale"><span></span><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
	<?endif?>
<div class="listitem-carousel">
	<ul class="lsnn" id="foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">

<?foreach($arResult["ITEMS"] as $key => $arItem):
	if(is_array($arItem))
	{
		$bPicture = is_array($arItem["PREVIEW_IMG"]);
        ?><li class="itembg R2D2" itemscope itemtype = "http://schema.org/Product">
			<?if($arParams["DISPLAY_COMPARE"]):?>
			<noindex>
				<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])):?>
					<span class="checkbox">
						<a href="javascript:void(0)" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arItem["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arItem["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'compare');">
							<input type="checkbox" class="addtoCompareCheckbox"/><span class="checkbox_text"><?=GetMessage("CATALOG_COMPARE")?></span>
						</a>
					</span>
				<?else:?>
					<span class="checkbox">
						<a href="<?echo $arItem["COMPARE_URL"]?>" rel="nofollow" onclick="return addToCompare(this, 'list', '<?=GetMessage("CATALOG_IN_COMPARE")?>', '<?=$arItem["DELETE_COMPARE_URL"]?>');" id="catalog_add2compare_link_<?=$arItem['ID']?>">
							<input type="checkbox" class="addtoCompareCheckbox"/><span class="checkbox_text"><?=GetMessage("CATALOG_COMPARE")?></span>
						</a>
					</span>
				<?endif?>
			</noindex>
			<?endif?>
			<?if ($bPicture):?>
				<a class="link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="item_img" itemprop="image" src="<?=$arItem["PREVIEW_IMG"]["SRC"]?>" width="<?=$arItem["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" /></a>
			<?else:?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:130px; width:130px;"></div></a>
			<?endif?>
			<hr/>
			<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item_title" title="<?=$arItem["NAME"]?>">
				<span itemprop = "name"><?=$arItem["NAME"]?> <span class="white_shadow"></span></span>
			</a></h4>
			<div class="buy">
				<div class="price" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer"><?
				if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))   //if product has offers
				{
					if (count($arItem["OFFERS"]) > 1)
					{
                    ?>
                       <span itemprop = "price" class="item_price" style="color:#000">
                    <?
						echo GetMessage("CR_PRICE_OT")."&nbsp;";
						echo $arItem["PRINT_MIN_OFFER_PRICE"];
                    ?>
                        </span>
                    <?
					}
					else
					{
						foreach($arItem["OFFERS"] as $arOffer):?>
							<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
								<?if($arPrice["CAN_ACCESS"]):?>
										<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
											<span itemprop = "discount-price" class="item_price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br>
											<span class="old-price"><?=$arPrice["PRINT_VALUE"]?></span><br>
											<?else:?>
											<span itemprop = "price" class="item_price price"><?=$arPrice["PRINT_VALUE"]?></span>
										<?endif?>
								<?endif;?>
							<?endforeach;?>
						<?endforeach;
					}
				}
				else // if product doesn't have offers
				{
                    foreach($arItem["PRICES"] as $code=>$arPrice):
                        if($arPrice["CAN_ACCESS"]):
                            ?>
                                <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                                    <span itemprop = "price" class="item_price discount-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span><br>
                                    <span itemprop = "price" class="old-price"><?=$arPrice["PRINT_VALUE"]?></span>
                                <?else:?>
                                    <span itemprop = "price" class="item_price price"><?=$arPrice["PRINT_VALUE"]?></span>
                                <?endif;?>
                            <?
                        endif;
                    endforeach;
				}
				?>
				</div>
				<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"]))
				{
				?>
                    <noindex><a href="javascript:void(0)" class="bt3 addtoCart" id="catalog_add2cart_offer_link_<?=$arItem['ID']?>" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arItem["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arItem["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'cart');"><?echo GetMessage("CATALOG_ADD")?></a></noindex>
				<?
				}
				else
				{
				?>
					<?if ($arItem["CAN_BUY"]):?>
						<noindex><a href="<?=$arItem["ADD_URL"]?>" class="bt3 addtoCart" rel="nofollow" onclick="return addToCart(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', 'noCart');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?=GetMessage("CATALOG_ADD")?></a></noindex>
					<?elseif ($arNotify[SITE_ID]['use'] == 'Y'):?>
						<?if ($USER->IsAuthorized()):?>
							<noindex><a href="<?echo $arItem["SUBSCRIBE_URL"]?>" rel="nofollow" class="subscribe_link" onclick="return addToSubscribe(this, '<?=GetMessage("CATALOG_IN_SUBSCRIBE")?>');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?else:?>
                        	<noindex><a href="javascript:void(0)" rel="nofollow" class="subscribe_link" onclick="showAuthForSubscribe(this, <?=$arItem['ID']?>, '<?echo $arItem["SUBSCRIBE_URL"]?>')" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?endif;?>
					<?endif?>
				<?
				}
				?>
			</div>
			<div class="tlistitem_shadow"></div>
			<?if(!(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])) && !$arItem["CAN_BUY"]):?>
        	<div class="badge notavailable"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></div>
			<?endif?>
		</li>
<?
	}
endforeach;
?>
    </ul>
    <div class="clearfix"></div>
    <a id="prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="prev" href="#">&lt;</a>
    <a id="next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="next" href="#">&gt;</a>
    <div id="pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="pager"></div>
</div>
<?elseif($USER->IsAdmin()):?>
<h3 class="hitsale"><span></span><?=GetMessage("CR_TITLE_".$arParams["FLAG_PROPERTY_CODE"])?></h3>
<div class="listitem-carousel">
	<?=GetMessage("CR_TITLE_NULL")?>
</div>
<?endif;?>

<script type="text/javascript">
    $('#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>').carouFredSel({prev:'#prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',next:'#next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',pagination:"#pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>",auto:false,height:'auto',circular:true,infinite:false,cookie:true});
    function setEqualHeight(columns){
        var tallestcolumn = 0;
        columns.each(function(){
            currentHeight = $(this).height();
            if(currentHeight > tallestcolumn){
                tallestcolumn = currentHeight;
            }
        });
        columns.height(tallestcolumn);
    }
    $(document).ready(function() {
        /*setEqualHeight($(".listitem li > h4"));
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> > li > h4"));
        setEqualHeight($(".listitem li > .buy"));
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> > li > .buy"));*/
        setEqualHeight($("#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?> .R2D2"));
        setEqualHeight($(".listitem .R2D2"));

        var countli = $(".caroufredsel_wrapper ul li").size()
        if(countli < 4){
            $(".listitem-carousel").find(".next").addClass("disabled")
        }
    });
</script>
