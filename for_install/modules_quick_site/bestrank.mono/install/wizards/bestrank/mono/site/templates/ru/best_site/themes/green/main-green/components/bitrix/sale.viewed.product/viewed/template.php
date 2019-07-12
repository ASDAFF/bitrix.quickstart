<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (count($arResult) > 0):?>
<div class="actions viewed">
	<h5><?=GetMessage("VIEW_HEADER");?></h5>

	<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery('#view-products').jcarousel({
	    	wrap: 'circular',
		scroll: 1,
		
	    });
	});	
		</script>

	<?if(count($arResult)<5){?>
	<style>
	.view-list .jcarousel-skin-tango .jcarousel-container-horizontal {
	    width: <?=(200*count($arResult))?>px;
	}
	</style>
	<?}?>

	<div class="view-list"><ul id="view-products" class="jcarousel-skin-tango news lsnn">
		<?foreach($arResult as $arItem):?>
			<li class="R2D2">
				<?if($arParams["VIEWED_IMAGE"]=="Y" && is_array($arItem["PICTURE"])):?>
					<div class="img">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" class="picture item_img" ></a>
					</div>
				<?endif?>
				<div class="descr">
					<?if($arParams["VIEWED_NAME"]=="Y"):?>
						<div class="name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
					<?endif?>
					<?if($arParams["VIEWED_PRICE"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
						<div class="price"><div class="item_price"><?=$arItem["PRICE_FORMATED"]?></div></div>
					<?endif?>

					<?if($arItem["CAN_BUY"]=="Y" && ($arParams["VIEWED_CANBUY"]=="Y" || $arParams["VIEWED_CANBUSKET"]=="Y") ):?>
						<div class="buy">
							<?if($arParams["VIEWED_CANBUY"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
								<noindex>
									<a class="bt3" href="<?=$arItem["BUY_URL"]?>" rel="nofollow"><?=GetMessage("PRODUCT_BUY")?></a>
								</noindex>
							<?endif?>
							<?if($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
								<noindex>
									<a class="bt3" href="<?=$arItem["ADD_URL"]?>" rel="nofollow" onclick="return addToCart(this, 'list', '<?=GetMessage("CATALOG_IN_CART")?>', 'noCart');" id="catalog_add2cart_link_<?=$arItem['ID']?>"><?=GetMessage("PRODUCT_BUSKET")?></a>
								</noindex>
							<?endif?>
						</div>
					<?endif?>

				</div>
			</li>
		<?endforeach;?></ul>
	</div><div style="clear:both;"></div>
</div>
<?endif;?>