<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult["READY"]=="Y" || $arResult["DELAY"]=="Y" || $arResult["NOTAVAIL"]=="Y" || $arResult["SUBSCRIBE"]=="Y"):?>




	<? if (!empty($arResult["ITEMS"])): ?>
		<?
			$count = $price = 0;
			$ids = array();

			foreach ($arResult["ITEMS"] as $v)
			{
				if ($v["DELAY"]=="N" && $v["CAN_BUY"]=="Y")
				{
					$count += $v["QUANTITY"];

					$price += $v["QUANTITY"] * $v["PRICE"];

					$ids[] = 'id' . $v['PRODUCT_ID'];
					?>

					<?
				}
			}
		?>


		<p id="tp-info"><i></i><?= GetMessage("SBL_IN_CART");?> <a href="<?=$arParams["PATH_TO_BASKET"]?>" id="priceCount" ccount="<?= $count; ?>"><?= $count . ' ' . _emisc::ruscomp($count, GetMessage("SBL_GOODS_IN_CART")); ?></a></p>
		<p id="def-cart-mess" style="display: none"><i></i><?= GetMessage("SBL_IN_CART_EMPTY");?></p>

		<input type="hidden" id="cart-items" name="cart-items" value="<?= join(',', $ids); ?>" />

	<? endif; ?>


<? else : ?>
	<p id="tp-info" style="display: none"><i></i><?= GetMessage("SBL_IN_CART");?> <a href="<?=$arParams["PATH_TO_BASKET"]?>" id="priceCount" ccount="0">0</a></p>
	<p id="def-cart-mess"><i></i><?= GetMessage("SBL_IN_CART_EMPTY");?></p>

	<input type="hidden" id="cart-items" name="cart-items" value="" />

<?endif;?>
