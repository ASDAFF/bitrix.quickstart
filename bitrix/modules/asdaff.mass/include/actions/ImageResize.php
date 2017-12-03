<?
class CWDA_ImageResize extends CWDA_Plugin {
	CONST GROUP = 'IMAGES';
	CONST CODE = 'IMAGE_RESIZE';
	CONST NAME = 'Ресайз изображений';
	//
	static function GetDescription() {
		$Descr = 'Плагин для уменьшения больших картинок. Поддерживаются поля «Картинка для анонса» и «Детальная картинка», а также свойства инфоблока типа «Файл».';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'PROP_GROUP_1' => 'Поля / свойства с изображениями:',
			'PROP_GROUP_ADDITIONAL_SETTINGS' => 'Дополнительные опции',
			//
			'BX_RESIZE_IMAGE_PROPORTIONAL' => 'Вписать в указанный размер',
			'BX_RESIZE_IMAGE_EXACT' => 'Заполнить, обрезать лишнее',
			'BX_RESIZE_IMAGE_PROPORTIONAL_ALT' => 'Вписать в указанный размер (верт.)',
			//
			'PROP_METHOD' => 'Метод:',
			'PROP_WIDTH' => 'Ширина:',
			'PROP_HEIGHT' => 'Высота:',
			'PROP_SHARPEN' => 'Резкость:',
			'PROP_SHARPEN_Y' => 'да',
			//
			'ALERT_NO_WIDTH' => 'Укажите максимальную ширину изображения',
			'ALERT_NO_HEIGHT' => 'Укажите максимальную высоту изображения',
			//
			'HINT_METHOD' => 'Режим изменения размеров',
			'HINT_WIDTH' => 'Максимальная ширина изображения',
			'HINT_HEIGHT' => 'Максимальная высота изображения',
			'HINT_SHARPEN' => 'Применение фильтра резкости (Sharpen)',
			//
			'SOURCE_EMPTY' => '--- инфоблок не выбран ---',
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
				if ($('#wda_input_width').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_WIDTH',true);?>');
				} else if ($('#wda_input_height').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_HEIGHT',true);?>');
				}
			}
		});
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var SelectSource = $('#wda_field_source').html(Select.html()).val('');
			var IBlockID = $('#wda_select_iblock').val();
			if (IBlockID>0) {
				SelectSource.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
				SelectSource.find('option').not('[data-type=F]').remove();
				SelectSource.val('DETAIL_PICTURE').change();
			} 
			if (SelectSource.val()==null) {
				SelectSource.val('');
			}
		}
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_1');?></div>
			<div><select name="params[field_source][]" id="wda_field_source" multiple="multiple" size="6" class="wda_select_field"></select></div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_ADDITIONAL_SETTINGS');?></div>
			<div class="wda_additional_params" id="wda_additional_params_<?=self::CODE?>">
				<table>
					<tbody>
						<tr>
							<td class="label">
								<label for="wda_checkbox_method"><?=self::GetMessage('PROP_METHOD');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_METHOD'));?>
							</td>
							<td class="value">
								<select name="params[method]" id="wda_select_method">
									<option value="BX_RESIZE_IMAGE_PROPORTIONAL"><?=self::GetMessage('BX_RESIZE_IMAGE_PROPORTIONAL');?></option>
									<option value="BX_RESIZE_IMAGE_EXACT"><?=self::GetMessage('BX_RESIZE_IMAGE_EXACT');?></option>
									<option value="BX_RESIZE_IMAGE_PROPORTIONAL_ALT"><?=self::GetMessage('BX_RESIZE_IMAGE_PROPORTIONAL_ALT');?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="wda_input_width"><?=self::GetMessage('PROP_WIDTH');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_WIDTH'));?>
							</td>
							<td class="value">
								<input type="text" name="params[width_value]" value="1000" size="5" id="wda_input_width" data-int="Y" maxlength="5" /> <span>px</span>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="wda_input_height"><?=self::GetMessage('PROP_HEIGHT');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_HEIGHT'));?>
							</td>
							<td class="value">
								<input type="text" name="params[height_value]" value="1000" size="5" id="wda_input_height" data-int="Y" maxlength="5" /> <span>px</span>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="wda_checkbox_sharpen"><?=self::GetMessage('PROP_SHARPEN');?></label>
								<?=CWDA::ShowHint(self::GetMessage('HINT_SHARPEN'));?>
							</td>
							<td class="value">
								<input type="checkbox" name="params[sharpen]" value="Y" id="wda_checkbox_sharpen" checked="checked" />
								<label for="wda_checkbox_sharpen"><?=self::GetMessage('PROP_SHARPEN_Y');?></label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?
	}
	static function NeedResize($Value, $Width, $Height) {
		if ($ImageID>0) {
			$arImage = CFile::GetFileArray($ImageID);
			if ($arImage['WIDTH']>$Width || $arImage['HEIGHT']>$Height) {
				return true;
			}
		}
		return false;
	}
	static function ResizeAll($Value, $Params) {
		if (is_array($Value)) {
			$bNeedResize = false;
			$arImagesFull = array();
			foreach($Value as $ValueItem) {
				if ($ValueItem>0) {
					$Image = CFile::GetFileArray($ValueItem);
					$arImagesFull[] = $Image;
					if ($Image['WIDTH']>$Params['width_value'] || $Image['HEIGHT']>$Params['height_value']) {
						$bNeedResize = true;
					}
				}
			}
			if ($bNeedResize) {
				foreach($arImagesFull as $Key => $arImage) {
					$Image = self::ResizeImage($arImage, $Params);
					if ($Image!==false) {
						$arImagesFull[$Key] = $Image;
					}
				}
				return $arImagesFull;
			}
		} elseif ($Value>0) {
			$Image = self::ResizeImage($Value, $Params);
			if ($Image!==false) {
				return $Image;
			}
		}
		return false;
	}
	static function ResizeImage($Image, $Params) {
		$Params['method'] = in_array($Params['method'],array('BX_RESIZE_IMAGE_PROPORTIONAL','BX_RESIZE_IMAGE_EXACT','BX_RESIZE_IMAGE_PROPORTIONAL_ALT')) ? $Params['method'] : BX_RESIZE_IMAGE_PROPORTIONAL;
		$arSize = array('width'=>IntVal($Params['width_value']),'height'=>IntVal($Params['height_value']));
		$arFilters = $Params['sharpen']=='Y' ? array(array('name'=>'sharpen','precision'=>15)) : array();
		$arResizedImage = CFile::ResizeImageGet($Image, $arSize, $Params['method'], false, $arFilters);
		if (is_array($arResizedImage) && is_file($_SERVER['DOCUMENT_ROOT'].$arResizedImage['src'])) {
			return CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arResizedImage['src']);
		}
		return false;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$arSize = array('width'=>IntVal($Params['width_value']),'height'=>IntVal($Params['height_value']));
		if (!is_array($Params['field_source']) || empty($Params['field_source']) || IntVal($Params['width_value'])<=0 || IntVal($Params['height_value'])<=0) {
			return false;
		}
		$arUpdateFields = array();
		$arUpdateProperties = array();
		foreach($Params['field_source'] as $SourceField) {
			if(preg_match('#^PROPERTY_(\d+)$#i',$SourceField,$M)) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$M[1]);
				if (is_array($arProp)) {
					$Value = $arProp['VALUE'];
					$Images = self::ResizeAll($Value, $Params);
					if ($Images!==false) {
						$arUpdateProperties[$arProp['ID']] = $Images;
					}
				}
			} else {
				$Value = $arElement[$SourceField];
				if ($Value>0) {
					$arImage = CFile::GetFileArray($Value);
					if ($arImage['WIDTH']>$arSize['width'] || $arImage['HEIGHT']>$arSize['height']) {
						$Image = self::ResizeImage($arImage, $Params);
						if ($Image!==false) {
							$arUpdateFields[$SourceField] = $Image;
						}
					}
				}
			}
		}
		if (!empty($arUpdateFields)){
			$IBlockElement = new CIBlockElement;
			if ($IBlockElement->Update($ElementID,$arUpdateFields)) {
				CWDA::Log('Resize '.$SourceField.' for element #'.$ElementID.' ['.$arUpdateFields['DETAIL_PICTURE']['SRC'].']', self::CODE);
				$bResult = true;
			}
		}
		if (!empty($arUpdateProperties)){
			foreach($arUpdateProperties as $PropertyID => $PropertyValue) {
				CIBlockElement::SetPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array($PropertyID=>$PropertyValue));
				CWDA::Log('Resize properties pictures for element #'.$ElementID, self::CODE);
				$bResult = true;
			}
		}
		return $bResult;
	}
}
?>