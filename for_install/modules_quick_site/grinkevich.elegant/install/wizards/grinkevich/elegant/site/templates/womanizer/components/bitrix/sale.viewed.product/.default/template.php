<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["ITEMS"]) > 0){ ?>

<div class="orange-noise wave">
	<div class="page-block">
		<h3 class="cat-header"><span><?=GetMessage("LAST_VIEWED_PRODUCTS")?></span></h3>
		<div class="cat-wrap">
			<?
			$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
			$arNotify = unserialize($notifyOption);
			?>
			<div class="listitem-carousel">
				<ul class="lsnn" id="foo_<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>">

				<?foreach($arResult["ITEMS"] as $key => $arItem){
					if(is_array($arItem)){
						$bPicture = is_array($arItem["PICTURE"]);
						?><li class="itembg R2D2" itemscope itemtype = "http://schema.org/Product">
							<?if ($bPicture):?>
								<a class="link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img class="item_img" itemprop="image" src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=$arElement["NAME"]?>" /></a>
							<?else:?>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><div class="no-photo-div-big" style="height:130px; width:130px;"></div></a>
							<?endif?>

							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item_title" title="<?=$arItem["NAME"]?>"><?=$arItem["NAME"]?></a>
							
							<div class="buy">
								<div class="price" itemprop = "offers" itemscope itemtype = "http://schema.org/Offer">

									<?if($arParams["VIEWED_PRICE"]=="Y"){?>
										<?if(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])){?>
											<span itemprop = "price" class="item_price price">
												<?if (count($arItem["OFFERS"]) > 1):?>
													<?=GetMessage("CR_PRICE_FROM")."&nbsp;";?>
												<?endif?>
												<?=$arItem["MIN_PRODUCT_OFFER_PRICE_PRINT"];?>
											</span>
										<?}else{?>
											<?if ($arItem["CAN_BUY"]=="Y"):?>
												<span itemprop = "price" class="item_price price"><?=$arItem["PRICE_FORMATED"]?></span>
											<?endif?>
										<?}?>
									<?}?>
					
								</div>

							</div>
							<div class="tlistitem_shadow"></div>
							<?if(!(is_array($arItem["OFFERS"]) && !empty($arItem["OFFERS"])) && !$arItem["CAN_BUY"]):?>
								<div class="badge notavailable"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></div>
							<?endif?>
						</li>
					<?
					}
				}
				?>
			    </ul>
			    <div class="clearfix"></div>
			    <a id="prev<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="prev" href="#">&lt;</a>
			    <a id="next<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="next" href="#">&gt;</a>
			    <div id="pager<?=ToLower($arParams["FLAG_PROPERTY_CODE"])?>" class="pager"></div>
			</div>

		</div>
	</div>
</div>


<?}?>

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
