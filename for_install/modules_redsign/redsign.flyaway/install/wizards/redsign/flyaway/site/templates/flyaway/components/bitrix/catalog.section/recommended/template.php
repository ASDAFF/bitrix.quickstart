<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0):
	?><div  class="product-recom"><?
		?><div class="product-recom-title hidden-xs hidden-sm"><?=GetMessage('CATALOG_POPUP')?></div><?
		?><ul class="product-recom-list row"><?
			foreach($arResult['ITEMS'] as $arItem):
				if(empty($arItem['OFFERS'])):
					$arItemShow = &$arItem;
				else:
					$arItemShow = &$arItem['OFFERS'][0];
				endif;
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
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
				?><li class="product-recom-list__item  col-xs-12 col-md-3 col-lg-12" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="product-recom-list__img">
							<a class="products-side-list__img" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
							if(!empty($arItemShow['PREVIEW_PICTURE']['RESIZE']) && is_array($arItemShow['PREVIEW_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['PREVIEW_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(!empty($arItemShow['DETAIL_PICTURE']['RESIZE']) && is_array($arItemShow['DETAIL_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['DETAIL_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(!empty($arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']) && is_array($arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(!empty($arItem['PREVIEW_PICTURE']['RESIZE']) && is_array($arItem['PREVIEW_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItem['PREVIEW_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(!empty($arItem['DETAIL_PICTURE']['RESIZE']) && is_array($arItem['DETAIL_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItem['DETAIL_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(!empty($arItem['OFFERS'])):
								$bNoImg = true;
								foreach($arItem['OFFERS'] as $arOffer):
									if(is_array($arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE'])):
										?><img class="g-product-img" src="<?=$arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>" /><?
										$bNoImg = false;
										break;
									endif;
								endforeach;
								if($bNoImg):
									?><img class="g-product-img" src="<?=$arResult['NO_PHOTO']['src']?>" alt="<?=$strAlt?>"  title="<?=$strTitle?>"><?
								endif;
							else:
								?><img class="g-product-img" src="<?=$arResult['NO_PHOTO']['src']?>"  alt="<?=$strAlt?>" title="<?=$strTitle?>"><?
							endif
							?></a><?
						?></div><?
            ?><div class="product-recom-list__description"><?
							?><a class="product-recom-list__name" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
							if(!empty($arItem['OFFERS'])):
								?><span class="product-recom-list__price"><?=$arItem['OFFERS'][0]['PRICES'][$arParams['PRICE_CODE'][0]]['PRINT_DISCOUNT_VALUE']?></span><?
							else:
								?><span class="product-recom-list__price"><?=$arItem['PRICES'][$arParams['PRICE_CODE'][0]]['PRINT_DISCOUNT_VALUE']?></span><?
							endif
            ?></div><?
            ?><div class="clearfix"></div><?
				?></li><?
			endforeach;
		?></ul><?
	?></div><?
endif;
