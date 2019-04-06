<?
class CWDA_PriceCopy extends CWDA_Plugin {
	CONST GROUP = 'PRICES';
	CONST CODE = 'PRICE_COPY';
	CONST NAME = 'Копирование цен';
	//
	static function GetDescription() {
		$Descr = 'Плагин копирует цены из одного поля в другое. Поддерживаются: свойства инфоблока (типа «Строка» и «Число», с некоторыми производными), цены торгового каталога, а также закупочная цена. Операция копирования возможна в любых сочетаниях (напр., из свойства в оптовую цену, или из закупочной цены в розничную).';
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
			'CASE_LOWER' => 'нижний («для примера»)',
			'CASE_UPPER' => 'верхний («ДЛЯ ПРИМЕРА»)',
			'CASE_UCWORDS' => 'первые буквы («Для Примера»)',
			'ALERT_NO_PRICE_SOURCE' => 'Укажите цену в поле «откуда»',
			'ALERT_NO_PRICE_TARGET' => 'Укажите цену в поле «куда»',
			'PRICE_RUB_FULL' => 'руб',
			'PRICE_RUB_SHORT' => 'р',
			//
			'FIELD_SOURCE' => 'Откуда взять:',
			'FIELD_TARGET' => 'Куда записать:',
			//
			'SELECT_PRICE_SOURCE' => 'Укажите здесь свойство инфоблока или цену торгового каталога (в случае установленного модуля «Каталог»), которое следует взять для сохранение в другое свойство/цену. Показаны значения только типов «Число» и «Строка».',
			'SELECT_PRICE_TARGET' => 'Выберите свойство инфоблока или цену торгового каталога (в случае установленного модуля «Каталог»), куда следует записывать значения из выпадающего списка в предыдущем параметре. Показаны значения только типов «Число» и «Строка».',
			'ADDITIONAL_SETTINGS' => 'Дополнительные опции',
			'PARAM_NOT_EMPTY' => 'Не устанавливать нулевые цены:',
			'PARAM_ROUND' => 'Округлить до:',
			'PARAM_FORMAT' => 'Формат:',
			'PARAM_CURRENCY' => 'Валюта:',
			//
			'HINT_PARAM_NOT_EMPTY' => 'Параметр отменяет сохранение нулевых цен. Т.е. будут загружены все цены, больше нуля.',
			'HINT_PARAM_ROUND' => 'Параметр позволяет округлять загружаемые цены до тысяч (12345.67 => 12000), сотен (12345.67 => 12300), десятков (12345.67 => 12350) и целых (12345.67 => 12346)',
			'HINT_PARAM_FORMAT' => 'Параметр позволяет изменить формат сохранения валюты. Применяется только для сохранения цены в свойство инфоблока; при сохранении цены в торговый каталог сохраняется только число, цена форматируется на основании настроек валют в модуле «Валюты».',
			'HINT_PARAM_CURRENCY' => 'Параметр позволяет задать валюту цены. Будет полезно, для случая, если копируется цена из свойства инфоблока в цену торгового каталога, т.к. в свойстве инфоблока не указана валюта напрямую. Также используется в обратном процессе: при копировании цены каталога в свойство, идет пересчет на данную валюту.',
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
				} else if ($.trim($('#wda_field_target').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_PRICE_TARGET',true);?>');
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
			if (!CWDA::IsUtf()) {
				$Format = CWDA::ConvertCharset($Format,'UTF-8','CP1251');
			}
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
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
			<div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_PRICE_TARGET'));?></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_ADDITIONAL_SETTINGS');?></div>
			<div class="wda_additional_params" id="wda_additional_params_<?=self::CODE?>">
				<table>
					<tbody>
						<tr>
							<td class="check">
								<input type="checkbox" name="params[not_empty]" value="Y" id="wda_checkbox_not_empty" />
							</td>
							<td class="label" colspan="2">
								<label for="wda_checkbox_not_empty"><?=self::GetMessage('PARAM_NOT_EMPTY');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_NOT_EMPTY'));?>
							</td>
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
									<?for($i=3;$i>=0;$i--):?><option value="<?=$i;?>"<?if($i==0):?> selected="selected"<?endif?>><?=self::GetMessage('ROUND_'.$i);?></option><?endfor?>
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
								<input type="checkbox" name="params[currency]" value="Y" id="wda_checkbox_currency" />
							</td>
							<td class="label">
								<label for="wda_checkbox_currency"><?=self::GetMessage('PARAM_CURRENCY');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_PARAM_CURRENCY'));?>
							</td>
							<td class="value">
								<?$arCurrencies = CWDA::GetCurrencyList();?>
								<select name="params[currency_value]">
									<?foreach($arCurrencies as $arCurrency):?>
										<option value="<?=$arCurrency['CURRENCY'];?>"><?=$arCurrency['FULL_NAME'];?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
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
		// Target
		$TargetPriceID = false;
		if(preg_match('#^CATALOG_PRICE_(\d+)$#i',$Params['field_target'],$M)) {
			$TargetPriceID = IntVal($M[1]);
		}
		$TargetPropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_target'],$M)) {
			$TargetPropertyID = IntVal($M[1]);
		}
		$TargetPurchasing = false;
		if($Params['field_target']=='CATALOG_PURCHASING_PRICE') {
			$TargetPurchasing = true;
		}
		// Process...
		$SourceCurrency = false;
		if (($TargetPriceID>0 || $TargetPropertyID>0 || $TargetPurchasing) && ($SourcePriceID>0 || $SourcePropertyID>0 || $SourcePurchasing)) {
			$PriceRaw = 0;
			if ($SourcePriceID>0) {
				$PriceRaw = FloatVal($arElement['CATALOG_PRICE_'.$SourcePriceID]);
				$SourceCurrency = $arElement['CATALOG_CURRENCY_'.$SourcePriceID];
			} elseif ($SourcePropertyID) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'], $SourcePropertyID);
				$PriceRaw = $arProp['VALUE'];
				if (is_array($PriceRaw) && !empty($PriceRaw)) {
					foreach($PriceRaw as $PriceRawItem) {
						$PriceRaw = $PriceRawItem;
						break;
					}
				}
				$PriceRaw = FloatVal($PriceRaw);
			} elseif ($SourcePurchasing) {
				if (CModule::IncludeModule('catalog')) {
					$arProduct = CCatalogProduct::GetByID($ElementID);
					if (is_array($arProduct)) {
						$PriceRaw = $arProduct['PURCHASING_PRICE'];
						$SourceCurrency = $arProduct['PURCHASING_CURRENCY'];
					}
				}
			}
			if ($Params['not_empty']=='Y' && $PriceRaw==0) {
				CWDA::Log('Skip empty price for element #'.$ElementID, self::CODE);
				return true;
			}
			$Price = $PriceRaw;
			if ($Params['round']=='Y' && $TargetPropertyID===false) {
				$Price = CWDA::RoundEx($Price, IntVal($Params['round_value']));
			}
			if ($TargetPropertyID>0) {
				if ($Params['currency']=='Y' && strlen($SourceCurrency) && CModule::IncludeModule('currency')) {
					$Factor = CCurrencyRates::GetConvertFactor($SourceCurrency, $Params['currency_value']);
					if ($Factor>0) {
						$Price = $Price * $Factor;
					}
				}
				if ($Params['round']=='Y') {
					$Price = CWDA::RoundEx($Price, IntVal($Params['round_value']));
				}
				if ($Params['format']=='Y') {
					$Price = self::FormatPrice($Price, $Params['format_value']);
				}
				CIBlockElement::SetPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array($TargetPropertyID=>$Price));
				CWDA::Log('Set price ['.$Price.'] for element #'.$ElementID.' to property #'.$TargetPropertyID, self::CODE);
				$bResult = true;
			} elseif ($TargetPriceID>0) {
				$Currency = 'RUB';
				if (strlen($SourceCurrency)) {
					$Currency = $SourceCurrency;
				} elseif ($Params['currency']=='Y' && CModule::IncludeModule('currency')) {
					$Currency = $Params['currency_value'];
					$arCurrencies = CWDA::GetCurrencyList();
					if (isset($arCurrencies[$Params['currency_value']])) {
						$Currency = $Params['currency_value'];
					}
				}
				if (CWDA::SetProductPrice($ElementID, $TargetPriceID, $Price, $Currency)) {
					$bResult = true;
				}
				CWDA::Log('Set price ['.$Price.'] for element #'.$ElementID.' to price #'.$TargetPriceID, self::CODE);
			} elseif ($TargetPurchasing) {
				if (CModule::IncludeModule('catalog')) {
					$Currency = 'RUB';
					if (strlen($SourceCurrency)) {
						$Currency = $SourceCurrency;
					} elseif ($Params['currency']=='Y' && CModule::IncludeModule('currency')) {
						$Currency = $Params['currency_value'];
						$arCurrencies = CWDA::GetCurrencyList();
						if (isset($arCurrencies[$Params['currency_value']])) {
							$Currency = $Params['currency_value'];
						}
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
		return $bResult;
	}
}
?>