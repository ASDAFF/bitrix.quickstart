<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["ITEMS"]["DelDelCanBuy"])){?> 
<table class="b-basket-table">
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
                <?foreach($arResult["ITEMS"]["DelDelCanBuy"] as $arBasketItems){?>
                <tr>
                    <td class="b-basket-table__image"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><img alt="" src="<?=$arBasketItems["PICTURE"]['src'];?>"></a></td>
                    <td class="b-basket-table__name"><a href="<?=$arBasketItems["DETAIL_PAGE_URL"];?>"><?=$arBasketItems["NAME"] ?></a></td>
                    <td class="b-basket-table__where">
     <?
   
     foreach($arBasketItems['SHOP'] as $k=>$shop){?>
        <span title="<?=$shop["VALUE_ENUM"]?>" class="b-where__icon <?=$shop["VALUE_XML_ID"]?>"></span>
    <?}?>
                    </td> 
                    <td class="b-basket-table__price"><span class="b-price m-no_margin"><?=$arBasketItems["PRICE_FORMATED"]?></span></td>
                    <td class="b-basket-table__count">
                        <span class="b-basket-item-count clearfix">
                            <button data-id="1" class="b-basket-item-count__btn m-dec">−</button>
                            <input type="text" name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems["QUANTITY"]?>"  id="QUANTITY_<?=$arBasketItems["ID"]?>" class="b-basket-item-count__text">
                            <button data-id="1" class="b-basket-item-count__btn m-inc">+</button>
                        </span>
                    </td>
                    <td class="b-basket-table__total"><span class="b-price m-no_margin"><?=$arBasketItems["COST"]?></span></td>
                    <td class="b-basket-table__action">
                        <a class="b-basket-link__wishlist undelay_" data-id="<?=$arBasketItems["ID"]?>" href="#">В заказ</a>
                        <a title="Удалить" class="b-basket-link__del" data-id="<?=$arBasketItems["ID"]?>" href="#"></a>
                    </td>
                </tr> 
                <input type="hidden" name="DELAY_<?echo $arBasketItems["ID"] ?>" value="Y" checked>
           <?}?>
            </tbody>
        </table>  
  <?} else {
      ?> 
<p>Отложеных товаров нет</p>

<?
  }?>           
 
<?



return;


?>
<b><?= GetMessage("SALE_OTLOG_TITLE")?></b><br /><br />
<table class="sale_basket_basket data-table">
	<tr>
		<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_NAME")?></th>
		<?endif;?>
		<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_PROPS")?></th>
		<?endif;?>
		<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_PRICE")?></th>
		<?endif;?>
		<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_PRICE_TYPE")?></th>
		<?endif;?>
		<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_QUANTITY")?></th>
		<?endif;?>
		<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_DELETE")?></th>
		<?endif;?>
		<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_OTLOG")?></th>
		<?endif;?>
		<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
			<th align="center"><?= GetMessage("SALE_WEIGHT")?></th>
		<?endif;?>
	</tr>
	<?
	foreach($arResult["ITEMS"]["DelDelCanBuy"] as $arBasketItems)
	{
		?>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?><a href="<?echo $arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				?><b><?echo $arBasketItems["NAME"]?></b><?
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
					?></a><?
				endif;
				?></td>
			<?endif;?>
			<?if (in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
				<td>
				<?
				foreach($arBasketItems["PROPS"] as $val)
				{
					echo $val["NAME"].": ".$val["VALUE"]."<br />";
				}
				?>
				</td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td align="right"><?=$arBasketItems["PRICE_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td><?echo $arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><?echo $arBasketItems["QUANTITY"]?></td>
			<?endif;?>
			<?if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><input type="checkbox" name="DELETE_<?echo $arBasketItems["ID"] ?>" value="Y"></td>
			<?endif;?>
			<?if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
				<td align="center"><input type="checkbox" name="DELAY_<?echo $arBasketItems["ID"] ?>" value="Y" checked></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td align="right"><?echo $arBasketItems["WEIGHT_FORMATED"] ?></td>
			<?endif;?>
		</tr>
		<?
	}
	?>
</table>
<br />
<div width="30%">
	<input type="submit" value="<?= GetMessage("SALE_REFRESH")?>" name="BasketRefresh"><br />
	<small><?= GetMessage("SALE_REFRESH_DESCR")?></small><br />
</div>
<br />
<?