<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

use \Bitrix\Main\Localization\Loc;

if ($arParams['DISPLAY_TOP_PAGER'] == 'Y') {
	echo $arResult['NAV_STRING'];
}

ob_start();

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
	?><div class="owlslider products-owl products-owl-slider products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?> <?
	?>"><?
		foreach($arResult['ITEMS'] as $key1 => $arItem) {
 			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			if(empty($arItem['OFFERS'])){
        $HAVE_OFFERS = false;
        $PRODUCT = &$arItem;
      } else {
        $HAVE_OFFERS = true;
        $PRODUCT = &$arItem['OFFERS'][0];

    }
			$IS_PREVIEW_TEXT = 'N';
			if (isset($arItem['PREVIEW_TEXT']) && $arItem['PREVIEW_TEXT'] != '') {
				$IS_PREVIEW_TEXT = 'Y';
			}

			?><div class="products__item item <?
				if ($IS_PREVIEW_TEXT == 'Y') {
					?>products__item_wide <?
				}
				?>js-element <?
				?>js-elementid<?=$arItem['ID']?> <?
				?>JS-Compare <?
				?>JS-Toggle <?
				?>" <?
				?>data-elementid="<?=$arItem['ID']?>" <?
				?>id="<?=$this->GetEditAreaId($arItem["ID"]);?>"<?
				?>data-toggle="{'classActive': 'products__item_active'}" <?
				?>><?

				?><div class="row"><?
					?><div class="col col-md-12"><?
						?><div class="products__in"><?
							// PICTURE
							?><div class="products__pic"><?
								?><a class="JS-Compare-Label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?

									$strTitle = (
										isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != ''
										? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
										: $arItem['NAME']
									);
									$strAlt = (
										isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != ''
										? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]
										: $arItem['NAME']
									);

									if (isset($arItem['FIRST_PIC']['RESIZE']['src']) && trim($arItem['FIRST_PIC']['RESIZE']['src']) != '') {
										?><img class="products__img" src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
									} else {
										?><img class="products__img" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>" /><?
									}
								?></a><?
								?><div class="marks"><?
									if ($arItem['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_action"><?=Loc::getMessage('RS_ACTION_ITEM');?></span><?
									}

									if ($arItem['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_hit"><?=Loc::getMessage('RS_BESTSELLER_ITEM');?></span><?
									}

									if ($arItem['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y') {
										?><span class="marks__item marks__item_new"><?=Loc::getMessage('RS_NEW_ITEM');?></span><?
									}
								?></div><?
							?></div><?

							?><div class="products__data"><?
								// NAME
								?><div class="products__name"><?
									?><a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br /><?
								?></div><?

								?><div class="hidden-xs products__category separator"><?
								if ('Y' == $arParams['SHOW_SECTION_URL'] && isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])) {
										?><a class="category-label" href="<? echo $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL']?>"><?
											echo $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME'];
										?></a><?
								}
								?></div><?

								?><div class="visible-xs separator"></div><?

								// PRICES
								?><div class="products__prices"><?
									if (count($PRODUCT['PRICES']) > 1) {
										foreach ($arResult['PRICES'] as $key1 => $titlePrices) {
											if (isset($PRODUCT['PRICES'][$key1])) {
												?><div class="prices"><?
													?><div class="hidden-xs prices__title"><?=$titlePrices['TITLE']?></div><?
													?><div class="prices__values"><?
														if ($PRODUCT['PRICES'][$key1]['DISCOUNT_DIFF'] > 1) {
															?><div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['PRICES'][$key1]['PRINT_VALUE']?></div><?
															?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div><?
														} else {
															?><div class="prices__val prices__val_cool"><?=$PRODUCT['PRICES'][$key1]['PRINT_DISCOUNT_VALUE']?></div><?
														}
													?></div><?
												?></div><?
											}
										}
									} else {
										if (isset($PRODUCT['MIN_PRICE'])) {
											?><div class="prices"><?
												?><div class="hidden-xs prices__title"></div><?
												?><div class="prices__values"><?
													if (IntVal($PRODUCT['MIN_PRICE']['DISCOUNT_DIFF']) > 0) {
														?><div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div><?
														?><div class="prices__val prices__val_cool prices__val_new"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													} else {
														?><div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
													}
												?></div><?
											?></div><?
										}
									}
								?></div><?

							?></div><?
						?></div><?
					?></div><?
				?></div><?
			?></div><?
		}

	?></div><?

} elseif($arParams['SHOW_ERROR_EMPTY_ITEMS'] == "Y") {
	?><div class="alert alert-info" role="alert"><?=Loc::getMessage('RS.FLYAWAY.NO_PRODUCTS')?></div><?
}

if ($arParams['DISPLAY_BOTTOM_PAGER'] == 'Y') {
	echo $arResult['NAV_STRING'];
}

$templateData = ob_get_flush();
