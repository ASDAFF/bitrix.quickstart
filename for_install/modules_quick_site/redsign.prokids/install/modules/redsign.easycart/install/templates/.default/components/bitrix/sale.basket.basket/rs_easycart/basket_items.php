<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Sale\DiscountCouponsManager;

if($normalCount>0) {
	?><table class="rsec_table"><?
		?><thead><?
			?><tr><?
				?><th class="rsec_hov"></th><?
				?><th colspan="3"><?=GetMessage('SALE_COLUMN_PRODUCTS')?></th><?
				?><th class="rsec_cen"><?=GetMessage('SALE_PRICE')?></th><?
				?><th class="rsec_cen"><?=GetMessage('SALE_QUANTITY')?></th><?
				?><th class="rsec_cen"><?=GetMessage('SALE_SUM')?></th><?
				?><th class="rsec_cen"><?=GetMessage('SALE_DELETE')?></th><?
			?></tr><?
		?></thead><?
		?><tbody><?
			foreach($arResult['GRID']['ROWS'] as $k => $arItem) {
				if($arItem['DELAY']=='N' && $arItem['CAN_BUY']=='Y') {
					?><tr class="rsec_jsline" data-id="<?=$arItem['ID']?>"><?
						?><td class="rsec_hov"></td><?
						?><td class="rsec_min rsec_cen"><?
							// checkbox
							?><input type="checkbox" name="DELETE_<?=$arItem['ID']?>" id="DELETE_<?=$arItem['ID']?>" value="Y"><label class="rsec_checkbox" for="DELETE_<?=$arItem['ID']?>"></label><?
						?></td><?
						?><td class="rsec_image rsec_min"><?
							?><div><?
							// image
								if(strlen($arItem['PREVIEW_PICTURE_SRC'])>0) {
									$url = $arItem['PREVIEW_PICTURE_SRC'];
								} elseif(strlen($arItem['DETAIL_PICTURE_SRC'])>0) {
									$url = $arItem['DETAIL_PICTURE_SRC'];
								} else {
									$url = $arResult['NO_PHOTO']['src'];
								}
								if(strlen($arItem['DETAIL_PAGE_URL'])>0) {
									?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
								}
								?><img src="<?=$url?>" alt="" /><?
								if(strlen($arItem['DETAIL_PAGE_URL'])>0) {
									?></a><?
								}
							?></div><?
						?></td><?
						?><td class="rsec_nowrap"><?
							// name
							if(strlen($arItem['DETAIL_PAGE_URL'])>0) {
								?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
							}
							?><?=$arItem['NAME']?><?
							if(strlen($arItem['DETAIL_PAGE_URL'])>0) {
								?></a><?
							}
						?></td><?
						?><td class="rsec_nowrap rsec_min rsec_padd"><?=$arItem['PRICE_FORMATED']?></td><?
						?><td class="rsec_nowrap rsec_min rsec_padd"><?
							?><span class="rsec_quantity"><?
								?><a class="rsec_minus">-</a><?
								?><input type="text" class="rsec_quantity" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem['QUANTITY']?>" <?
									?>data-ratio="<?=$arItem['MEASURE_RATIO']?>" <?
									?>data-avaliable="<?=$arItem['AVAILABLE_QUANTITY']?>" /><?
								?><span class="rsec_measurename"><?=$arItem['MEASURE_TEXT']?></span><?
								?><a class="rsec_plus">+</a><?
							?></span><?
						?></td><?
						?><td class="rsec_nowrap rsec_min rsec_cen rsec_padd"><?
							?><?=$arItem['SUM']?><?
						?></td><?
						?><td class="rsec_delete rsec_min rsec_cen rsec_padd"><?
							?><a class="rsec_delete" href="#DELETE_<?=$arItem["ID"]?>" title="<?=GetMessage('SALE_DELETE')?>"><i class="rsec_iconka"></i></a><?
						?></td><?
					?></tr><?
				}
			}
		?></tbody><?
	?></table><?
	?><div class="rsec_buttons rsec_clearfix"><?
		?><div class="rsec_leftp"><?
			if($arParams['HIDE_COUPON']!='Y') {
				?><div class="rsec_coupon rsec_totaltext"><?
					?><input class="rsec_cop <?=$couponClass?>" type="text" id="coupon" name="COUPON" value="" placeholder="<?=GetMessage('SALE_COUP_PLACEHOLDER')?>" /><?
					?><input class="rsec_btn rsec_btn1 rsec_coup" type="button" name="BasketRefresh" value="<?=GetMessage('SALE_APPLY_BTN')?>" /><?
				?></div><?
				if (!empty($arResult['COUPON_LIST'])) {
					?><div class="rsec_clearfix"></div><?
					foreach ($arResult['COUPON_LIST'] as $oneCoupon) {
						$couponClass = 'disabled';
						switch ($oneCoupon['STATUS']) {
							case DiscountCouponsManager::STATUS_NOT_FOUND:
							case DiscountCouponsManager::STATUS_FREEZE:
								$couponClass = 'bad';
								break;
							case DiscountCouponsManager::STATUS_APPLYED:
								$couponClass = 'good';
								break;
						}
						?><div class="rsec_coupon_result <?=$couponClass?>"><?
							?><?=GetMessage('COUPON')?>: <span class="rsec_coupon_code"><?=htmlspecialcharsbx($oneCoupon['COUPON'])?></span><?
							if (isset($oneCoupon['CHECK_CODE_TEXT'])) {
								?> &mdash; <span class="rsec_counpon_note"><?
									echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br />', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
								?></span><?
							}
						?></div><?
					}
					unset($couponClass, $oneCoupon);
				}
			}
			?><input class="rsec_btn rsec_btn2 rsec_delall" type="button" name="BasketRefresh" value="<?=GetMessage('SALE_BTN_DEL_ALL')?>" /><?
			?><input class="rsec_btn rsec_btn2 rsec_delsome" type="button" name="BasketRefresh" value="<?=GetMessage('SALE_DELETE')?>" /><?
		?></div><?
		?><div class="rsec_rightp"><?
			?><span class="rsec_totaltext"><span class="rsec_name"><?=GetMessage('SALE_EC_HEADER_LINK_PRODS')?>:</span><span class="rsec_color">&nbsp;<span class="rsec_take_normalCount"><?=$normalCount?></span></span> &nbsp;<span class="rsec_name"><?=GetMessage('SALE_SUM')?>:</span><span class="rsec_color">&nbsp;<span class="rsec_take_allSum_FORMATED"><?=$arResult['allSum_FORMATED']?></span></span></span><?
			?><input class="rsec_btn rsec_btn1" type="submit" name="BasketRefresh" value="<?=GetMessage('SALE_ORDER')?>" onclick="location.href='<?=$arParams['PATH_TO_ORDER']?>';return false;" /><?
		?></div><?
	?></div><?
	$arHeaders = array('PRICE','QUANTITY','SUM');
	?><input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" /><?
}