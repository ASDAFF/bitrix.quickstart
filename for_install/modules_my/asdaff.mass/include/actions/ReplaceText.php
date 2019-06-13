<?
class CWDA_ReplaceText extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'REPLACE_TEXT';
	CONST NAME = 'Замена в тексте';
	//
	static function GetDescription() {
		$Descr = 'Плагин выполняет замены в значениях полей и свойств. Плагин поддерживает замену по регулярным выражениям.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'ALERT_NO_FIELD_TARGET' => 'Укажите поле или свойство для выполнения замены',
			//
			'FIELD_TARGET' => 'Поле или свойство',
			'SEARCH_FROM' => 'Что ищем:',
			'SEARCH_TO' => 'На что заменяем:',
			'USE_REGEXP' => 'Использовать регулярные выражения',
				'USE_REGEXP_HINT' => 'Регулярные выражения это мощнейшее средство поиска и обработки текстовых строк, в данном случае они помогают искать и заменять текст для любых задач. Например, если в каждом товаре указан артикул вида "ARTICUL_123456", то его можно автоматически заменять на "art_123456_new".<br/><br/>В случае использования регулярных выражений строка "Что ищем" должна содержать полный шаблон для поиска, например:<br/><code><b>#value_(\d+)#i</b></code><br/>или так:<br/><code><b>/value_(\d+)/</b></code><br/><br/>Подробную информацию можно найти на <a href="http://php.net/manual/ru/reference.pcre.pattern.syntax.php" target="_blank">сайте PHP</a>.',
			'CASE_SENSITIVE' => 'Учитывать регистр',
				'CASE_SENSITIVE_HINT' => 'В случае учета регистра, символы в верхнем регистре не равны символам в нижне регистре. Например, Если в тексте содержится "АБВ", а поиск идет для "абв", то замены не будет.<br/><br/><b>Внимание!</b> На некоторых серверах (там где не работает нормально php-функция <code><a href="http://forum.ru-board.com/topic.cgi?forum=31&topic=13373" target="_blank">str_ireplace</a></code>) данная функция не работает.',
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
				 if ($.trim($('#wda_field_target').val())=='') {
					alert('<?=self::GetMessage('ALERT_NO_FIELD_TARGET',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var IBlock = $('#wda_select_iblock').val();
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html()); // Property
			SelectTarget.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectTarget.find('optgroup[data-group=FIELDS] option').not('[value=NAME],[value=CODE],[value=PREVIEW_TEXT],[value=DETAIL_TEXT],[value=EXTERNAL_ID],[value=XML_ID],[value=TAGS]').remove();
			SelectTarget.find('optgroup[data-group=PROPERTIES] option').not('[data-type=S],[data-type="S:HTML"]').remove();
			SelectTarget.change();
		}
		$('#wda_checkbox_use_regexp input[type=checkbox]').live('change',function(){
			if($(this).is(':checked')) {
				$('#wda_case_sensitive').hide();
			} else {
				$('#wda_case_sensitive').show();
			}
		});
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
			<div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_PRICE_TARGET'));?></div>
			</div>
			<div class="wda_additional_settings" id="wda_additional_settings_<?=self::CODE?>"></div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('SEARCH_FROM');?></div>
			<div>
				<div><input type="text" name="params[search_from]" value="" size="50" /></div>
				<br/>
			</div>
			<div>
				<div class="wda_settings_header"><?=self::GetMessage('SEARCH_TO');?></div>
				<div><input type="text" name="params[search_to]" value="" size="50" /></div>
				<br/>
			</div>
			<div id="wda_checkbox_use_regexp">
				<div><label><input type="hidden" name="params[use_regexp]" value="N" /><input type="checkbox" name="params[use_regexp]" value="Y" /> <?=self::GetMessage('USE_REGEXP');?></label> <?=CWDA::ShowHint(self::GetMessage('USE_REGEXP_HINT'));?></div>
				<br/>
			</div>
			<div id="wda_case_sensitive">
				<div><label><input type="hidden" name="params[case_sensitive]" value="N" /><input type="checkbox" name="params[case_sensitive]" value="Y" /> <?=self::GetMessage('CASE_SENSITIVE');?></label> <?=CWDA::ShowHint(self::GetMessage('CASE_SENSITIVE_HINT'));?></div>
				<br/>
			</div>
		</div>
		<?
	}
	static function Replace($From, $To, $Value, $bUseRegExp, $bCaseSensitive, $ElementID){
		$bValueIsArray = is_array($Value) && count($Value)==2 && in_array(ToUpper($Value['TYPE']),array('TEXT','HTML')) && isset($Value['TEXT']);
		if($bUseRegExp) {
			if($bValueIsArray) {
				$Value['TEXT'] = preg_replace($From, $To, $Value['TEXT']);
			} else {
				$Value = preg_replace($From, $To, $Value);
			}
		} else {
			if($bCaseSensitive) {
				if($bValueIsArray) {
					$Value['TEXT'] = str_replace($From, $To, $Value['TEXT']);
				} else {
					$Value = str_replace($From, $To, $Value);
				}
			} else {
				if($bValueIsArray) {
					$Value['TEXT'] = str_ireplace($From, $To, $Value['TEXT']);
				} else {
					$Value = str_ireplace($From, $To, $Value);
				}
			}
		}
		return $Value;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$IBlockElement = new CIBlockElement;
		$Target = $Params['field_target'];
		$From = $Params['search_from'];
		$To = $Params['search_to'];
		if(!CWDA::IsUtf()) {
			$From = CWDA::ConvertCharset($From);
			$To = CWDA::ConvertCharset($To);
		}
		$arSpec1 = array('\r','\n','\t');
		$arSpec2 = array("\r","\n","\t");
		$From = str_replace($arSpec1,$arSpec2,$From);
		$To = str_replace($arSpec1,$arSpec2,$To);
		$bUseRegExp = $Params['use_regexp']=='Y';
		$bCaseSensitive = $Params['case_sensitive']=='Y';
		if(in_array($Target,array('NAME','CODE','EXTERNAL_ID','XML_ID','PREVIEW_TEXT','DETAIL_TEXT','TAGS'))) {
			$Value = $arElement['~'.$Target];
			$Value = self::Replace($From, $To, $Value, $bUseRegExp, $bCaseSensitive, $ElementID);
			if ($IBlockElement->Update($ElementID,array($Target=>$Value))) {
				$bResult = true;
			}
		} elseif (preg_match('#^PROPERTY_(\d+)$#',$Target,$M)) {
			$PropertyID = $M[1];
			foreach($arElement['PROPERTIES'] as $arProperty){
				if($arProperty['ID']==$PropertyID) {
					$Value = $arProperty['~VALUE'];
					if($arProperty['MULTIPLE']=='Y' && is_array($Value)) {
						foreach($Value as $Key => $ValueItem) {
							$Value[$Key] = self::Replace($From, $To, $ValueItem, $bUseRegExp, $bCaseSensitive, $ElementID);
						}
					} else {
						$Value = self::Replace($From, $To, $Value, $bUseRegExp, $bCaseSensitive, $ElementID);
					}
					break;
				}
			}
			$IBlockElement->SetPropertyValuesEx($ElementID,$arElement['IBLOCK_ID'],array($PropertyID=>$Value));
			$bResult = true;
		}
		return $bResult;
	}
}

?>