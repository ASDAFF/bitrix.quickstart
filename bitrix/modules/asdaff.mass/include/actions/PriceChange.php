<?
class CWDA_PriceChange extends CWDA_Plugin {
	CONST GROUP = 'PRICES';
	CONST CODE = 'PRICE_CHANGE';
	CONST NAME = 'Пересчет цен - наценки и скидки';
	//
	static function GetDescription() {
		$Descr = 'Плагин для изменения цен. Доступны как увеличения цен, так и снижения. Имеется возможность увеличивать/уменьшать цены как процентным указанием (напр., <b>5%</b> [равно как и +5%] и <b>-5%</b>), так и числовым (напр., <b>200</b> [равно как и +200], и <b>-200</b>). Также доступна возможность округления несколькими способами.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'ROUND_3' => 'тысяч',
			'ROUND_2' => 'сотен',
			'ROUND_1' => 'десятков',
			'ROUND_0' => 'целых',
			'ROUND_M1' => 'десятых',
			'CASE_LOWER' => 'нижний («для примера»)',
			'CASE_UPPER' => 'верхний («ДЛЯ ПРИМЕРА»)',
			'CASE_UCWORDS' => 'первые буквы («Для Примера»)',
			'ALERT_NO_PRICE_SOURCE' => 'Укажите поле с ценой',
			'PRICE_RUB_FULL' => 'руб',
			'PRICE_RUB_SHORT' => 'р',
			//
			'FIELD_SOURCE' => 'Цена:',
			//
			'SELECT_PRICE_SOURCE' => 'Укажите здесь свойство инфоблока или цену торгового каталога (в случае установленного модуля «Каталог»), которое следует взять для сохранение в другое свойство/цену. Показаны значения только типов «Число» и «Строка».',
			'ADDITIONAL_SETTINGS' => 'Дополнительные опции',
			'PARAM_USE_PURCHASE' => 'Пересчет относительно закупочной цены',
			'PARAM_CHANGE_VALUE' => 'Значение наценки/скидки',
			'PARAM_ROUND' => 'Округлить до:',
			'PARAM_FORMAT' => 'Формат:',
			'PARAM_OFFERS' => 'Применить для торговых предложений',
			'PARAM_LIMIT_BELOW' => 'Ограничить минимальную цену<br/>относительно закупочной цены',
			//
			'HINT_PARAM_USE_PURCHASE' => 'Если данная опция включена, цена будет подсчитана на основе закупочной цены.',
			'HINT_PARAM_CHANGE_VALUE' => 'Значение можно указывать как в процентах, так и числом, например: <ul style=\'font-family:monospace\'><li>+5%</li><li>200</li><li>+200</li><li>-3%</li><li>-200</li></ul>',
			'HINT_PARAM_ROUND' => 'Опция позволяет округлять загружаемые цены до тысяч (12345.67 => 12000), сотен (12345.67 => 12300), десятков (12345.67 => 12350) и целых (12345.67 => 12346)',
			'HINT_PARAM_FORMAT' => 'Опция позволяет изменить формат сохранения валюты. Применяется только для сохранения цены в свойство инфоблока; при сохранении цены в торговый каталог сохраняется только число, цена форматируется на основании настроек валют в модуле «Валюты».',
			'HINT_PARAM_OFFERS' => 'Опция позволяет применить пересчет цен в том числе для торговых предложений, относящихся к обрабатываемым товарам',
			'HINT_PARAM_LIMIT_BELOW' => 'Опция позволяет снижать цены (применять скидки), ограничивая при этом ее нижнее значение - чтобы итоговая цена товаров не была ниже закупочной (плюс некоторый процент).',
			//
			'CATALOG_PURCHASING_PRICE' => 'Закупочная цена',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		?>
		<script>
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if ($.trim($('#wda_field_source').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_PRICE_SOURCE',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			// Source
			var SelectSource = $('#wda_field_source').html(Select.html()); // Price
			SelectSource.find('optgroup').not('optgroup[data-group=PRICES]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectSource.find('optgroup[data-group=PROPERTIES] option').not('[data-type^=N]').not('[data-type=S]').remove();
			SelectSource.find('optgroup[data-group=PRICES]').append('<option value="CATALOG_PURCHASING_PRICE" data-type="P"><?=self::GetMessage('CATALOG_PURCHASING_PRICE',true);?></option>');
			SelectSource.change();
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html()); // Property
			SelectTarget.find('optgroup').not('optgroup[data-group=PRICES]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectTarget.find('optgroup[data-group=PROPERTIES] option').not('[data-type^=N]').not('[data-type=S]').remove();
			SelectTarget.find('[value=ID],[value=ACTIVE_DATE],[value=SECTION_GLOBAL_ACTIVE]').remove();
			SelectTarget.find('optgroup[data-group=PRICES]').append('<option value="CATALOG_PURCHASING_PRICE" data-type="P"><?=self::GetMessage('CATALOG_PURCHASING_PRICE',true);?></option>');
			SelectTarget.change();
		}
		</script>
		<?
	}
	static function GetPriceFormats() {
		// 1. [# руб] - обозначение валюты
		// 2. [ ] - разделитель тысяч
		// 3. [.] - разделитель дроби
		// 4. [2] - количество десятичных символов
		// 5. [Y] - скрывать незначащие десятичные нули
		$arResult = array(
			'[# '.self::GetMessage('PRICE_RUB_FULL').'.][ ][.][2][Y]',
			'[# '.self::GetMessage('PRICE_RUB_FULL').'][ ][.][2][Y]',
			'[# '.self::GetMessage('PRICE_RUB_SHORT').'.][ ][.][2][Y]',
			'[# '.self::GetMessage('PRICE_RUB_SHORT').'][ ][.][2][Y]',
			'[$#][ ][.][2][Y]',
			'[&euro;#][ ][.][2][Y]',
		);
		return $arResult;
	}
	static function FormatPrice($Price, $Format) {
		$Format = str_replace("][", "]\n[", $Format);
		$arFormat = explode("\n",$Format);
		if (is_array($arFormat)) {
			foreach($arFormat as $Key => $Value) {
				$arFormat[$Key] = substr($Value,1,-1);
			}
			$Price = FloatVal($Price);
			if ($arFormat[4]=='Y' && $Price==IntVal($Price)) {
				$arFormat[3] = 0;
			}
			$Price = number_format($Price,$arFormat[3],$arFormat[2],$arFormat[1]);
			$Format = str_replace('#',$Price,$arFormat[0]);
			return $Format;
		}
		return '';
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_SOURCE');?></div>
			<div>
				<div><select name="params[field_source]" id="wda_field_source" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_SOURCE_PRICE'));?></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_ADDITIONAL_SETTINGS');?></div>
			<div class="wda_additional_params" id="wda_additional_params_<?=self::CODE?>">
				<table>
					<tbody>
						<tr>
							<td class="label" colspan="2">
								<label for="wda_input_change_value"><?=self::GetMessage('PARAM_CHANGE_VALUE');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_CHANGE_VALUE'));?>
							</td>
							<td class="value">
								<input type="text" name="params[change_value]" value="+10%" size="10" maxlength="10" id="wda_input_change_value" />
							</td>
						</tr>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[use_purchase]" value="Y" id="wda_checkbox_use_purchase" />
							</td>
							<td class="label">
								<label for="wda_checkbox_use_purchase"><?=self::GetMessage('PARAM_USE_PURCHASE');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_USE_PURCHASE'));?>
							</td>
							<td class="value"></td>
						</tr>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[round]" value="Y" id="wda_checkbox_round" />
							</td>
							<td class="label">
								<label for="wda_checkbox_round"><?=self::GetMessage('PARAM_ROUND');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_ROUND'));?>
							</td>
							<td class="value">
								<select name="params[round_value]">
									<?foreach(array(3,2,1,0,-1) as $fRound):?>
										<option value="<?=$fRound;?>"<?if($fRound==0):?> selected="selected"<?endif?>><?=self::GetMessage('ROUND_'.str_replace('-','M',$fRound));?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[format]" value="Y" id="wda_checkbox_format" />
							</td>
							<td class="label">
								<label for="wda_checkbox_format"><?=self::GetMessage('PARAM_FORMAT');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_FORMAT'));?>
							</td>
							<td class="value">
								<?$arFormats = self::GetPriceFormats();?>
								<select name="params[format_value]">
									<?foreach($arFormats as $strFormat):?>
										<option value="<?=$strFormat;?>"><?=self::FormatPrice(45678.90, $strFormat);?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[offers]" value="Y" id="wda_checkbox_offers" />
							</td>
							<td class="label" colspan="2">
								<label for="wda_checkbox_offers"><?=self::GetMessage('PARAM_OFFERS');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_OFFERS'));?>
							</td>
						</tr>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[limit_below]" value="Y" id="wda_checkbox_limit_below" />
							</td>
							<td class="label">
								<label for="wda_checkbox_limit_below"><?=self::GetMessage('PARAM_LIMIT_BELOW');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_LIMIT_BELOW'));?>
							</td>
							<td>
								<input type="text" name="params[limit_below_value]" value="+10%" size="10" maxlength="5" id="wda_limit_below_value" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}
	static function ParsePrice($Price) {
		if (preg_match_all('#([\d\,.]{1})#is',$Price,$M)) {
			$Price = str_replace(',','.',implode($M[1]));
		}
		$Price = FloatVal($Price);
		return $Price;
	}
	static function ChangePrice($Price, $Delta) {
		if ($Price<0) {
			$Price = 0;
		}
		if (preg_match_all('#([\d\,\.\+\-%]{1})#is',$Delta,$M)) {
			$Delta = str_replace(',','.',implode($M[1]));
		}
		if (preg_match('#^\+([\d\,\.]+)%$#',$Delta,$M) || preg_match('#^([\d\,\.]+)%$#',$Delta,$M)) {
			$Price = $Price + Round($Price*$M[1]/100,2);
		} elseif (preg_match('#^\-([\d\,\.]+)%$#',$Delta,$M)) {
			$Price = $Price - Round($Price*$M[1]/100,2);
		} elseif (preg_match('#^\+([\d\,\.]+)$#',$Delta,$M) || preg_match('#^([\d\,\.]+)$#',$Delta,$M)) {
			$Price = $Price + $M[1];
		} elseif (preg_match('#^\-([\d\,\.]+)$#',$Delta,$M)) {
			$Price = $Price - $M[1];
		}
		if ($Price<0) {
			$Price = 0;
		}
		return $Price;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$UsePurchasePrice = $Params['use_purchase']=='Y';
		// Source
		$SourcePriceID = false;
		if(preg_match('#^CATALOG_PRICE_(\d+)$#i',$Params['field_source'],$M)) {
			$SourcePriceID = IntVal($M[1]);
		}
		$SourcePropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_source'],$M)) {
			$SourcePropertyID = IntVal($M[1]);
		}
		$SourcePurchasing = false;
		if($Params['field_source']=='CATALOG_PURCHASING_PRICE') {
			$SourcePurchasing = true;
		}
		$SourceCurrency = false;
		if ($SourcePriceID>0 || $SourcePropertyID>0 || $SourcePurchasing) {
			// 1. prepare source
			$PriceRaw = 0;
			if ($SourcePurchasing || $UsePurchasePrice) {
				if (CModule::IncludeModule('catalog')) {
					$arProduct = CCatalogProduct::GetByID($ElementID);
					if (is_array($arProduct)) {
						$PriceRaw = $arProduct['PURCHASING_PRICE'];
						$SourceCurrency = $arProduct['PURCHASING_CURRENCY'];
					}
				}
			} elseif ($SourcePriceID>0) {
				$PriceRaw = FloatVal($arElement['CATALOG_PRICE_'.$SourcePriceID]);
				$SourceCurrency = $arElement['CATALOG_CURRENCY_'.$SourcePriceID];
			} elseif ($SourcePropertyID>0) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'], $SourcePropertyID);
				$PriceRaw = $arProp['VALUE'];
				if (is_array($PriceRaw) && !empty($PriceRaw)) {
					foreach($PriceRaw as $PriceRawItem) {
						$PriceRaw = $PriceRawItem;
						break;
					}
				}
				$PriceRaw = self::ParsePrice($PriceRaw);
			}
			// 2. process price
			$PriceBefore = $PriceRaw;
			$PriceAfter = self::ChangePrice($PriceBefore, $Params['change_value']);
			$Price = $PriceAfter;
			// 2.1 handle min price (compare with purchasing price)
			$MinPrice = false;
			if (!$SourcePurchasing && $Params['limit_below']=='Y' && strlen($Params['limit_below_value']) && CModule::IncludeModule('catalog')){
				$arProduct = CCatalogProduct::GetByID($ElementID);
				if (is_array($arProduct)) {
					$PurchasingPriceValue = $arProduct['PURCHASING_PRICE'];
					$PurchasingPriceCurrency = $arProduct['PURCHASING_CURRENCY'];
					if($SourceCurrency!==false && $SourceCurrency!=$PurchasingPriceCurrency && CModule::IncludeModule('currency')){
						$PurchasingPriceValue = $PurchasingPriceValue*CCurrencyRates::GetConvertFactor($PurchasingPriceCurrency,$SourceCurrency);
						$PurchasingPriceCurrency = $SourceCurrency;
					}
					$MinPrice = self::ChangePrice($PurchasingPriceValue, $Params['limit_below_value']);
				}
			}
			if($MinPrice!==false && $Price<$MinPrice) {
				$Price = $MinPrice;
			}
			// 2.2 round price
			if ($Params['round']=='Y') {
				$Price = CWDA::RoundEx($Price, IntVal($Params['round_value']));
			}
			// 3. save value
			if ($SourcePropertyID>0) {
				if ($Params['format']=='Y') {
					$Price = self::FormatPrice($Price, $Params['format_value']);
				}
				CIBlockElement::SetPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array($SourcePropertyID=>$Price));
				CWDA::Log('Set price for element #'.$ElementID.' from '.$PriceBefore.' to '.$PriceAfter.' (property #'.$SourcePropertyID.', change:'.$Params['change_value'].')', self::CODE);
				$bResult = true;
			} elseif ($SourcePriceID>0) {
				$Currency = 'RUB';
				if (strlen($SourceCurrency)) {
					$Currency = $SourceCurrency;
				}
				if (CWDA::SetProductPrice($ElementID, $SourcePriceID, $Price, $Currency)) {
					CWDA::Log('Set price for element #'.$ElementID.' from '.$PriceBefore.' to '.$PriceAfter.' (price #'.$SourcePriceID.', change:'.$Params['change_value'].')', self::CODE);
					$bResult = true;
				}
			} elseif ($SourcePurchasing) {
				if (CModule::IncludeModule('catalog')) {
					$Currency = 'RUB';
					if (strlen($SourceCurrency)) {
						$Currency = $SourceCurrency;
					}
					$arFields = array(
						'ID' => $ElementID,
						'PURCHASING_PRICE' => $Price,
						'PURCHASING_CURRENCY' => $Currency,
					);
					if (CCatalogProduct::Add($arFields)) {
						CWDA::Log('Set price ['.$Price.'] for element #'.$ElementID.' to price #'.$TargetPriceID, self::CODE);
						$bResult = true;
					} else {
						CWDA::Log('Error update '.$Target.' for element #'.$ElementID.', fields: '.print_r($arFields,1));
					}
				}
			}
		}
		// 4. process offers
		if ($Params['offers']=='Y' && CModule::IncludeModule('catalog')) {
			$OfferParams = $Params;
			$OfferParams['offers'] = 'N';
			$arCatalog = CCatalog::GetByID($arElement['IBLOCK_ID']);
			if ($arCatalog['OFFERS_IBLOCK_ID']>0 && $arCatalog['OFFERS_PROPERTY_ID']) {
				$resOffers = CIBlockElement::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>$arCatalog['OFFERS_IBLOCK_ID'],'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID']=>$ElementID),false,false,array('ID'));
				while ($arOffer = $resOffers->GetNext(false,false)) {
					$arOfferFields = CWDA::GetElementByID($arOffer['ID']);
					self::Process($arOffer['ID'], $arOfferFields, $OfferParams);
				}
			}
		}
		return $bResult;
	}
}
?>