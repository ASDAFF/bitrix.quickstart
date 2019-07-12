<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="cart-items" id="id-subscribe-list" style="display:none;">
	<div class="catalog-section-list">
		<ul class="cart_sections">
			<li class="cart_sections_title"><?=GetMessage("SHOES_PRD_IN_BASKET")?></li>
			<li><a href="javascript:void(0)" onclick="ShowBasketItems(1);" ><?=GetMessage("SHOES_PRD_IN_BASKET_ACT")?> (<?=count($arResult["ITEMS"]["AnDelCanBuy"])?>)</a></li>
			<?if ($countItemsDelay=count($arResult["ITEMS"]["DelDelCanBuy"])):?><li><a href="javascript:void(0)" onclick="ShowBasketItems(2);"><?=GetMessage("SHOES_PRD_IN_BASKET_SHELVE")?> (<?=$countItemsDelay?>)</a></li><?endif?>
			<li><a class="active" href="javascript:void(0)"><?=GetMessage("SHOES_PRD_IN_BASKET_SUBSCRIBE")?></a></li>
			<?if ($countItemsNotAvailable=count($arResult["ITEMS"]["nAnCanBuy"])):?><li><a href="javascript:void(0)" onclick="ShowBasketItems(4);" class="sortbutton"><?=GetMessage("SHOES_PRD_IN_BASKET_NOTA")?> (<?=$countItemsNotAvailable?>)</a></li><?endif?>
		</ul>
	</div>

	<div class="table_wrapper">
	<table class="shopping_cart shopping_cart_row_height">
		<thead>
			<tr>
				<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<th align="center" class="cart_tovar_img_title"><?echo GetMessage("SHOES_NAME")?></th>
					<th></th>
				<?endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<th align="center"><?echo GetMessage("SHOES_PROPS")?></th>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<th align="center"></th>
				<?endif;?>
			</tr>
		</thead>
		<tbody>
		<?
		foreach($arResult["ITEMS"]["ProdSubscribe"] as $arBasketItems)
		{
			?>
			<tr>
				<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<td  class="cart_tovar_img_cell">
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>" class="cart_tovar_img">
						<?endif;?>
						<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
							<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
						<?else:?>
							<img src="/bitrix/components/bagmet/mobile.basket/templates/.default/images/nothing_found.png" alt="<?=$arBasketItems["NAME"] ?>"/>
						<?endif?>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							</a>
						<?endif;?>
					</td>
					<td>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							<a class="cart_tovar_name" href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
						<?endif;?>
						<p><?=$arBasketItems["NAME"] ?></p>
						<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
							</a>
						<?endif;?>
					</td>
				<?endif;?>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<td align="center">
					<?
						foreach($arBasketItems["PROPS"] as $val)
						{
							echo "<p class='cart_tovar_other_p'>".$val["NAME"].": ".$val["VALUE"]."</p>";
						}
					?>
					</td>
				<?endif;?>
				<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<td align="center">
						<a class="delete_from_cart_btn" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" title="<?=GetMessage("SHOES_DELETE_PRD")?>"></a>
					</td>
				<?endif;?>
			</tr>
			<?
		}
		?>
		</tbody>
	</table>
	</div>
</div>
<?