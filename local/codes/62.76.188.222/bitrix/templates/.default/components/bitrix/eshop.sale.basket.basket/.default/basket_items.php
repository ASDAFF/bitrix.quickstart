<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
  <?if(count($arResult["ITEMS"]["AnDelCanBuy"])){?> <table class="b-basket-table">
            <thead>
                <tr>
                    <td>Название</td>
                    <td></td>
                    <td>Наличие</td>
                    <td>Цена</td>
                    <td>Количество</td>
                    <td>Стоимость</td>
                    <td>Действие</td>
                </tr>
            </thead>
            <tbody>
                <?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems){?>
                <tr>
                    <td class="b-basket-table__image"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><img alt="" src="<?=$arBasketItems["DETAIL_PICTURE"]['SRC'];?>"></a></td>
                    <td class="b-basket-table__name"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><?=$arBasketItems["NAME"] ?></a></td>
                    <td class="b-basket-table__where">
                        <span title="че то надо написать" class="b-where__icon m-r"></span>
                        <span title="че то надо написать" class="b-where__icon m-p"></span>
                    </td>
                    <td class="b-basket-table__price"><span class="b-price m-no_margin">19 898</span></td>
                    <td class="b-basket-table__count">
                        <span class="b-basket-item-count clearfix">
                            <button data-id="1" class="b-basket-item-count__btn m-dec">−</button>
                            <input type="text"  name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems["QUANTITY"]?>"  id="QUANTITY_<?=$arBasketItems["ID"]?>" class="b-basket-item-count__text">
                            <button data-id="1" class="b-basket-item-count__btn m-inc">+</button>
                        </span>
                    </td>
                    <td class="b-basket-table__total"><span class="b-price m-no_margin">19 898</span></td>
                    <td class="b-basket-table__action">
                        <a class="b-basket-link__wishlist" href="#">Отложить</a>
                        <a title="Удалить" class="b-basket-link__del" href="#"></a>
                    </td>
                </tr>
           <?}?>
            </tbody>
        </table>
  <?}?>           
            










 










<?
prent($arResult["ITEMS"]["AnDelCanBuy"]);
//return;
?>
<div id="id-cart-list">
	<div class="sort"> 
		 <a href="javascript:void(0)" class="sortbutton current">1<?=GetMessage("SALE_PRD_IN_BASKET_ACT")?>1</a>
		<?if ($countItemsDelay=count($arResult["ITEMS"]["DelDelCanBuy"])):?><a href="javascript:void(0)" onclick="ShowBasketItems(2);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?> (<?=$countItemsDelay?>)</a><?endif?>
		<?if ($countItemsSubscribe=count($arResult["ITEMS"]["ProdSubscribe"])):?><a href="javascript:void(0)" onclick="ShowBasketItems(3);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_SUBSCRIBE")?> (<?=$countItemsSubscribe?>)</a><?endif?>
		<?if ($countItemsNotAvailable=count($arResult["ITEMS"]["nAnCanBuy"])):?><a href="javascript:void(0)" onclick="ShowBasketItems(4);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_NOTA")?> (<?=$countItemsNotAvailable?>)</a><?endif?>
	</div>
<?$numCells = 0;?>
<table class="equipment mycurrentorders" rules="rows" style="width:726px">
	<thead>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_NAME")?></td>
				<td></td>
				<?$numCells += 2;?>
			<?endif;?>
			<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_VAT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-quantity"><?= GetMessage("SALE_QUANTITY")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price"><?= GetMessage("SALE_PRICE")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-delay"></td>
				<?$numCells++;?>
			<?endif;?>
		</tr>
	</thead>
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<tbody>
	<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
	{
		?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td>
					<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
						<a class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" onclick="//return DeleteFromCart(this);" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a>
					<?endif;?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
					<?endif;?>
					<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
						<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?else:?>
						<img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?endif?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						</a>
					<?endif;?>
				</td>
				<td class="cart-item-name">
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
					<?endif;?>
						<?=$arBasketItems["NAME"] ?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						</a>
					<?endif;?>
					<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
					{
						foreach($arBasketItems["PROPS"] as $val)
						{
							echo "<br />".$val["NAME"].": ".$val["VALUE"];
						}
					}?>
				</td>
			<?endif;?>
			<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td>
					<input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3" id="QUANTITY_<?=$arBasketItems["ID"]?>">
					<div class="count_nav">
						<a href="javascript:void(0)" class="plus" onclick="BX('QUANTITY_<?=$arBasketItems["ID"]?>').value++;"></a>
						<a href="javascript:void(0)" class="minus" onclick="if (BX('QUANTITY_<?=$arBasketItems["ID"]?>').value > 1) BX('QUANTITY_<?=$arBasketItems["ID"]?>').value--;"></a>
					</div>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price">
					<?if(doubleval($arBasketItems["FULL_PRICE"]) > 0):?>
						<div class="discount-price"><?=$arBasketItems["PRICE_FORMATED"]?></div>
						<div class="old-price"><?=$arBasketItems["FULL_PRICE_FORMATED"]?></div>
					<?else:?>
						<div class="price"><?=$arBasketItems["PRICE_FORMATED"];?></div>
					<?endif?>
				</td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td><a class="setaside" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>"><?=GetMessage("SALE_OTLOG")?></a></td>
			<?endif;?>
		</tr>
		<?
		$i++;
	}
	?>
	</tbody>
</table>
<table class="myorders_itog">
	<tbody>
		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td rowspan="5" class="tal">
				<input class="input_text_style"
					<?if(empty($arResult["COUPON"])):?>
						onclick="if (this.value=='<?=GetMessage("SALE_COUPON_VAL")?>')this.value=''; this.style.color='black'"
						onblur="if (this.value=='') {this.value='<?=GetMessage("SALE_COUPON_VAL")?>'; this.style.color='#a9a9a9'}"
						style="color:#a9a9a9"
					<?endif;?>
						value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SALE_COUPON_VAL")?><?endif;?>"
						name="COUPON">
			</td>
		</tr>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<tr>
				<td><?echo GetMessage("SALE_ALL_WEIGHT")?>:</td>
				<td><?=$arResult["allWeight_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
			<tr>
				<td><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
					if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
						echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:
				</td>
				<td><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
			<tr>
				<td><?echo GetMessage('SALE_VAT_EXCLUDED')?></td>
				<td><?=$arResult["allNOVATSum_FORMATED"]?></td>
			</tr>
			<tr>
				<td><?echo GetMessage('SALE_VAT_INCLUDED')?></td>
				<td><?=$arResult["allVATSum_FORMATED"]?></td>
			</tr>
		<?endif;?>
		<tr>
			<td><?= GetMessage("SALE_ITOGO")?>:</td>
			<td><?=$arResult["allSum_FORMATED"]?></td>
		</tr>
	</tbody>
</table>
<br/>
<table class="w100p" style="border-top:1px solid #d9d9d9;margin-bottom:40px;">
	<tr>
		<td style="padding:30px 2px;" class="tal"><input type="submit" value="<?echo GetMessage("SALE_UPDATE")?>" name="BasketRefresh" class="bt2"></td>
		<td style="padding:30px 2px;" class="tar"><input type="submit" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2" class="bt3"></td>
	</tr>
</table>
<?else:?>
	<tbody>
		<tr>
			<td colspan="<?=$numCells?>" style="text-align:center">
				<div class="cart-notetext"><?=GetMessage("SALE_NO_ACTIVE_PRD");?></div>
				<a href="<?=SITE_DIR?>" class="bt3"><?=GetMessage("SALE_NO_ACTIVE_PRD_START")?></a><br><br>
			</td>
		</tr>
	</tbody>
</table>
<?endif;?>
 
</div>
