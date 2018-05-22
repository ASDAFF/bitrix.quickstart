<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arResult['HAVE_PRODUCT_TYPE']['NOT_AVAILABLE'] )
{
	?><div class="part notavailable clearfix"><?
		
		?><div class="title"><h3><?=GetMessage('SALE_BASKET_ITEMS_NOT_AVAILABLE')?></h3></div><?
		
		ShowTable($arParams,$arResult,'notavailable');
		
		?><div class="btns clearfix"><?
			?><input class="btn btn3 clearitems" type="submit" name="BasketClearAll" value="<?=GetMessage('SALE_BTN_DEL_ALL')?>" /><?
			?><input class="btn btn3" type="submit" name="BasketRefresh" value="<?=GetMessage('SALE_DELETE')?>" /><?
		?></div><?

	?></div><?
}