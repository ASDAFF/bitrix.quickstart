<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!function_exists('RSGoProCatalogMenuElement')) {
	function RSGoProCatalogMenuElement($ELEMENT_ID=0,$arParams) {
		global $APPLICATION;
		if(IntVal($ELEMENT_ID)>0) {
			?><!-- element in menu --><ul class="elementinmenu lvl5"><?
$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"inmenu",
	Array(
		"IBLOCK_TYPE" => "",
		"IBLOCK_ID" => $arParams['IBLOCK_ID'][0],
		"PROPERTY_CODE" => "",
		"META_KEYWORDS" => "",
		"META_DESCRIPTION" => "",
		"BROWSER_TITLE" => "",
		"BASKET_URL" => "",
		"ACTION_VARIABLE" => "",
		"PRODUCT_ID_VARIABLE" => "",
		"SECTION_ID_VARIABLE" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"PRICE_CODE" => $arParams['PRICE_CODE'],
		"USE_PRICE_COUNT" => "",
		"SHOW_PRICE_COUNT" => "",
		"PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
		"PRICE_VAT_SHOW_VALUE" => "N",
		"USE_PRODUCT_QUANTITY" => "Y",
		"OFFERS_CART_PROPERTIES" => $arParams['OFFERS_CART_PROPERTIES'],
		"OFFERS_FIELD_CODE" => $arParams['OFFERS_FIELD_CODE'],
		"OFFERS_PROPERTY_CODE" => array("SKU_MORE_PHOTO"),
		"OFFERS_SORT_FIELD" => "catalog_PRICE_".$arParams['SKU_PRICE_SORT_ID'],
		"OFFERS_SORT_ORDER" => "ASC",
		"OFFERS_LIMIT" => "0",
		"ELEMENT_ID" => $ELEMENT_ID,
		"ELEMENT_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
		"CURRENCY_ID" => $arParams['CURRENCY_ID'],
		"USE_ELEMENT_COUNTER" => "N",
		"USE_COMPARE" => "N",
		"COMPARE_URL" => "",
		"COMPARE_NAME" => "",
		// seo
		"ADD_SECTIONS_CHAIN" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"ADD_ELEMENT_CHAIN" => "N",
	),
	false
);
			?></ul><!-- /element in menu --><?
		}
	}
}

if(is_array($arResult) && count($arResult)>0) {
	?><div class="catalogmenucolumn"><?
		?><ul class="catalogmenu clearfix"><?
			?><li class="parent"><a href="<?=$arParams['CATALOG_PATH']?>" class="parent"><?=GetMessage('RSGOPRO_CATALOG')?><i class="menu icon<?if($arParams['IS_MAIN']=='Y'):?> show<?endif;?>"></i></a><?
			?><ul class="first clearfix lvl1<?if($arParams['IS_MAIN']=='Y'):?> show<?endif;?>"><?
			$previousLevel = 0;
			$index = 1;
			$max = $arParams['RSGOPRO_MAX_ITEM'];
			$last_lvl1 = false;
			foreach($arResult as $arItem){
				if($previousLevel>0 && $arItem['DEPTH_LEVEL']<$previousLevel){
					echo str_repeat('</ul></li><!-- the end -->', ($previousLevel - $arItem['DEPTH_LEVEL'] - 1));
					if($arItem['DEPTH_LEVEL']==1 && $last_lvl1!==false && $arResult[$last_lvl1]['PARAMS']['ELEMENT']=='Y'){
						RSGoProCatalogMenuElement($arResult[$last_lvl1]['PARAMS']['ELEMENT_ID'],$arParams);
					}
					echo '</ul></li>';
				}
				if($arItem['DEPTH_LEVEL'] == 1){
					$last_lvl1 = $arItem['ITEM_INDEX'];
				}
				if($arItem['IS_PARENT']){
					if($arItem['DEPTH_LEVEL'] == 1){
						?><li class="first<?if($index>$max):?> more<?endif;?><?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?><i class="menu icon pngicons"></i></a><?
							?><ul class="lvl<?if($arItem['DEPTH_LEVEL']>3):?>4<?else:?><?=($arItem['DEPTH_LEVEL']+1)?><?endif;?>"><?
						$index++;
					} else {
						?><li class="sub<?if($arItem['SELECTED']):?> selected<?endif?>"><a href="<?=$arItem['LINK']?>" class="sub" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?><i class="menu icon pngicons"></i></a><?
							?><ul class="lvl<?if($arItem['DEPTH_LEVEL']>3):?>4<?else:?><?=($arItem['DEPTH_LEVEL']+1)?><?endif;?>"><?
					}
				} else {
					if($arItem['DEPTH_LEVEL'] == 1){
						?><li class="first<?if($index>$max):?> more<?endif;?><?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?></a></li><?
						$index++;
					} else {
						?><li class="sub<?if($arItem['SELECTED']):?> selected<?endif?>"><a href="<?=$arItem['LINK']?>" class="sub" title="<?=$arItem['TEXT']?>"><?=$arItem['TEXT']?></a></li><?
					}
				}
				$previousLevel = $arItem['DEPTH_LEVEL'];
			}
			if($previousLevel>1){
				echo str_repeat('</ul></li>', ($previousLevel-2) );
				if($last_lvl1!==false && $arResult[$last_lvl1]['PARAMS']['ELEMENT']=='Y'){
					RSGoProCatalogMenuElement($arResult[$last_lvl1]['PARAMS']['ELEMENT_ID'],$arParams);
				}
				echo '</ul></li>';
			}
			if($index>($max+1)){
				?><li class="first morelink lastchild"><a href="<?=$arParams['CATALOG_PATH']?>" class="first morelink"><?=GetMessage('RSGOPRO_MORE')?><i class="icon pngicons"></i></a></li><?
			}
			?></ul></li><?
		?></ul><?
		
		?><ul class="catalogmenusmall clearfix"><?
			?><li class="parent"><a href="<?=$arParams['CATALOG_PATH']?>" class="parent"><?=GetMessage('RSGOPRO_CATALOG')?><i class="menu icon"></i></a><?
			?><ul class="first clearfix lvl1 noned"><?
				foreach($arResult as $arItem){
					if($arItem['DEPTH_LEVEL'] == 1){
						?><li class="first<?if($arItem['IS_LAST_LVL1']=='Y'):?> lastchild<?endif;?>"><a href="<?=$arItem['LINK']?>" class="first<?if($arItem['SELECTED']):?> selected<?endif?>"><?=$arItem['TEXT']?></a></li><?
					}
				}
			?></ul><?
		?></ul><?
	?></div><?
}