<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */
 
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?><?

$sFormTarget = ' target="_blank"';
if($arResult['PAYMENT']['PSA_NEW_WINDOW'] == 'Y') {
	// если в новом окне
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><?
	?><head><?
        	?><meta http-equiv="X-UA-Compatible" content="IE=Edge" /><?
		?><meta http-equiv="imagetoolbar" content="no" /><?
		?><meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET;?>" /><?
		?><title><?=$arResult['PAYMENT']['NAME']?></title><?
		$GLOBALS['APPLICATION']->ShowCSS();
		$GLOBALS['APPLICATION']->ShowHeadScripts();
		$GLOBALS['APPLICATION']->ShowHeadStrings();
	?></head><?
	?><body><?
	$sFormTarget = '';
	$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder.'/new_window.css');
}

// сумма счета
?><div class="onlinedengi-caption"><?
	?><span class="onlinedengi-caption-name"><?
		echo GetMessage('ONLINEDENGI_PAYMENT_AMOUNT');
	?></span><?
	?><span class="onlinedengi-caption-value"><?
		echo SaleFormatCurrency($arResult['ORDER']['AMOUNT'], $arResult['ORDER']['CURRENCY']);
	?></span><?
?></div><?

if($arResult['FIELDS']['mode_type']) {
	$arOnlinedengiCurMode_ =& $arResult['arOnlineDengiAvailablePaymentTypes'][$arResult['FIELDS']['mode_type']];
	// способ оплаты
	?><div class="onlinedengi-caption"><?
		?><span class="onlinedengi-caption-name"><?
			echo GetMessage('ONLINEDENGI_PAYMENT_MODE_TYPE');
		?></span><?
		?><span class="onlinedengi-caption-value"><?
			echo GetMessage($arOnlinedengiCurMode_['lang']);
		?></span><?
		// ссылка на изменение способа оплаты
		if(!$arResult['bAdminModeTypeDefined'] && count($arResult['arModeTypeList']) > 1) {
			?><span class="onlinedengi-href"><?
				?> [<a href="<?=$arResult['sCurPage']?>"><?
					echo GetMessage('ONLINEDENGI_MODETYPE_CHANGE');
				?></a>]<?
			?></span><?
		}
	?></div><?

	// сумма счета для выбранного способа оплаты
	?><div class="onlinedengi-caption"><?
		?><span class="onlinedengi-caption-name"><?
			echo GetMessage('ONLINEDENGI_PAYMENT_AMOUNT_MODE');
		?></span><?
		?><span class="onlinedengi-caption-value"><?
			echo number_format($arResult['FIELDS']['amount'], $arOnlinedengiCurMode_['precision'], '.', ' ').' '.$arOnlinedengiCurMode_['display_currency'];
		?></span><?
	?></div><?
}


if(empty($arResult['ERRORS'])) {
	?><div class="onlinedengi-form-block"><div class="onlinedengi-form-block-pad"><?
		if(empty($arResult['FIELDS']['mode_type'])) {
			// выбор способа оплаты
			?><form method="post" action=""><?
				?><input type="hidden" name="ORDER_ID" value="<?=$arResult['ORDER']['ID']?>" /><?

				?><div class="onlinedengi-field-row"><?
					?><span class="onlinedengi-caption"><?
						?><span class="onlinedengi-caption-name"><?
							echo GetMessage('ONLINEDENGI_SELECT_MODE_TYPE');
						?></span><?
					?></span><?

					/*?><span class="onlinedengi-from-field"><?
						?><select class="inputselect" name="mode_type"><?
							foreach($arResult['arModeTypeList'] as $arItem) {
								?><option value="<?=$arItem['value']?>"><?
									echo $arItem['description'];
								?></option><?
							}
						?></select><?
					?></span><?*/
					?><table class="onlinedengi-pay-table">
						<tr>
					<?
						$i = 0;
						foreach($arResult['arModeTypeList'] as $arItem):
							if(!$i || $i%4 == 0) echo "</tr><tr>";
						?>
							<td align="center" valign="middle">								
									<input name="mode_type" type="radio" id="online_pay_<?=$arItem['value']?>" value="<?=$arItem['value']?>" />
									<label for="online_pay_<?=$arItem['value']?>">
										<img alt="<?=$arItem['description']?>" src="<?=$arItem['img']?>" width="88" height="31"/>
									</label>
							</td>
						<?
							$i++;
						endforeach;	
					?>
						</tr>
					</table>
				</div><?
				?><div class="onlinedengi-from-button"><?
					?><input class="inputsubmit" type="submit" name="mode_type_submit" value="<?=GetMessage('ONLINEDENGI_SELECT_MODE_TYPE_SUBMIT')?>" /><?
				?></div><?
			?></form><?
		} else {
			// заполнение дополнительных полей способа оплаты и отправка данных на OnlineDengi
			?><form<?=$sFormTarget?> action="<?=ONLINEDENGI_PAYMENT_REQUEST_URL?>" method="<?=ONLINEDENGI_PAYMENT_REQUEST_TYPE?>"><?
				$arFields = COnlineDengiPayment::GetModeTypeFieldsById($arResult['FIELDS']['mode_type']);
				if(!empty($arFields) && is_array($arFields)) {
					foreach($arFields as $arItem) {
						$mValue = isset($arItem['value']) ? $arItem['value'] : $arResult['FIELDS'][$arItem['name']];
						if(!isset($arResult['FIELDS'][$arItem['name']]) && !isset($arItem['value'])) {
							?><div class="onlinedengi-field-row"><?
								// поля, которые должен дополнительно заполнить покупатель
								?><div class="onlinedengi-caption"><?
									?><span class="onlinedengi-caption-name"><?
										echo GetMessage($arItem['lang']).': ';
									?></span><?
								?></div><?
        	
								?><div class="onlinedengi-from-field"><?
									?><input type="text" class="inputtext" name="<?=$arItem['name']?>" value="<?=$mValue?>" /><?
								?></div><?
							?></div><?
						} else {
							?><input type="hidden" name="<?=$arItem['name']?>" value="<?=$mValue?>" /><?
						}
					}
				}
				?><div class="onlinedengi-from-button"><?
					?><input class="inputsubmit" type="submit" value="<?=GetMessage('ONLINEDENGI_PAYMENT_SUBMIT')?>" /><?
				?></div><?
			?></form><?
		}
	?></div></div><?
}

if(!empty($arResult['ERRORS'])) {
	// вывод ошибок
	array_walk($arResult['ERRORS'], create_function('&$value, $key', '$value .= " [".$key."]";'));
	ShowMessage(array('TYPE' => 'ERROR', 'MESSAGE' => implode('<br />', $arResult['ERRORS'])));
}
		
if($arResult['PAYMENT']['PSA_NEW_WINDOW'] == 'Y') {
	// если в новом окне
	?></body></html><?
}
