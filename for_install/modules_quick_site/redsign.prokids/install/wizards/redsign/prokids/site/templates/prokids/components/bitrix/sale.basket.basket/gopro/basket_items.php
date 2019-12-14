<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Sale\DiscountCouponsManager;

$normalCount = IntVal( count($arResult['ITEMS']['AnDelCanBuy']) );

?><?=ShowError($arResult['ERROR_MESSAGE']);?><?

if( $arResult['HAVE_PRODUCT_TYPE']['ITEMS'] )
{
	?><div class="part items clearfix"><?

		ShowTable($arParams,$arResult);

		?><div class="btns clearfix"><?
			if($arParams['HIDE_COUPON']!='Y') {
				?><div class="coupon"><?
					?><input class="cop " type="text" id="coupon" name="COUPON" value="" placeholder="<?=GetMessage('STB_COUPON_PROMT')?>" /><?
					?><input class="btn btn3" type="submit" name="BasketRefresh" value="<?=GetMessage('SALE_ACCEPT')?>" /><?
					if (!empty($arResult['COUPON_LIST'])) {
						?><div class="clearfix"></div><?
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
							?><div class="coupon_result <?=$couponClass?>"><?
								?><?=GetMessage('COUPON')?>: <span class="coupon_code"><?=htmlspecialcharsbx($oneCoupon['COUPON'])?></span><?
								if (isset($oneCoupon['CHECK_CODE_TEXT'])) {
									?> &mdash; <span class="counpon_note"><?
										echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br />', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
									?></span><?
								}
							?></div><?
						}
						unset($couponClass, $oneCoupon);
					}
				?></div><?
			}
			?><span class="totaltext"><?
				?><span class="name"><?=GetMessage('SALE_EC_HEADER_LINK_PRODS')?>:</span>&nbsp;<span class="take_normalCount"><?=$normalCount?></span> <?
				?>&nbsp;<span class="name"><?=GetMessage('SALE_SUM')?>:</span>&nbsp;<span class="take_allSum_FORMATED"><?=$arResult['allSum_FORMATED']?></span><?
			?></span><?
			?><div class="clear"></div><?
			?><input class="btn btn3 clearitems" type="button" name="BasketRefresh" value="<?=GetMessage('SALE_BTN_DEL_ALL')?>" /><?
			?><input class="btn btn3 clearsolo" type="button" name="BasketRefresh" value="<?=GetMessage('SALE_DELETE')?>" /><?
			?><span class="separator"></span><?
			?><input class="btn btn1" type="submit" name="BasketOrder" value="<?=GetMessage('SALE_ORDER')?>" onclick="location.href='<?=$arParams['PATH_TO_ORDER']?>';return false;" /><?
		?></div><?

	?></div><?
}