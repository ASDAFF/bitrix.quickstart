<?if (is_array($arResult['DETAIL_PICTURE_280']) || count($arResult["MORE_PHOTO"])>0):?>
<?
if (!empty($arResult["PROPERTIES"]["TITLE"]["VALUE"]))
	$APPLICATION->SetTitle($arResult["PROPERTIES"]["TITLE"]["VALUE"]);
else
	$APPLICATION->SetTitle($arResult["NAME"]);
if (!empty($arResult["PROPERTIES"]["HEADER1"]["VALUE"]))
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["PROPERTIES"]["HEADER1"]["VALUE"]);
else
	$APPLICATION->SetPageProperty("ADDITIONAL_TITLE", $arResult["NAME"]);?>

<script type="text/javascript">
$(document).ready(function() {
	$('.catalog-detail-images').fancybox({
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


<script type="text/javascript">
$(document).ready(function() {
	$('#header').html($('#posttitle').html());
	$('#posttitle').hide();
});
</script>


<?
$sticker = "";
if (array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"]))
{

	foreach (Array("SPECIALOFFER", "NEWPRODUCT", "SALELEADER") as $propertyCode)
		if (array_key_exists($propertyCode, $arResult["PROPERTIES"]) && intval($arResult["PROPERTIES"][$propertyCode]["PROPERTY_VALUE_ID"]) > 0)
		{
			$sticker .= "<div class=\"".ToLower($propertyCode)."\"></div>";
			//break;
		}
}

$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<div class="recommend"></div>
<div class="R2D2" id="details">

	
	<div class="main_img">

<div class="rating"></div>



		<?if(is_array($arResult["PREVIEW_PICTURE"]) || is_array($arResult["DETAIL_PICTURE"])):?>
			<div class="element-one-picture">
				<?if(is_array($arResult["DETAIL_PICTURE_280"])):?>
					<a rel="catalog-detail-images" class="catalog-detail-images" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>"><img itemprop="image" class="item_img"  src="<?=$arResult["DETAIL_PICTURE_280"]["SRC"]?>"  alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
				<?elseif(is_array($arResult["DETAIL_PICTURE"])):?>
					<a rel="catalog-detail-images" class="catalog-detail-images" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>"><img width="280" itemprop="image" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
				<?elseif(is_array($arResult["PREVIEW_PICTURE"])):?>
					<a rel="catalog-detail-images" class="catalog-detail-images" href="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" title="<?=(strlen($arResult["PREVIEW_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["PREVIEW_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>"><img width="280" itemprop="image" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a>
				<?endif?>
			</div>
		<?else:?>
			<div class="element-one-picture">
				<div class="no-photo-div-big" style="height:130px;"></div>
			</div>
		<?endif;?>

		<?if(!(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) && !$arResult["CAN_BUY"]):?>
			<div class="badge notavailable"><?=GetMessage("CATALOG_NOT_AVAILABLE2")?></div>
		<?elseif (strlen($sticker)>0):?>
			<div class="badge"><?=$sticker?></div>
		<?endif?>


 



<?if(count($arResult["MORE_PHOTO"])>0){?>

	<ul class="more-photo">
		<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<li><a rel="catalog-detail-images" class="catalog-detail-images" href="<?=$PHOTO['SRC_HREF']?>" title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $PHOTO["NAME"])?>"><img  src="<?=$PHOTO["SRC"]?>"  alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /></a></li>
		<?endforeach?>
	</ul>
	<div style="clear:both;"></div>
<?}?>








	</div>
	<div class="props">
		<h2  id="posttitle"><a class="item_title" href="<?=$arResult["DETAIL_PAGE_URL"]?>" title="<?=$arResult["NAME"]?>"><span itemprop="name"><?=$arResult["NAME"]?></span></a></h2>


<?
$APPLICATION->IncludeComponent("bitrix:asd.share.buttons", ".default", array(
	"ASD_ID" => $arResult["ID"],
	"ASD_TITLE" => $arResult["NAME"],
	"ASD_URL" => $arResult["DETAIL_PAGE_URL"],
	"ASD_PICTURE" => $arResult['PREVIEW_PICTURE'] ? $arResult['PREVIEW_PICTURE']['SRC'] : ($arResult['DETAIL_PICTURE'] ? $arResult['DETAIL_PICTURE']['SRC'] : ""),
	"ASD_TEXT" => $arResult["PREVIEW_TEXT"],
	"ASD_LINK_TITLE" => "Расшарить в #SERVICE#",
	"ASD_SITE_NAME" => "",
	"ASD_INCLUDE_SCRIPTS" => array(
	)
	),
	false
);
?>

		<div class="preview_descr"><?=strip_tags($arResult["~PREVIEW_TEXT"])?></div>


		<!-- prices -->

				<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]))
				{
				?>
				<?    
				 
                ?><div class="price"><div class=" item_price">
                <div class="price" id="currentOfferPrice"></div>
                <div class="price" id="minOfferPrice">
                    <?if (count($arResult["OFFERS"]) > 1) echo GetMessage("CATALOG_FROM");?>
                    <?=$arResult["MIN_PRODUCT_OFFER_PRICE_PRINT"]?>
                </div></div></div>
           
				<?
				}
				else
				{
					foreach($arResult["PRICES"] as $code=>$arPrice):?>
						<?if($arPrice["CAN_ACCESS"]):?><div class="price"><?=GetMessage("CATALOG_PRICE")?>:&nbsp;<span class=" item_price">
							<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
								<s><?=$arPrice["PRINT_VALUE"]?></s>
							<?else:?>
								<?=$arPrice["PRINT_VALUE"]?>
							<?endif?></span></div>
						<?endif;?>
					<?endforeach;?>
				<?
				}
				?>

		<div class="buy">
				<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]))
				{
				?>
				<?    
				 
                ?>
               

                	 
                   
                 		   <div class="old-price item_old_price" id="currentOfferOldPrice"></div>
					<form name="buy_form">
						<table class="options" id="sku_selectors">
							<tr>
								<td colspan="2" class="fwb"><?=GetMessage("CARALOG_OFFERS_PROPS")?></td>
							</tr>

						</table>
					</form>				
			 
				<?
				}
				else
				{
					
				}
				?>


				<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
					<br><div id="element_buy_button"></div>
					<?if ($arParams["USE_COMPARE"] == "Y"):?>
                		<div id="element_compare_button"></div>
					<?endif?>
				<?else:?>
					<?if($arResult["CAN_BUY"]):?>
						<a href="<?echo $arResult["ADD_URL"]?>" rel="nofollow" class="bt3 addtoCart" onclick="return addToCart(this, 'detail', '<?=GetMessage("CATALOG_IN_CART")?>', 'cart');" id="catalog_add2cart_link"><span class="cartbuy"></span> <?=GetMessage("CATALOG_BUY")?></a> 
					<?elseif ($arNotify[SITE_ID]['use'] == 'Y'):?>
						<?if ($USER->IsAuthorized()):?>
							<noindex><a href="<?echo $arResult["SUBSCRIBE_URL"]?>" rel="nofollow" onclick="return addToSubscribe(this, '<?=GetMessage("CATALOG_IN_SUBSCRIBE")?>');" class="bt2" id="catalog_add2cart_link"><span></span><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex> 
						<?else:?>
                        	<noindex><a href="javascript:void(0)" rel="nofollow" onclick="showAuthForSubscribe(this, <?=$arResult["ID"]?>, '<?echo $arResult["SUBSCRIBE_URL"]?>')" class="bt2"><span></span><?echo GetMessage("CATALOG_SUBSCRIBE")?></a></noindex>
						<?endif;?>
					<?endif?>
					<?if ($arParams["USE_COMPARE"] == "Y"):?>
						<a href="<?=$arResult["COMPARE_URL"]?>" rel="nofollow" class="bt2 addtoCompare" onclick="return addToCompare(this, 'detail', '<?=GetMessage("CATALOG_IN_COMPARE")?>');" id="catalog_add2compare_link"><?=GetMessage("CT_BCE_CATALOG_COMPARE")?></a>
					<?endif?>
				<?endif?></div>

 
		
		


	</div>
	<div style="clear:both; "></div>





  


</div>
<script type="text/javascript">
	var mess = <?=CUtil::PhpToJsObject($arResult["POPUP_MESS"])?>;
	BX.message(mess);
    <?if (!empty($arResult["SKU_PROPERTIES"])):?>
		var arProperties = <?=CUtil::PhpToJsObject($arResult["SKU_PROPERTIES"])?>,
			arSKU = <?=CUtil::PhpToJsObject($arResult["SKU_ELEMENTS"])?>,
			properties_num = arProperties.length;
		var lastPropCode = arProperties[properties_num-1].CODE;

		BX.ready(function(){
			buildSelect('buy_form', 'sku_selectors', 0, arSKU, arProperties, "detail", "cart");
			addHtml(lastPropCode, arSKU, "detail", "clear_cart");
		});
	<?endif?>

</script>