<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
	{
?>
					<div class="b-cart__item clearfix">
						<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
						<div class="b-cart__image"><img src="<?=CFile::GetPath($arBasketItems["PREVIEW_PICTURE"]);?>" alt="" /></div>
						<div class="b-cart__text">
						<? $ar_res = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array("ID" => $arBasketItems["PRODUCT_ID"], "IBLOCK_ID" => "1"),false , false , Array("ID", "PROPERTY_model", "PROPERTY_type", "PROPERTY_article"))->GetNextElement();
						$props = $ar_res->GetFields();
						?>
							<div class="b-cart__link"><?=$props["PROPERTY_TYPE_VALUE"]?> 
							<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
								?><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
							endif;?>
							<?=$arBasketItems["NAME"] ?> <?=$props["PROPERTY_MODEL_VALUE"]?> (<?=$props["PROPERTY_ARTICLE_VALUE"]?>)
							<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?></a><?endif;?>
							</div>
							<div class="b-cart__info"><?=$arBasketItems["PREVIEW_TEXT"]?></div>
						</div>
						<?endif;?>

						<div class="b-cart__price">
							<div class="clearfix">
								<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
								<div class="b-cart__price_left">
									<div class="b-slider__price m-price__16px"><?=$arBasketItems["PRICE_FORMATED"]?></div>
									<div class="b-slider__price_clearing">Безнал<br /><b>19 540.8.–</b></div>							
								</div>
								<?endif;?>
								<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
									<div class="b-cart__price_right">
									<button class="b-cart-count__btn m-dec" data-value="<?=$arBasketItems["PRODUCT_ID"]?>"></button>
									<span class="b-cart-count__count" id="item_count_<?=$arBasketItems["PRODUCT_ID"]?>"><?=$arBasketItems["QUANTITY"]?></span>
									<input type="hidden" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" id="item_hidden_<?=$arBasketItems["PRODUCT_ID"]?>">
									<button class="b-cart-count__btn m-inc" data-value="<?=$arBasketItems["PRODUCT_ID"]?>"></button>
								</div>
								<?endif;?>							
							</div>
						</div>
						<div class="b-cart__total">
							<div class="clearfix">
								<div class="b-cart__total_left">
									<div class="b-slider__price m-price__16px"><?=$arBasketItems["PRICE_FORMATED"]?></div>
									<div class="b-slider__price_clearing">Безнал<br /><b>19 540.8.–</b></div>							
								</div>
								<div class="b-cart__total_right">
								<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
									<a class="b-button__delete m-cart__delete" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a><br>
								<?endif;?>
								</div>
							</div>
						</div>
					</div>


			<tr>



				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-discount"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
				<?endif;?>
				<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type"><?=$arBasketItems["NOTES"]?></td>
				<?endif;?>

				<td class="cart-item-actions">

					<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
						<a href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>"><?=GetMessage("SALE_OTLOG")?></a>
					<?endif;?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
					
					
<div class="cart-items" id="id-cart-list">
	<div class="inline-filter cart-filter">
		<label><?=GetMessage("SALE_PRD_IN_BASKET")?></label>&nbsp;
		<b><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?></b>&nbsp;
		<a href="javascript:void(0);" onclick="ShowBasketItems(2);"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?> (<?=count($arResult["ITEMS"]["DelDelCanBuy"])?>)</a>
		<a href="javascript:void(0);" onclick="ShowBasketItems(4);"><?=GetMessage("SALE_NOACTIVE")?> (<?=count($arResult["ITEMS"]["nAnCanBuy"])?>)</a>
		<a href="javascript:void(0);" onclick="ShowBasketItems(3);"><?=GetMessage("SALE_BASKET_NOTIFY")?> (<?=count($arResult["ITEMS"]["AnSubscribe"])?>)</a>
	</div>
	
	<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
		<table border="0" class="cart-items" cellspacing="0">
		<thead>
			<tr>
				<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-name"><?= GetMessage("SALE_NAME")?></td>
				<?endif;?>
				<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></td>
				<?endif;?>
				<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-quantity"><?= GetMessage("SALE_QUANTITY")?></td>
				<?endif;?>
				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></td>
				<?endif;?>	
				<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></td>
				<?endif;?>
				<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-price"><?= GetMessage("SALE_PRICE")?></td>
				<?endif;?>
					
				<td class="cart-item-actions">
					<?if (in_array("DELETE", $arParams["COLUMNS_LIST"]) || in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
						<?=GetMessage("SALE_ACTION")?>
					<?endif;?>
				</td>
			</tr>
		</thead>
		<tbody>

		</tbody>
		<tfoot>
			<tr>
				<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-itog" valign="bottom">
						<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
							<p><?echo GetMessage("SALE_ALL_WEIGHT")?>:</p>
						<?endif;?>
						<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
						{
							?><p><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
							if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
								echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:</p><?
						}?>
						<?if ($arParams['PRICE_TAX_SHOW_VALUE'] == 'Y'):?>
							<p><?echo GetMessage('SALE_TAX_INCLUDED')?></p>
						<?endif;?>	
						<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
							<p><?echo GetMessage('SALE_VAT_INCLUDED')?></p>
						<?endif;?>
						<p><b><?= GetMessage("SALE_ITOGO")?>:</b></p>
					</td>
				<?endif;?>
					
				<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type">&nbsp;</td>
				<?endif;?>
				<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type">&nbsp;</td>
				<?endif;?>
				<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type">&nbsp;</td>
				<?endif;?>
				<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-type">&nbsp;</td>
				<?endif;?>
				<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-price" valign="bottom">
						<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
							<p><?=$arResult["ORDER_WEIGHT_FORMATED"]?></p>
						<?endif;?>
						<?if (doubleval($arResult["DISCOUNT_PRICE"]) > 0):?>
							<p><?=$arResult["DISCOUNT_PRICE_FORMATED"]?></p>
						<?endif;?>
						<?if ($arParams['PRICE_TAX_SHOW_VALUE'] == 'Y'):?>
							<p><?=$arResult["TAX_VALUE_FORMATED"]?></p>
						<?endif;?>	
						<?if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
							<p><?=$arResult["VAT_SUM_FORMATED"]?></p>
						<?endif;?>
						<p><b><?=$arResult["PRICE_FORMATED"]?></b></p>
					</td>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"]) || in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
					<td class="cart-item-actions">&nbsp;</td>
				<?endif;?>
			</tr>
		</tfoot>
		</table>

		<div class="cart-ordering">
			<?if ($arParams["HIDE_COUPON"] != "Y"):?>
				<div class="cart-code">
					<input value="<?=$arResult["COUPON"]?>" name="COUPON" >
					<div><small><?=GetMessage("SALE_COUPON_VAL")?></small></div>
				</div>
			<?endif;?>
			<div class="cart-buttons">
				<input type="submit" value="<?=GetMessage("SALE_UPDATE")?>" name="BasketRefresh" onClick="submitForm();">
				<input type="button" value="<?=GetMessage("SALE_TAKE_ORDER")?>" name="BasketOrder" onClick="ShowUser();">
			</div>
		</div>
	<?else:
		echo ShowNote(GetMessage("SALE_NO_ACTIVE_PRD"));
	endif;?>	
</div>
