<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
CAjax::Init();

echo ShowError($arResult["ERROR_MESSAGE"]);
echo GetMessage("STB_ORDER_PROMT"); ?>
<br /><br />
<input type="submit" class="btn btn-grey" value="<?=GetMessage('SALE_BACKTOGOODS')?>" id="inputReturn" />
<?/*<input type="submit" class="btn" value="Очистить корзину" id="inputClearButton" />*/?>

<table cellspacing="0" cellpadding="0" border="0" class="t1 grid" id="grid_basket">
	<col width="50" />
	<col />
	<col width="100" />
	<col />
	<? if (count($arResult["ITEMS"]["AnDelCanBuy"])>1) { ?>
		<col width="100" />
	<? } ?>
	<col />
	<tr class="formheader">
		<th>&nbsp;</th>
		<th style="text-align: left;"><?=GetMessage("SALE_NAME")?></th>
		<th><?=GetMessage("SALE_PRICE")?></th>
		<th><?=GetMessage("SALE_QUANTITY")?></th>
		<? if (count($arResult["ITEMS"]["AnDelCanBuy"])>1) { ?><th><?=GetMessage("SALE_SUMM")?></th><? } ?>
		<th>&nbsp;</th>
	</tr>
	<?
	$i=0;
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems) {
		$pic = CSofteffect::getPicPath($arBasketItems['PRODUCT_ID'], TRUE);
		if ($pic) {
			$renderImage = CFile::ResizeImageGet($pic, Array("width" => '50', "height" => '150'));
		}
		?>
		<tr>
			<td class="col_image">
				<? if ($renderImage['src']) { ?>
					<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>">
						<img width="50" src="<?=$renderImage['src']?>" alt="<?=$arBasketItems["NAME"] ?>" title="<?=$arBasketItems["NAME"] ?>" />
					</a>
				<? } else { ?>
					&nbsp;
				<? } ?>
			</td>
			<td>
				<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><b><?=$arBasketItems["NAME"] ?></b></a>
			</td>
			<td align="right"><?=$arBasketItems["PRICE_FORMATED"]?></td>
			<td align="center" class="qty"><input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"] ?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3" autocomplete="off" ></td>
			<? if (count($arResult["ITEMS"]["AnDelCanBuy"])>1) { ?>
				<td align="right"><?=SaleFormatCurrency($arBasketItems["PRICE"]*$arBasketItems["QUANTITY"], 'RUB')?></td>
			<? } ?>
			<td align="center" class="col_delete">
				<input type="hidden" name="DELETE_<?=$arBasketItems["ID"] ?>" id="DELETE_<?=$i?>" value="">
				<a href="#" title="Удалить <?=$arBasketItems["NAME"] ?> из корзины"></a>
			</td>
		</tr>
		<?
		$i++;
	}
	?>
	<script>
		function sale_check_all(val) {
			for(i=0;i<=<?=count($arResult["ITEMS"]["AnDelCanBuy"])-1?>;i++)
			{
				if(val)
					document.getElementById('DELETE_'+i).checked = true;
				else
					document.getElementById('DELETE_'+i).checked = false;
			}
		}
	</script>
	<tr class="formfooter">
		<td style="border-right: 0;">&nbsp;</td>
		<? if (count($arResult["ITEMS"]["AnDelCanBuy"])>1) { ?>
			<td style="border-right: 0;">&nbsp;</td>
			<td style="border-right: 0;">&nbsp;</td>
		<? } ?>
		<td align="right" nowrap>
			<?/*if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
				<b><?echo GetMessage('SALE_VAT_INCLUDED')?></b><br />
			<?endif;?>
			<?
			if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
			{
				?><b><?echo GetMessage("SALE_CONTENT_DISCOUNT")?><?
				if (strLen($arResult["DISCOUNT_PERCENT_FORMATED"])>0)
					echo " (".$arResult["DISCOUNT_PERCENT_FORMATED"].")";?>:</b><br /><?
			}*/
			?>
			<?= GetMessage("SALE_ITOGO")?>:&nbsp;
		</td>
		<td align="right" nowrap>
			<?/*if ($arParams['PRICE_VAT_SHOW_VALUE'] == 'Y'):?>
				<?=$arResult["allVATSum_FORMATED"]?><br />
			<?endif;?>
			<?
			if (doubleval($arResult["DISCOUNT_PRICE"]) > 0)
			{
				echo $arResult["DISCOUNT_PRICE_FORMATED"]."<br />";
			}*/
			?>
			<span class="bigPrice"><?=$arResult["allSum_FORMATED"]?></span>
		</td>
		<? if (count($arResult["ITEMS"]["AnDelCanBuy"])<=1) { ?>
			<td style="border-right: 0;">&nbsp;</td>
			<td>&nbsp;</td>
		<? } else { ?>
			<td>&nbsp;</td>
			<?/*<td class="col_delete col_delete_all">
				<a href="#" title="Удалить все товары из корзины"></a>
			</td>*/?>
		<? } ?>
	</tr>
	<tr class="formfooter_last">
		<?if ($arParams["HIDE_COUPON"] != "Y") { ?>
			<td colspan="2">
				<br />
				<p class="coupon_text"><small><?= GetMessage("STB_COUPON_PROMT") ?></small></p>
				<input type="text" name="COUPON" value="<?=$arResult["COUPON"]?>" size="20">
			</td>
		<? } ?>
		<td colspan="10" align="right">
			<br />
			<div class="buttons">
				<input type="submit" class="refresh" value="<?echo GetMessage("SALE_REFRESH")?>" name="BasketRefresh" />
				<input type="submit" class="btn" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2" onclick="return false;" />
			</div>
		</td>
	</tr>
</table>
<br />
<div class="order-anotation">
	<?echo GetMessage("STOF_EMAIL_NOTE")?>
</div>
<br />
<script type="text/javascript">
	function showRefresh() {
		jsAjaxUtil.ShowLocalWaitWindow('sky007', 'grid_basket', false);
		$('#grid_basket').css('opacity', '0.5');
	}
	
	$('.grid th:first').css('text-align', 'left');
	$('.grid tr').each(function (data) {
		if (data%2==0 && data!=($('.grid tr').length-1)) $(this).addClass('color');
	});
	
	$('.grid .col_delete:not(.col_delete_all) a').click(function () {
		showRefresh();
		var inputDelete = $(this).parent().find('input');
		if (inputDelete.attr('value')=='Y') {
			inputDelete.attr('value', '');
		} else {
			inputDelete.attr('value', 'Y');
		}
		
		$('input[name=BasketRefresh]').trigger('click');
		return false;
	});
	
	$('.grid input[type=text]').bind('keyup', function () {
		timeout_id = window.setTimeout(function () {
			showRefresh();
			$('input[name=BasketRefresh]').trigger('click');
		}, 1500);
	});
	
	$('#inputReturn').click(function () {
		location.href='<?=($_SERVER['HTTP_REFERER']!='') ? $_SERVER['HTTP_REFERER'] : '#' ?>';
		return false;
	});
	
	$('#basketOrderButton2').bind('click', function () {
		basketOrderView();
		window.location.hash='step-order';
		
		return false;
	});
</script>