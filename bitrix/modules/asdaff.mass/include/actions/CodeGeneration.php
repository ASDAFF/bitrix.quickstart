<?
class CWDA_CodeGeneration extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'CODE_GENERATION';
	CONST NAME = 'Генерация символьного кода';
	//
	static function GetDescription() {
		$Descr = 'Плагин выполняет генерацию символьного кода из другого поля или свойства. Плагин имеет большое количество настраиваемых параметров для обеспечения наиболее приемлемого результата.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'PROP_GROUP_1' => 'Поле / свойство для сохранения в символьный код:',
			'PROP_GROUP_ADDITIONAL_SETTINGS' => 'Дополнительные опции',
			//
			'PREG_A_YA_PATTERN' => 'А-я',
			//
			'PROP_CASE' => 'Изменить регистр:',
				'CASE_NONE' => '--- не изменять ---',
				'CASE_LOWER' => 'нижний («для примера»)',
				'CASE_UPPER' => 'верхний («ДЛЯ ПРИМЕРА»)',
			'PROP_LEN' => 'Ограничить длину:',
			'PROP_REPLACE_SPACE' => 'Заменить пробелы:',
			'PROP_REPLACE_OTHER' => 'Заменить спецсимволы:',
			'PROP_IF_EXISTS' => 'При совпадении:',
				'IF_EXISTS_ADD_SUFFIX' => 'добавить порядковый индекс',
				'IF_EXISTS_ADD_ELEMENT_ID' => 'добавить ID элемента',
				'IF_EXISTS_ADD_SECTION_ID' => 'добавить ID раздела',
				'IF_EXISTS_ADD_SECTION_ID_AND_ELEMENT_ID' => 'добавить ID раздела и ID элемента',
				'IF_EXISTS_DO_NOTHING' => 'пропускать',
			'PROP_TRANSLIT' => 'Транслит',
			'PROP_TRIM' => 'Тримминг',
			'PROP_UNIQUE' => 'Проверка на уникальность',
			'PROP_REMOVEEXTRA' => 'Удалять лишние символы',
			'PROP_TEXT_BEFORE' => 'Текст в начале',
			'PROP_TEXT_AFTER' => 'Текст в конце',
			//
			'SOURCE_EMPTY' => '--- инфоблок не выбран ---',
			//
			'HINT_TRANSLIT' => 'Опция задействует функционал траслитерации (перевод на латиницу). Например, «Товар» будет заменено на «Tovar».',
			'HINT_TRIM' => 'Опция очищает строку от лишних пробелов в начале и конце значения, например, " Товар " будет заменено на "Товар".',
			'HINT_UNIQUE' => 'Опция позволяет проверять существование сохраняемого символьго кода и автоматически его изменять в случае, если элемент с таким символьным кодом уже существует.',
			'HINT_REMOVEEXTRA' => 'Опция очищает лишние символы замены, например, если после преобразований строки «Лето - это хорошо» получается «Leto___eto_horosho», то включенная опция меняет это на «Leto_eto_horosho».',
			'HINT_CASE' => 'Опция преобразования текста в верхний или нижний регистр.',
			'HINT_LENGTH' => 'Опция контролирует максимальную длину текста, и в случае превышения лимита - обрезает результат по длине.',
			'HINT_SPACE' => 'Символ, на который заменяются пробелы в символьном коде.',
			'HINT_SPECSYMBOLS' => 'Символ, на который заменяются специальные и другие символы в символьном коде.',
			'HINT_IF_EXISTS' => 'Опция позволяет выбрать алгоритм изменения символьного кода в случае, если сохраняемый код уже существует.',
			'HINT_TEXT_BEFORE' => 'Текст, который будет добавляться к символьному коду в начале.',
			'HINT_TEXT_AFTER' => 'Текст, который будет добавляться к символьному коду в конце.',
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
		BX.addCustomEvent('onWdaAfterIBlockChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var SelectSource = $('#wda_field_source').html(Select.html());
			var IBlockID = $('#wda_select_iblock').val();
			if (IBlockID>0) {
				SelectSource.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
				SelectSource.find('option').not('[data-type^=N]').not('[data-type=S]').remove();
				SelectSource.find('option').filter('[value=CODE],[value=SHOW_COUNTER]').remove();
				SelectSource.val('NAME').change();
				var CodeProps = window['IBlock_'+IBlockID]['CODE']['DEFAULT_VALUE'];
				WDA_SetCheckboxValue('#wda_checkbox_translit',CodeProps.TRANSLITERATION);
				WDA_SetCheckboxValue('#wda_checkbox_unique',CodeProps.UNIQUE);
				WDA_SetCheckboxValue('#wda_checkbox_removeextra',CodeProps.TRANS_EAT);
				$('#wda_select_case').val(CodeProps.TRANS_CASE);
				$('#wda_input_length').val(CodeProps.TRANS_LEN);
				$('#wda_input_space').val(CodeProps.TRANS_SPACE);
				$('#wda_input_specsymbols').val(CodeProps.TRANS_OTHER);
			}
		}
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_1');?></div>
			<div><select name="params[field_source]" id="wda_field_source" class="wda_select_field"></select></div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_ADDITIONAL_SETTINGS');?></div>
			<div class="wda_additional_params" id="wda_additional_params_<?=self::CODE?>">
			<table>
				<tbody>
					<tr>
						<td class="check">
							<input type="checkbox" name="params[translit]" value="Y" id="wda_checkbox_translit" checked="checked" />
						</td>
						<td class="label" colspan="2">
							<label for="wda_checkbox_translit"><?=self::GetMessage('PROP_TRANSLIT');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_TRANSLIT'));?>
						</td>
					</tr>
					<tr>
						<td class="check">
							<input type="checkbox" name="params[trim]" value="Y" id="wda_checkbox_trim" checked="checked" />
						</td>
						<td class="label" colspan="2">
							<label for="wda_checkbox_trim"><?=self::GetMessage('PROP_TRIM');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_TRIM'));?>
						</td>
					</tr>
					<tr>
						<td class="check">
							<input type="checkbox" name="params[unique]" value="Y" id="wda_checkbox_unique" checked="checked" />
						</td>
						<td class="label" colspan="2">
							<label for="wda_checkbox_unique"><?=self::GetMessage('PROP_UNIQUE');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_UNIQUE'));?>
						</td>
					</tr>
					<tr>
						<td class="check">
							<input type="checkbox" name="params[removeextra]" value="Y" id="wda_checkbox_removeextra" checked="checked" />
						</td>
						<td class="label" colspan="2">
							<label for="wda_checkbox_removeextra"><?=self::GetMessage('PROP_REMOVEEXTRA');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_REMOVEEXTRA'));?>
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_case"><?=self::GetMessage('PROP_CASE');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_CASE'));?>
						</td>
						<td class="input">
							<select name="params[case_value]" id="wda_select_case">
								<option value="none"><?=self::GetMessage('CASE_NONE');?></option>
								<option value="lower"><?=self::GetMessage('CASE_LOWER');?></option>
								<option value="upper"><?=self::GetMessage('CASE_UPPER');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_length"><?=self::GetMessage('PROP_LEN');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_LENGTH'));?>
						</td>
						<td class="input">
							<input type="text" name="params[length_value]" value="100" size="5" id="wda_input_length" />
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_space"><?=self::GetMessage('PROP_REPLACE_SPACE');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_SPACE'));?>
						</td>
						<td class="input">
							<input type="text" name="params[space_value]" value="-" size="5" style="font-family:monospace;" id="wda_input_space" />
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_specsymbols"><?=self::GetMessage('PROP_REPLACE_OTHER');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_SPECSYMBOLS'));?>
						</td>
						<td class="input">
							<input type="text" name="params[specsymbols_value]" value="_" size="5" style="font-family:monospace;" id="wda_input_specsymbols" />
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_if_exists"><?=self::GetMessage('PROP_IF_EXISTS');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_IF_EXISTS'));?>
						</td>
						<td class="input">
							<select name="params[if_exists]" id="wda_select_if_exists">
								<option value="add_suffix"><?=self::GetMessage('IF_EXISTS_ADD_SUFFIX');?></option>
								<option value="add_element_id"><?=self::GetMessage('IF_EXISTS_ADD_ELEMENT_ID');?></option>
								<option value="add_section_id"><?=self::GetMessage('IF_EXISTS_ADD_SECTION_ID');?></option>
								<option value="add_section_id_and_element_id"><?=self::GetMessage('IF_EXISTS_ADD_SECTION_ID_AND_ELEMENT_ID');?></option>
								<option value="do_nothing"><?=self::GetMessage('IF_EXISTS_DO_NOTHING');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_text_before"><?=self::GetMessage('PROP_TEXT_BEFORE');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_TEXT_BEFORE'));?>
						</td>
						<td class="input">
							<input type="text" name="params[text_before]" value="" size="50" id="wda_input_text_before" />
						</td>
					</tr>
					<tr>
						<td class="check"></td>
						<td class="label">
							<label for="wda_checkbox_text_after"><?=self::GetMessage('PROP_TEXT_AFTER');?></label>
							<?=CWDA::ShowHint(self::GetMessage('HINT_TEXT_AFTER'));?>
						</td>
						<td class="input">
							<input type="text" name="params[text_after]" value="" size="50" id="wda_input_text_after" />
						</td>
					</tr>
				</tbody>
			</table>
			</div>
		</div>
		<?
	}
	function CodeExists($Code, $IBlockID, $ElementID){
		return CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$IBlockID,'CODE'=>$Code,'!ID'=>$ElementID),array())>0;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		// Source
		$SourceField = false;
		$SourcePropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_source'],$M)) {
			$SourcePropertyID = IntVal($M[1]);
		} elseif (in_array($Params['field_source'],array('ID','NAME','SORT','EXTERNAL_ID'))) {
			$SourceField = $Params['field_source'];
		}
		// Process...
		if (strlen($SourceField) || $SourcePropertyID>0) {
			$Value = '';
			if (strlen($SourceField)) {
				$Value = $arElement[$SourceField];
			} elseif ($SourcePropertyID>0) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'], $SourcePropertyID);
				$Value = $arProp['VALUE'];
				if (is_array($Value) && !empty($Value)) {
					foreach($Value as $ValueItem) {
						$Value = $ValueItem;
						break;
					}
				}
			}
			if ($Params['trim']=='Y') {
				$Value = trim($Value);
			}
			$Value = str_replace('&quot;','',$Value);
			$Case = 'false';
			switch(ToUpper($Params['case_value'])) {
				case 'LOWER':
					$Case = 'L';
					break;
				case 'UPPER':
					$Case = 'U';
					break;
			}
			$TranslitParams = array(
				'UNIQUE' => $Params['unique']=='Y'?'Y':'N',
				'TRANS_LEN' => $Params['length_value']>0?$Params['length_value']:'255',
				'TRANS_CASE' => strlen($Case)?$Case:'false',
				'TRANS_SPACE' => strlen($Params['space_value'])?$Params['space_value']:'_',
				'TRANS_OTHER' => strlen($Params['specsymbols_value'])?$Params['specsymbols_value']:'_',
				'TRANS_EAT' => $Params['removeextra']=='Y'?'Y':'N',
				'USE_GOOGLE' => $Params['use_google']=='Y'?'Y':'N',
			);
			$Code = '';
			$CodeIndex = 0;
			$Suffix = '';
			do {
				if ($TranslitParams['UNIQUE']=='Y'){
					if ($CodeIndex>0) {
						if ($Params['if_exists']=='add_suffix') {
							$Suffix = $TranslitParams['TRANS_SPACE'].$CodeIndex;
						} elseif ($Params['if_exists']=='add_section_id') {
							$Suffix = $TranslitParams['TRANS_SPACE'].$arElement['IBLOCK_SECTION_ID'];
						} elseif ($Params['if_exists']=='element_id') {
							$Suffix = $TranslitParams['TRANS_SPACE'].$arElement['ID'];
						} elseif ($Params['if_exists']=='add_section_id_and_element_id') {
							$Suffix = $TranslitParams['TRANS_SPACE'].$arElement['IBLOCK_SECTION_ID'].$TranslitParams['TRANS_SPACE'].$arElement['ID'];
						}
					}
					if ($CodeIndex<2) {
						$CodeIndex = 2;
					} else {
						$CodeIndex++;
					}
				}
				if ($Params['translit']=='Y') {
					$Code = CUtil::translit($Value, LANGUAGE_ID, array(
						'max_len' => IntVal($TranslitParams['TRANS_LEN']) - strlen($Suffix),
						'change_case' => $TranslitParams['TRANS_CASE'],
						'replace_space' => $TranslitParams['TRANS_SPACE'],
						'replace_other' => $TranslitParams['TRANS_OTHER'],
						'delete_repeat_replace' => $TranslitParams['TRANS_EAT']=='Y'?'true':'false',
						'use_google' => $TranslitParams['USE_GOOGLE']=='Y'?'true':'false',
					)).$Suffix;
				} else {
					$Code = $Value;
					if ($TranslitParams['TRANS_CASE']=='L') {
						$Code = ToLower($Code);
					} elseif ($TranslitParams['TRANS_CASE']=='U') {
						$Code = ToUpper($Code);
					}
					while (strpos($Code,' ')!==false) {
						$Code = str_replace(' ',$TranslitParams['TRANS_SPACE'],$Code);
					}
					$CodeTmp = '';
					for($i=0; $i<StrLen($Code); $i++) {
						$Letter = substr($Code,$i,1);
						$A_Ya = self::GetMessage('PREG_A_YA_PATTERN');
						if (!CWDA::IsUtf()) {
							$A_Ya = CWDA::ConvertCharset($A_Ya);
						}
						if (!preg_match('#[A-z0-9'.$A_Ya.'-_]#i'.BX_UTF_PCRE_MODIFIER,$Letter)) {
							$Letter = $TranslitParams['TRANS_OTHER'];
						}
						$CodeTmp .= $Letter;
					}
					$Code = $CodeTmp;
					$T_S = $TranslitParams['TRANS_SPACE'];
					$T_O = $TranslitParams['TRANS_OTHER'];
					if ($Params['removeextra']=='Y') {
						while (strpos($Code,$T_S.$T_S)!==false || strpos($Code,$T_O.$T_O)!==false) {
							$Code = str_replace($T_S.$T_S, $T_S, $Code);
							$Code = str_replace($T_O.$T_O, $T_O, $Code);
						}
					}
					$Code = substr($Code, 0, IntVal($TranslitParams['TRANS_LEN'])-strlen($Suffix)).$Suffix;
				}
				$Code = $Params['text_before'].$Code.$Params['text_after'];
			} while ($TranslitParams['UNIQUE']=='Y' && self::CodeExists($Code, $IBlockID, $ElementID));
			$IBlockElement = new CIBlockElement;
			if ($IBlockElement->Update($ElementID,array('CODE'=>$Code))) {
				CWDA::Log('Set code ['.$Code.'] for element #'.$ElementID, self::CODE);
				$bResult = true;
			} else {
				CWDA::Log('Error set code ['.$Code.'] for element #'.$ElementID.': '.$IBlockElement->LAST_ERROR, self::CODE);
			}
		}
		return $bResult;
	}
}
?>