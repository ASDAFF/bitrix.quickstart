<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0){
	?><div class="title_search_result-catalog"><?
		foreach($arResult['ITEMS'] as $key1 => $arItem){
			if($key1>0) echo '<div class="search_popup__sep"></div>';
			?><div class="title_search_result-catalog-item"><a class="title_search_result-catalog-item_overlay" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"></a><div class="title_search_result-catalog-item_inner"><?
				if($arItem['IMAGES'][0]['src']!=""):?>
					<div class="title_search_result-catalog-item-img"><img src="<?=$arItem['IMAGES'][0]['src']?>" width="<?=$arItem['IMAGES'][0]['width']?>" height="<?=$arItem['IMAGES'][0]['height']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" /></div><?
				endif;
				?><div class="title_search_result-catalog-item-name"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a></div><?
				?><div class="title_search_result-catalog-item-price clearfix"><?
				if(is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0){
					?><div class="price"><?=GetMessage('SKU_PRICE_FROM')?> <?=$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
					if($arItem['OFFERS'][0]['MIN_PRICE']['DISCOUNT_DIFF']){
						?><div class="crossed_price"><?=$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_VALUE']?></div><?
					}
				}
				else{
					?><div class="price"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
					if($arItem['MIN_PRICE']['DISCOUNT_DIFF']){
						?><div class="crossed_price"><?=$arItem['MIN_PRICE']['PRINT_VALUE']?></div><?
					}
				}
				?></div><?
			?></div></div><?
		}
	?></div><?
}