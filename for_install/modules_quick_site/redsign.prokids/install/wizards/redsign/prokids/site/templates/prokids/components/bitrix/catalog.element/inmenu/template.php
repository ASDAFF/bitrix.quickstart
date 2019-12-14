<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$HAVE_OFFERS = (is_array($arResult['OFFERS']) && count($arResult['OFFERS'])>0) ? true : false;
if($HAVE_OFFERS) { $PRODUCT = &$arResult['OFFERS'][0]; } else { $PRODUCT = &$arResult; }
?><div class="name"><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=$arResult['NAME']?></a></div><?
?><div class="pic"><?
	if( isset($arResult['PREVIEW_PICTURE']['RESIZE']['src']) )
	{
		?><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><img src="<?=$arResult['PREVIEW_PICTURE']['RESIZE']['src']?>" alt="<?=$arResult['PREVIEW_PICTURE']['ALT']?>" title="<?=$arResult['PREVIEW_PICTURE']['TITLE']?>" /></a><?
	} else {
		?><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><img src="<?=$arResult['NO_PHOTO']['src']?>" alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>" /></a><?
	}
?></div><?
?><div class="price"><?
	if( $PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']>0 )
	{
		?><span class="price new gen"><?if($HAVE_OFFERS):?><?=GetMessage('SKU_PRICE_FROM')?> <?endif;?><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span><?
		?><span class="price old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></span><?
		?><span class="discount"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_DIFF']?></span><?
	} else {
		?><span class="price gen"><?if($HAVE_OFFERS):?><?=GetMessage('SKU_PRICE_FROM')?> <?endif;?><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span><?
	}
?></div><?
?><div class="more"><?
	?><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=GetMessage('LINK_MORE')?><i class="icon pngicons"></i></a><?
?></div><?
