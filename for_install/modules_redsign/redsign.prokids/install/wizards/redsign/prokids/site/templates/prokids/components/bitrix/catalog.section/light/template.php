<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$this->SetViewTarget('paginator');
if($arParams['IS_AJAXPAGES']!="Y" && $arParams['DISPLAY_TOP_PAGER']=='Y')
{
	echo $arResult['NAV_STRING'];
}
$this->EndViewTarget();

if( isset($arResult['ITEMS']) )
{
	?><div class="light clearfix"><?
		foreach($arResult['ITEMS'] as $key1 => $arItem)
		{
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
			$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
			if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
			?><div class="js-element js-elementid<?=$arItem['ID']?> <?if($HAVE_OFFERS):?>offers<?else:?>simple<?endif;?>" data-elementid="<?=$arItem['ID']?>" id="<?=$this->GetEditAreaId($arItem["ID"]);?>"><?
				?><div class="name"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div><?
				?><div class="pic"><?
					// PICTURE
					?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
						if(isset($arItem['FIRST_PIC']))
						{
							?><img src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$arItem['FIRST_PIC']['ALT']?>" title="<?=$arItem['FIRST_PIC']['TITLE']?>" /><?
						} else {
							?><img src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>" /><?
						}
					?></a><?
				?></div><?
				// PRICE
				if( isset($arItem['MIN_PRICE']) )
				{
					?><div class="prices"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div><?
				}
				// ADD2BASKET
				?><noindex><div class="buy clearfix"><?
				if($HAVE_OFFERS)
				{
					$BUY_ID = 0;//$arItem['OFFERS'][0]['ID'];
					?><a rel="nofollow" class="go2detail" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=GetMessage('GO2DETAIL')?></a><?
				} else {
					$BUY_ID = $PRODUCT['ID'];
					?><form class="add2basketform js-buyform<?=$arItem['ID']?> js-synchro<?if(!$PRODUCT['CAN_BUY']):?> cantbuy<?endif;?> clearfix" name="add2basketform"><?
						?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET"><?
						?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="js-add2basketpid" value="<?=$BUY_ID?>"><?
						?><a rel="nofollow" class="submit add2basket" href="#" title="<?=GetMessage('ADD2BASKET')?>"><?=GetMessage('CT_BCE_CATALOG_ADD')?></a><?
						?><a rel="nofollow" class="inbasket" href="<?=$arParams['BASKET_URL']?>" title="<?=GetMessage('INBASKET_TITLE')?>"><?=GetMessage('INBASKET')?></a><?
						?><input type="submit" name="submit" class="noned" value="" /><?
					?></form><?
				}
				?></div></noindex><?
			?></div><?
		}
	?></div><?
}