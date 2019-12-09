<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arResult['HAVE_PRODUCT_TYPE']['SUBSCRIBED'] )
{
	?><div class="part subscribed clearfix"><?
		
		?><div class="title"><h3><?=GetMessage('SALE_BASKET_ITEMS_SUBSCRIBED')?></h3></div><?
		
		ShowTable($arParams,$arResult,'subscribed');
		
		?><div class="btns clearfix"><?
			?><input class="btn btn3 clearitems" type="submit" name="BasketClearAll" value="<?=GetMessage('SALE_BTN_DEL_ALL')?>" /><?
			?><input class="btn btn3" type="submit" name="BasketRefresh" value="<?=GetMessage('SALE_DELETE')?>" /><?
		?></div><?

	?></div><?
}