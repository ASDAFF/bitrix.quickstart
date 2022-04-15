<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"]) > 0): ?>
<?
if (empty($arParams["FLAG_PROPERTY_CODE"]))
	$arParams["FLAG_PROPERTY_CODE"] = rand();
?>
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
						<a href="javascript:void(0)" onclick="return showOfferPopup(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', <?=CUtil::PhpToJsObject($arItem["SKU_ELEMENTS"])?>, <?=CUtil::PhpToJsObject($arItem["SKU_PROPERTIES"])?>, <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>, 'list', 'compare');">
							<input type="checkbox" class="addtoCompareCheckbox"/><span class="checkbox_text"><?=GetMessage("CATALOG_COMPARE")?></span>
						</a>
					</span>
				<?else:?>
					<span class="checkbox">
						<a href="<?echo $arItem["COMPARE_URL"]?>" rel="nofollow" onclick="return addToCompare(this, 'list', '<?=GetMessage("CATALOG_IN_COMPARE")?>');" id="catalog_add2compare_link_<?=$arItem['ID']?>">
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
						echo GetMessage("CR_PRICE_OT")."&nbsp;";
						echo $arItem["PRINT_MIN_OFFER_PRICE"];
					}
					else
					{
						foreach($arItem["OFFERS"] as $arOffer):?>
							<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
								<?if($arPrice["CAN_ACCESS"]):?>
										<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
											<?=$arPrice["PRINT_DISCOUNT_VALUE"]?><br>
											<span class="old_price"><?=$arPrice["PRINT_VALUE"]?></span><br>
											<?else:?>
											<?=$arPrice["PRINT_VALUE"]?>
										<?endif?>
								<?endif;?>
							<?endforeach;?>
						<?endforeach;
					}
				}
				else // if product doesn't have offers
				{
					if(count($arItem["PRICES"])>0 && $arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] == $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE']):
						foreach($arItem["PRICES"] as $code=>$arPrice):
							if($arPrice["CAN_ACCESS"]):
								?>
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<?=$arPrice["PRINT_DISCOUNT_VALUE"]?><br>
										<span itemprop = "price" class="old_price"><?=$arPrice["PRINT_VALUE"]?></span>
									<?else:?>
										<span itemprop = "price"><?=$arPrice["PRINT_VALUE"]?></span>
									<?endif;?>
								<?
							endif;
						endforeach;
					else:
						$price_from = '';
						if($arItem['PROPERTIES']['MAXIMUM_PRICE']['VALUE'] > $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'])
						{
							$price_from = GetMessage("CR_PRICE_OT")."&nbsp;";
						}
						CModule::IncludeModule("sale")
						?>
						<?=$price_from?><?=FormatCurrency($arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'], CSaleLang::GetLangCurrency(SITE_ID))?>
						<?
					endif;
				}
				?>
				</div>
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="bt3"><?=GetMessage("CATALOG_MORE")?></a>
			</div>
			<div class="tlistitem_shadow"></div>
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
    $('#foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>').carouFredSel({prev:'#prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',next:'#next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>',pagination:"#pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>",auto:true,height:'auto',circular:false,infinite:false,cookie:true});
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
