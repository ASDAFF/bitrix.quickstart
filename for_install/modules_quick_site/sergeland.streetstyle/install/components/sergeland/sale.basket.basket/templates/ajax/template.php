<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $MESS; @include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".$arResult["LANG"]."/template.php");
?>
<div class="cart">
<?if(count($arResult["ITEMS"]["AnDelCanBuy"]) > 0):?>
	<div class="head">
		<div><u><?=GetMessage("SERGELAND_BASKET_SMALL_ITOGO")?>:</u><span class="price"><?=$arResult["~allSum_FORMATED"]?></span><?=$arResult["~CURRENCY_FORMAT"]["FORMAT_PRINT"]?>&nbsp;</div>
		
		<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="button_link btn_yellow"><span><?=GetMessage("SERGELAND_BASKET_SMALL_EDIT_BASKET")?></span></a>
		<a href="<?=$arParams["PATH_TO_ORDER"]?>" class="button_link btn_yellow"><span><?=GetMessage("SERGELAND_BASKET_SMALL_ORDER")?></span></a>			
	</div>
	<hr>
	
	<?foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems):?>
	<div class="basket-element-item">
		<h4><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?=$arBasketItems["NAME"]?></a></h4>
		<table border="0"><tr>
			<td><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><div class="img">
				<?if(!empty($arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"])):?>
					<img src="<?=$arResult["ITEMS_IMG"][$arBasketItems["ID"]]["SRC"]?>">
				<?endif?>
			</div></a></td>
			<td>
				<?if(in_array("PROPS", $arParams["COLUMNS_LIST"])):?>
					<div class="property">
						<?if(is_array($arBasketItems["PROPS"]["COLOR"])):?><div><?=$arBasketItems["PROPS"]["COLOR"]["VALUE"]?></div><?endif?>					
						<?if(is_array($arBasketItems["PROPS"]["SIZE"])):?><div><?=$arBasketItems["PROPS"]["SIZE"]["VALUE"]?></div><?endif?>
						<?if(is_array($arBasketItems["PROPS"]["ARTNUMBER"])):?><div><?=$arBasketItems["PROPS"]["ARTNUMBER"]["VALUE"]?></div><?endif?>
					</div>	
				<?endif?>				

				<div class="footer">
					<div class="quantity"><u><?=$arBasketItems["QUANTITY"]?></u> <?=GetMessage("SERGELAND_BASKET_SMALL_QUANTITY")?></div>
					<div class="price">
						<span><?=$arBasketItems["~PRICE_FORMATED"]?></span> <?=$arBasketItems["~CURRENCY_FORMAT"]["FORMAT_PRINT"]?>
					</div>
					<?if($arBasketItems["DISCOUNT_PRICE_PERCENT"] > 0):?>
					<div class="price old">
						<span><?=$arBasketItems["~FULL_PRICE"]?></span> <?=$arBasketItems["~CURRENCY_FORMAT"]["FORMAT_PRINT"]?>
					</div>					
					<?endif?>										
				</div>	
			</td>
		</tr></table>		
	</div>			
	<hr>
	<?endforeach?>
<?else:?>
	<p><?=GetMessage("SERGELAND_BASKET_SMALL_EMPTY_BASKET")?></p>
<?endif?>	
</div>
<div class="cart-logo">
	<div><?=$arResult["allQuantity"]?></div>
</div>