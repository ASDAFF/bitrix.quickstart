<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?><div class="modern-menu__product js-mm__product"><?
	if($arResult['IMAGES'][0]['src']!=''){
		?><a class="modern-menu__product-img" href="<?=$arResult['DETAIL_PAGE_URL']?>"><?
			?><img src="<?=$arResult['IMAGES'][0]['src']?>" width="<?=$arResult['IMAGES'][0]['width']?>" height="<?=$arResult['IMAGES'][0]['height']?>" alt="<?=$arResult['NAME']?>" /><?
		?></a><?
	}
	?><div class="modern-menu__product-name"><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=$arResult['NAME']?></a></div><?
	?><div class="modern-menu__product-price-wrap clearfix"><?
	if(!empty($arResult['OFFERS'])){
		if($arResult['OFFERS'][0]['MIN_PRICE']['DISCOUNT_DIFF']){
			?><div class="crossed_price"><?=$arResult['OFFERS'][0]['MIN_PRICE']['PRINT_VALUE']?></div><?
		}
		?><div class="modern-menu__product-price"><?=GetMessage('SKU_PRICE_FROM')?> <?=$arResult['OFFERS'][0]['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
	}
	else{
		if($arResult['MIN_PRICE']['DISCOUNT_DIFF']){
			?><div class="crossed_price"><?=$arResult['MIN_PRICE']['PRINT_VALUE']?></div><?
		}
		?><div class="modern-menu__product-price"><?=$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
	}
	?></div><?
?></div>