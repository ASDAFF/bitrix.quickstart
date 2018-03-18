<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
<div class="order-full-summary">
	<div class="order-item">
		<div class="order-title">
			<b class="r2"></b><b class="r1"></b><b class="r0"></b>
			<div class="order-title-inner">
				<span><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ID"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></span>
			</div>
		</div>
		<div class="order-info">
<!-- UnitellerPlugin change -->
<?php
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . ps_uniteller::UNITELLER_SALE_PATH . '/result.php')) {
			include($_SERVER['DOCUMENT_ROOT'] . ps_uniteller::UNITELLER_SALE_PATH . '/result.php');
		}
?>
		</div>
<!-- /UnitellerPlugin change -->
	</div>

	<div class="order-item">
		<div class="order-title">
			<b class="r2"></b><b class="r1"></b><b class="r0"></b>
			<div class="order-title-inner">
				<span><?=GetMessage("P_ORDER_BASKET")?></span>
			</div>
		</div>
		<div class="order-info">
			<div class="cart-items">
				<table class="cart-items" cellspacing="0">
					<thead>
						<tr>
							<td class="cart-item-name"><?= GetMessage("SPOD_NAME") ?></td>
							<td class="cart-item-price"><?= GetMessage("SPOD_PRICE") ?></td>
							<td class="cart-item-weight"><?= GetMessage("SPOD_WEIGHT") ?></td>
							<td class="cart-item-quantity"><?= GetMessage("SPOD_QUANTITY") ?></td>
						</tr>
					</thead>
					<tbody>
					<?
					foreach($arResult["BASKET"] as $val)
					{
						?>
						<tr>
							<td class="cart-item-name"><?
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "<a href=\"".$val["DETAIL_PAGE_URL"]."\">";
							echo htmlspecialcharsEx($val["NAME"]);
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "</a>";

							if(!empty($val["PROPS"]))
							{
								foreach($val["PROPS"] as $vv)
									echo "<p>".$vv["NAME"].": ".$vv["VALUE"]."</p>";
							}?></td>
							<td class="cart-item-price"><?=$val["PRICE_FORMATED"]?></td>
							<td class="cart-item-weight"><?=$val["WEIGHT_FORMATED"]?></td>
							<td class="cart-item-quantity"><?=$val["QUANTITY"]?></td>
						</tr>
						<?
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<td class="cart-item-name">
								<?
								if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0)
									echo "<p>".GetMessage("SPOD_WEIGHT_ALL").":</p>";
								foreach($arResult["TAX_LIST"] as $val)
									echo "<p>".$val["TAX_NAME"]." ".$val["VALUE_FORMATED"].":</p>";
								if(DoubleVal($arResult["TAX_VALUE"]) > 0)
									echo "<p>".GetMessage("SPOD_TAX").":</p>";
								if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0)
									echo "<p>".GetMessage("SPOD_DISCOUNT").":</p>";
								if(DoubleVal($arResult["PRICE_DELIVERY"]) > 0)
									echo "<p>".GetMessage("SPOD_DELIVERY").":</p>";
								?>
								<p><b><?=GetMessage("SPOD_ITOG")?>:</b></p>
							</td>
							<td class="cart-item-price">
								<?
								if(DoubleVal($arResult["ORDER_WEIGHT"]) > 0)
									echo "<p>".$arResult["ORDER_WEIGHT_FORMATED"]."</p>";
								foreach($arResult["TAX_LIST"] as $val)
									echo "<p>".$val["VALUE_MONEY_FORMATED"]."</p>";
								if(DoubleVal($arResult["TAX_VALUE"]) > 0)
									echo "<p>".$arResult["TAX_VALUE_FORMATED"]."</p>";
								if(DoubleVal($arOrder["DISCOUNT_VALUE"]) > 0)
									echo "<p>".$arResult["DISCOUNT_VALUE_FORMATED"]."</p>";
								if(DoubleVal($arResult["PRICE_DELIVERY"]) > 0)
									echo "<p>".$arResult["PRICE_DELIVERY_FORMATED"]."</p>";
								?>
								<p><b><?=$arResult["PRICE_FORMATED"]?></b></p>
							</td>
							<td class="cart-item-weight">&nbsp;</td>
							<td class="cart-item-quantity">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
