<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0):
	?><div class="product-recom product-recom_main clearfix"><?
		?><h2><?=Loc::getMessage('CATALOG_POPUP')?></h2><?
		?><div class="owl product-recom-list" <?
			?>data-changespeed="<?if (IntVal($arParams["RS_OWL_CHANGE_SPEED"]) < 1) : ?>2000<?else:?><?=$arParams["RS_OWL_CHANGE_SPEED"]?><?endif;?>" <?
			?>data-changedelay="<?if (IntVal($arParams["RS_OWL_CHANGE_DELAY"]) < 1) : ?>8000<?else:?><?=$arParams["RS_OWL_CHANGE_DELAY"]?><?endif;?>" <?
			?>data-margin="20" <?
			?>data-responsive='{<?
				?>"0": {"items":"<?=(IntVal($arParams['RS_OWL_PHONE']) > 0 ? $arParams['RS_OWL_PHONE'] : 1)?>"},<?
				?>"740": {"items":"<?=(IntVal($arParams['RS_OWL_TABLET']) > 0 ? $arParams['RS_OWL_TABLET'] : 1)?>"},<?
				?>"1080": {"items":"<?=(IntVal($arParams['RS_OWL_MID']) > 0 ? $arParams['RS_OWL_MID'] : 1)?>"},<?
				?>"1620": {"items":"<?=(IntVal($arParams['RS_OWL_PC']) > 0 ? $arParams['RS_OWL_PC'] : 1)?>"}<?
			?>}'<?
		?>><?
			foreach($arResult['ITEMS'] as $arItem):
				if(empty($arItem['OFFERS'])):
					$arItemShow = &$arItem;
				else:
					$arItemShow = &$arItem['OFFERS'][0];
				endif;
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

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

				?><div class="item product-recom-list__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="product-recom-list__img">
							<a class="products-side-list__img" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
							if(isset($arItemShow['PREVIEW_PICTURE']['RESIZE']) && is_array($arItemShow['PREVIEW_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['PREVIEW_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(isset($arItemShow['DETAIL_PICTURE']['RESIZE']) && is_array($arItemShow['DETAIL_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['DETAIL_PICTURE']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(isset($arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']) && is_array($arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItemShow['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" /><?
							elseif(isset($arItem['PREVIEW_PICTURE']['RESIZE']) && is_array($arItem['PREVIEW_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItem['PREVIEW_PICTURE']['RESIZE']['src']?>" alt="<?=$strTitle?>" title="<?=$strAlt?>" /><?
							elseif(isset($arItem['DETAIL_PICTURE']['RESIZE']) && is_array($arItem['DETAIL_PICTURE']['RESIZE'])):
								?><img class="g-product-img" src="<?=$arItem['DETAIL_PICTURE']['RESIZE']['src']?>" alt="<?=$strTitle?>" title="<?=$strAlt?>" /><?
							elseif(!empty($arItem['OFFERS'])):
								$bNoImg = true;
								foreach($arItem['OFFERS'] as $arOffer):
									if(is_array($arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE'])):
										?><img class="g-product-img" src="<?=$arOffer['PROPERTIES'][$arParams['PROPCODE_IMAGES']]['VALUE'][0]['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>"><?
										$bNoImg = false;
										break;
									endif;
								endforeach;
								if($bNoImg):
									?><img class="g-product-img" src="<?=$arResult['NO_PHOTO']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>"><?
								endif;
							else:
								?><img class="g-product-img" src="<?=$arResult['NO_PHOTO']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>"><?
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
				?></div><?
			endforeach;
		?></div><?
	?></div><?
endif;
