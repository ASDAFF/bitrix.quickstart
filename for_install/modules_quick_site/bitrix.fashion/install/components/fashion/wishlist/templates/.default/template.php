<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="id-cart-list" class="cart-items">
<? if(!empty($arResult["ITEMS"])): ?>
	<table class="cart-items" cellspacing="0">
		<thead>
		<tr>
				<td colspan="2" class="cart-item-name"><?= GetMessage("NAME")?></td>
				<? if($arResult["MY_WISHLIST"]): ?>
					<td></td>
				<? endif; ?>
				<td class="cart-item-price"><?= GetMessage("PRICE")?></td>
<!--                    --><?// if($arResult["MY_WISHLIST"]): ?>
					<td class=""><?=GetMessage("BUY"); ?></td>
<!--                    --><?// endif; ?>
				<td></td>
		</tr>
		</thead>
		<tbody>
		<?
		$i=0; $totalSum = 0;
		foreach($arResult["ITEMS"] as $arItem){
			?>
			<tr>
					<td class="cart-item-image">
						<div class="wrap">
							<?if ($arItem["OFFER"]["models_hit"] || $arItem["OFFER"]["models_new"] || $arItem["OFFER"]["models_sale"]) {?>
								<ul class="shortcuts">
									<?if ($arItem["OFFER"]["models_hit"]) {?><li class="hit show"><?=GetMessage("HIT")?></li><?}?>
									<?if ($arItem["OFFER"]["models_new"]) {?><li class="new show"><?=GetMessage("NEW")?></li><?}?>
									<?if ($arItem["OFFER"]["models_sale"]) {?><li class="discount show"><?=GetMessage("SALE")?></li><?}?>
								</ul>
							<?}?>
							<?$img = CFile::ResizeImageGet($arItem["OFFER"]["PROPERTY_ITEM_MORE_PHOTO_VALUE"], array('width'=>150, 'height'=>192), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
							<div class="image"><?if (strlen($arItem["DETAIL_PAGE_URL"])>0){?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"></a><?}?><img itemprop="image" src="<?=$img["src"]?>" width="<?=$img["width"]?>" height="<?=$img["height"]?>" alt="<?=$arItem["NAME"]?>" /></div>
						</div>
					</td>
					<td class="cart-item-name">
						<h3><?if (strlen($arItem["DETAIL_PAGE_URL"])>0){?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?}?>
								<?=$arItem["NAME"]?>
								<?if (strlen($arItem["DETAIL_PAGE_URL"])>0){?></a><?}?>
						</h3>
						<?if (strlen($arItem["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"]) > 0) {
							$colorImg = CFile::ResizeImageGet($arItem["OFFER"]["PROPERTY_ITEM_COLOR_DETAIL_PICTURE"], array('width'=>24, 'height'=>16), BX_RESIZE_IMAGE_EXACT, true);
							?>
							<p><?=GetMessage("SIZE")?> <?=$arItem["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"><img src="<?=$colorImg["src"]?>" title="<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" alt="<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" width="<?=$colorImg["width"]?>" height="<?=$colorImg["height"]?>" /></span></p>
						<?} else {?>
							<p><?=GetMessage("SIZE")?> <?=$arItem["OFFER"]["PROPERTY_ITEM_SIZE_NAME"]?>, <?=GetMessage("COLOR")?> <span class="color" title="<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_NAME"]?>" style="background-color:#<?=$arItem["OFFER"]["PROPERTY_ITEM_COLOR_PROPERTY_HEX_VALUE"]?>"></span></p>
						<?}?>
					</td>

				<? if($arResult["MY_WISHLIST"]): ?>
					<td class="cart-item-quantity">
						<form name="remove<?=$arItem["PRODUCT_ID"];?>" action="" method="POST">
							<input type="hidden" name="ACTION_REMOVE" value="Y" />
							<input type="hidden" name="ID" value="<?=$arItem["PRODUCT_ID"];?>" />
							<a class="cart-delete-item" href="#" onclick="$('form[name=remove<?=$arItem["PRODUCT_ID"];?>]').submit(); return false;" title="<?=GetMessage("REMOVE_FROM_LIST")?>"><?=GetMessage("REMOVE")?></a>
						</form>
					</td>
				<? endif; ?>
					<td class="cart-item-price">
						<?if ($arItem["OFFER"]["models_sale"]) {?>
							<span class="oldprice"><?=CSiteFashionStore::formatMoney($arItem["BASE_PRICE"]["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span><br/>
							<span class="newprice"><?=CSiteFashionStore::formatMoney($arItem["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span>
						<? $totalSum += $arItem["PRICE"]; } else {?>
							<?=CSiteFashionStore::formatMoney($arItem["PRICE"])?> <span class="rub"><?=GetMessage("RUB")?></span></span>
						<? $totalSum += $arItem["PRICE"]; }?>
					</td>
<!--                    --><?// if($arResult["MY_WISHLIST"]): ?>

					<td>
						<a class="buy_from_wishlist" product-id="<?=$arItem["PRODUCT_ID"];?>" href="#"><img src="<?=SITE_TEMPLATE_PATH?>/i/buy_from_wishlist.png" /></a>
					</td>
<!--                    --><?// endif; ?>

				<? if($i == 0): ?>
					<td rowspan="<?=count($arResult["ITEMS"]);?>" style="width:250px;vertical-align: top;">
						<? if($arResult["MY_WISHLIST"]): ?>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/wishlist.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
						<? else: ?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/wishlist_not_authorized.php",
									"EDIT_TEMPLATE" => ""
								),
								false
							);?>
						<? endif; ?>

						<? if($arResult["MY_WISHLIST"]): ?>
						<style>.cart-buttons:before{background: none;}</style>
						<form name="sendToFriend" action="" method="post">
							<input type="text" name="email" value="" placeholder="E-mail" style="width: 185px; margin-top:10px; margin-bottom: 10px; text-align: left; " />
							<div class="cart-buttons" style="position:inherit; background: none; float:none; padding: 0px;">
								<input type="submit" value="<?=GetMessage("SEND_TO_FRIENDS");?>" name="send" style="width:195px;">
							</div>
						</form>
						<?if(isset($_REQUEST['result'])&&$_REQUEST['result']=="Y"):?>
						<br /><p style="color:green;width:195px"><?=GetMessage("SEND_OK");?></p>
						<?endif;?>
						<? endif; ?>
					</td>
				<? endif; ?>
			</tr>
			<?
			$i++;
		}
		?>
		</tbody>
	</table>
	<div class="cart-ordering">
		<div class="cart-order-amount">
			<p class="order-amount"><?=GetMessage("TOTAL_AMOUNT");?>: <?= $totalSum; ?> <span class="rub"><?=GetMessage("RUB")?></span></p>
		</div>
		
		<div class="cart-buttons">
			<form name="submitAll" method="post" action="">
				<input type="submit" value="<?=GetMessage("ADD_ALL");?>" name="buyAll" id="addAll">
			</form>
		</div>	
	</div>

    <script type="text/javascript">
        $(function() {
            $(".buy_from_wishlist").click(function(){
                var product_id = $(this).attr("product-id");
                $.ajax({
                    type: "post",
                    url: "/ajax/index.php",
                    data: {id:product_id, q: 1},
                    success: function() {
                        window.location = "<?=SITE_DIR?>personal/cart/";
                    }
                });
                return false;
            });
        });
    </script>
<? else: ?>
	<div class="box-container" style="padding:10px;">
		<div class="content"><?=GetMessage("YOUR_WISHLIST_IS_EMPTY");?>. <a href="<?=SITE_DIR?>" class="button-cont-right"><span><?=GetMessage("CONTINUE");?></span></a></div>
	</div>
<? endif; ?>
</div>