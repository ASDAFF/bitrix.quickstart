<?
class CWDA_ImageCopy extends CWDA_Plugin {
	CONST GROUP = 'IMAGES';
	CONST CODE = 'IMAGE_COPY';
	CONST NAME = 'Копирование изображений';
	//
	static function GetDescription() {
		$Descr = 'Плагин для копирования изображений в инфоблоках из одного поля/свойства в другое. Поддерживаются поля «Картинка для анонса» и «Детальная картинка», а также свойства инфоблока типа «Файл». Возможны любые сочетания.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'PROP_GROUP_1' => 'Откуда',
			'PROP_GROUP_2' => 'Куда',
			//
			'ALERT_NO_SOURCE' => 'Укажите, откуда скопировать изображения',
			'ALERT_NO_TARGET' => 'Укажите, куда скопировать изображения',
			'ALERT_TARGET_EQUAL_SOURCE' => 'Поля «Откуда» и «Куда» не могут совпадать',
			//
			'SELECT_SOURCE' => 'Выберите поле/свойство с изображением.',
			'SELECT_TARGET' => 'Выберите поле/свойство, куда будет сохраняться изображение.',
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
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if ($('#wda_field_source').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_SOURCE',true);?>');
				} else if ($('#wda_field_target').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_TARGET',true);?>');
				} else if ($('#wda_field_source').val()==$('#wda_field_target').val()) {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_TARGET_EQUAL_SOURCE',true);?>');
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			// Source
			var SelectSource = $('#wda_field_source').html(Select.html());
			SelectSource.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectSource.find('optgroup option').not('[data-type=F]').remove();
			SelectSource.change();
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html());
			SelectTarget.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectTarget.find('optgroup option').not('[data-type=F]').remove();
			SelectTarget.change();
		}
		//
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_1');?></div>
			<div>
				<div><select name="params[field_source]" id="wda_field_source" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_SOURCE'));?></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_2');?></div>
			<div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_TARGET'));?></div>
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		// Source
		$SourceField = false;
		if(in_array($Params['field_source'],array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
			$SourceField = $Params['field_source'];
		}
		$SourcePropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_source'],$M)) {
			$SourcePropertyID = IntVal($M[1]);
		}
		// Target
		$TargetField = false;
		if(in_array($Params['field_target'],array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
			$TargetField = $Params['field_target'];
		}
		$TargetPropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_target'],$M)) {
			$TargetPropertyID = IntVal($M[1]);
		}
		// Process
		if ((strlen($SourceField) || $SourcePropertyID>0) && (strlen($TargetField) || $TargetPropertyID>0)) {
			$Value = false;
			if (strlen($SourceField)) {
				$Value = $arElement[$SourceField];
			} elseif ($SourcePropertyID>0) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$SourcePropertyID);
				$Value = $arProp['VALUE'];
			}
			if (!empty($Value)) {
				if (strlen($TargetField)) {
					if (is_array($Value)) {
						$Value = $Value[0];
					}
					if ($Value>0) {
						$Value = CFile::MakeFileArray($Value);
						$IBlockElement = new CIBlockElement;
						if ($IBlockElement->Update($arElement['ID'],array($TargetField=>$Value))) {
							CWDA::Log('Copy image for element #'.$arElement['ID'].' (from '.$Params['field_source'].' to '.$Params['field_target'].')', self::CODE);
							$bResult = true;
						} else {
							CWDA::Log('Error while copy image for element #'.$arElement['ID'].': '.$IBlockElement->LAST_ERROR, self::CODE);
						}
					}
				} elseif ($TargetPropertyID>0) {
					$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$TargetPropertyID);
					if ($arProp['MULTIPLE']!='Y' && is_array($Value)) {
						$Value = $Value[0];
						$Value = CFile::MakeFileArray($Value);
					} elseif ($arProp['MULTIPLE']=='Y' && is_array($Value)) {
						foreach($Value as $Key => $FileID){
							$Value[$Key] = CFile::MakeFileArray($FileID);
						}
					} elseif ($Value>0) {
						$Value = CFile::MakeFileArray($Value);
					}
					CIBlockElement::SetPropertyValuesEx($arElement['ID'],$arElement['IBLOCK_ID'],array($TargetPropertyID=>$Value));
					CWDA::Log('Copy image for element #'.$arElement['ID'].' (from '.$Params['field_source'].' to '.$Params['field_target'].')', self::CODE);
					$bResult = true;
				}
			}
		}
		return $bResult;
	}
}
?>