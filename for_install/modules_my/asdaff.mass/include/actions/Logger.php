<?
class CWDA_Logger extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'LOGGER';
	CONST NAME = 'Логирование';
	CONST FILE_NAME = '/bitrix/modules/asdaff.mass/!log.#';
	CONST CSV_SEPARATOR = ';';
	//
	static function GetDescription() {
		$Descr = 'Плагин сохраняет данные найденных элементов в файл.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'LOG_TYPE' => 'Формат выходного файла:',
				'LOG_TYPE_TXT' => '[TXT] Текст',
				'LOG_TYPE_CSV' => '[CSV] Значения, разделённые запятыми',
			'FILE_NAME' => 'Имя файла (знак решетки # заменяетя на расширение файла):',
			'FIELDS_MANAGEMENT' => 'Поля для сохранения в файл (только для CSV)',
				'FIELDS_ALL' => 'Все доступные поля',
				'FIELDS_SELECTED' => 'Выбранные поля',
				'MOVE_RIGHT' => '&gt;',
				'MOVE_UP' => 'Выше',
				'MOVE_DOWN' => 'Ниже',
				'DELETE' => 'Удалить',
			'ENCODING' => 'Кодировка файла: ',
			'ALERT_CSV_NO_FIELDS_SELECTED' => 'Для сохранения в CSV необходимо указать хотя бы одно поле',
			'CATALOG_FIELD_QUANTITY' => 'Остаток',
			'CATALOG_FIELD_QUANTITY_RESERVED' => 'Зарезервировано',
			'CATALOG_FIELD_CATALOG_AVAILABLE' => 'Доступность',
			'CATALOG_FIELD_CATALOG_WEIGHT' => 'Вес, г',
			'CATALOG_FIELD_CATALOG_WIDTH' => 'Ширина, мм',
			'CATALOG_FIELD_CATALOG_LENGTH' => 'Длина, мм',
			'CATALOG_FIELD_CATALOG_HEIGHT' => 'Высота, мм',
			'CATALOG_FIELD_CATALOG_PURCHASING_PRICE' => 'Закупочная цена',
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
		function WDA_Params_Update_<?=self::CODE?>(){
			WDA_<?=self::CODE?>_MoveElement($.trim(window.WdaProfileData.PARAMS.match(/params\[fields_selected\]\=([A-z0-9_,]+)$/i)[1]).split(','));
			//$('#wda_fields_all option:selected').removeAttr('selected');
		}
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			var FieldsAll = $('#wda_fields_all').html(Select.html()).find('option[value=""],option[value=ACTIVE_DATE],option[value=SECTION_GLOBAL_ACTIVE]').remove();
			var IBlockID = $('#wda_select_iblock').val();
		}
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
				if($('#wda_log_type').val()=='csv' && $('#wda_fields_selected option').length==0) {
					alert('<?=self::GetMessage('ALERT_CSV_NO_FIELDS_SELECTED',true);?>');
					WdaCanSubmit = false;
				}
			}
		});
		function WDA_<?=self::CODE?>_MoveElement(Values){
			var Select1 = $('#wda_fields_all');
			var Select2 = $('#wda_fields_selected');
			$.each(Values,function(Index,Value){
				var OptionSource = Select1.find('option[value='+Value+']');
				var OptionExists = Select2.find('option[value='+Value+']');
				if(OptionExists.length==0) {
					var Option = $('<option value="'+OptionSource.val()+'">'+OptionSource.text()+'</option>');
					Select2.append(Option);
				}
			});
			WDA_<?=self::CODE?>_Serialize();
		}
		function WDA_<?=self::CODE?>_Serialize(){
			var SelectedValues = [];
			$('#wda_fields_selected option').each(function(){
				SelectedValues.push($(this).val());
			});
			$('#wda_fields_serialized').val(SelectedValues.join(','));
		}
		function WDA_<?=self::CODE?>_MoveRight(){
			var Select1 = $('#wda_fields_all');
			var Select2 = $('#wda_fields_selected');
			var OptionSource = Select1.find('option:selected');
			if(OptionSource.length>0){
				var Values = [];
				OptionSource.each(function(){
					Values.push($(this).val());
				});
				WDA_<?=self::CODE?>_MoveElement(Values);
			}
			WDA_<?=self::CODE?>_Serialize();
		}
		function WDA_<?=self::CODE?>_MoveUp(){
			var OptionSource = $('#wda_fields_selected option:selected');
			OptionSource.first().prev().before(OptionSource);
			WDA_<?=self::CODE?>_Serialize();
		}
		function WDA_<?=self::CODE?>_MoveDown(){
			var OptionSource = $('#wda_fields_selected option:selected');
			OptionSource.last().next().after(OptionSource);
			WDA_<?=self::CODE?>_Serialize();
		}
		function WDA_<?=self::CODE?>_Delete(){
			var OptionSource = $('#wda_fields_selected option:selected');
			OptionSource.remove();
			WDA_<?=self::CODE?>_Serialize();
		}
		function WDA_<?=self::CODE?>_TypeChange(Select){
			$('#wda_settings_fields').css('display',($(Select).val()=='csv'?'':'none'));
		}
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="field">
				<div class="wda_settings_header"><?=self::GetMessage('LOG_TYPE');?></div>
				<div>
					<select name="params[log_type]" id="wda_log_type" class="wda_select_field" onchange="WDA_<?=self::CODE?>_TypeChange(this)">
						<option value="txt"><?=self::GetMessage('LOG_TYPE_TXT');?></option>
						<option value="csv"><?=self::GetMessage('LOG_TYPE_CSV');?></option>
					</select>
				</div>
				<br/>
			</div>
			<div class="field">
				<div class="wda_settings_header"><?=self::GetMessage('FILE_NAME');?></div>
				<div>
					<input type="text" name="params[file_name]" value="<?=self::FILE_NAME;?>" size="50" id="wda_file_name" style="font-family:monospace;width:100%;" />
				</div>
				<br/>
			</div>
			<div class="field">
				<div>
					<?=self::GetMessage('ENCODING');?>
					<select name="params[encoding]">
						<option value="CP1251">windows-1251</option>
						<option value="UTF-8">UTF-8</option>
					</select>
				</div>
				<br/>
			</div>
			<div class="field" id="wda_settings_fields" style="display:none">
				<div>
					<table style="width:100%;">
						<tbody>
							<tr class="heading">
								<td colspan="4"><?=self::GetMessage('FIELDS_MANAGEMENT');?></td>
							</tr>
							<tr>
								<td style="width:50%">
									<div class="wda_settings_header"><?=self::GetMessage('FIELDS_ALL');?></div>
									<select size="10" id="wda_fields_all" style="height:220px; width:100%" multiple="multiple"></select>
								</td>
								<td style="padding:8px; width:40px">
									<input type="button" value="<?=self::GetMessage('MOVE_RIGHT');?>" class="move_right" onclick="WDA_<?=self::CODE?>_MoveRight()" />
								</td>
								<td style="width:50%">
									<div class="wda_settings_header"><?=self::GetMessage('FIELDS_SELECTED');?></div>
									<select name="params[fields_selected]" size="10" id="wda_fields_selected" style="height:220px; width:100%" multiple="multiple"></select>
								</td>
								<td style="padding:8px; width:40px">
									<div><input type="button" value="<?=self::GetMessage('MOVE_UP');?>" class="move_up" onclick="WDA_<?=self::CODE?>_MoveUp()" /></div>
									<br/>
									<div><input type="button" value="<?=self::GetMessage('MOVE_DOWN');?>" class="move_down" onclick="WDA_<?=self::CODE?>_MoveDown()" /></div>
									<br/>
									<div><input type="button" value="<?=self::GetMessage('DELETE');?>" class="delete" onclick="WDA_<?=self::CODE?>_Delete()" /></div>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="params[fields_selected]" value="" id="wda_fields_serialized" />
				</div>
				<br/>
			</div>
		</div>
		<?
	}
	static function WriteToFile($FileName, $Line){
		$Handle = fopen($_SERVER['DOCUMENT_ROOT'].$FileName, 'a+');
		@flock($Handle, LOCK_EX);
		fwrite($Handle, $Line.PHP_EOL);
		@flock($Handle, LOCK_UN);
		fclose($Handle);
	}
	static function EscapeTxt($Value){
		$Value = str_replace("\n",'\n',$Value);
		$Value = str_replace("\r",'',$Value);
		$Value = str_replace("\t",'',$Value);
		return $Value;
	}
	static function EscapeCsv($Value){
		$Value = '"'.str_replace('"','""',$Value).'"';
		return $Value;
	}
	static function Process($ElementID, $arElement, $Params) {
		$FileName = $Params['file_name'];
		if(empty($FileName)){
			$FileName = self::FILE_NAME;
		}
		$FileName = $FileName;
		//
		switch(ToUpper($Params['log_type'])){
			case 'TXT':
				$FileName = str_replace('#','txt',$FileName);
				if($_SESSION['WDA_FIRST'] || $GLOBALS['WDA_FIRST']) {
					@unlink($_SERVER['DOCUMENT_ROOT'].$FileName);
				}
				self::WriteToFile($FileName, '['.$ElementID.'] '.self::EscapeTxt($arElement['NAME']));
				break;
			case 'CSV':
				$FileName = str_replace('#','csv',$FileName);
				if($_SESSION['WDA_FIRST'] || $GLOBALS['WDA_FIRST']) {
					@unlink($_SERVER['DOCUMENT_ROOT'].$FileName);
				}
				$arFields = explode(',',$Params['fields_selected']);
				// Write header
				if($_SESSION['WDA_FIRST'] || $GLOBALS['WDA_FIRST']) {
					$arLine = array();
					foreach($arFields as $Code){
						$Line = $Code;
						if(in_array($Code,array('ID','NAME','CODE','ACTIVE','XML_ID','SORT','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','PREVIEW_PICTURE','DETAIL_TEXT','DETAIL_TEXT_TYPE','DETAIL_PICTURE','DATE_ACTIVE_FROM','DATE_ACTIVE_TO','SHOW_COUNTER','TAGS','DATE_CREATE','CREATED_BY','TIMESTAMP_X','MODIFIED_BY'))) {
							$Line = GetMessage('IBLOCK_FIELD_'.$Code);
						} elseif($PropID = CWDA::IsProperty($Code)){
							$bPropFound = false;
							foreach($arElement['PROPERTIES'] as $arProperty){
								if($arProperty['ID']==$PropID) {
									$Line = $arProperty['NAME'];
									$bPropFound = true;
									break;
								}
							}
						} elseif ($PriceID = CWDA::IsPrice($Code)){
							if (CModule::IncludeModule('catalog')) {
								$arPrice = CCatalogGroup::GetByID($PriceID);
								$Line = $arPrice['NAME_LANG'];
							}
						} elseif (CWDA::IsCatalogField($Code)){
							$arCatalogFieldsNames = array(
								'CATALOG_QUANTITY' => self::GetMessage('CATALOG_FIELD_QUANTITY',true),
								'CATALOG_QUANTITY_RESERVED' => self::GetMessage('CATALOG_FIELD_QUANTITY_RESERVED',true),
								'CATALOG_AVAILABLE' => self::GetMessage('CATALOG_FIELD_CATALOG_AVAILABLE',true),
								'CATALOG_WEIGHT' => self::GetMessage('CATALOG_FIELD_CATALOG_WEIGHT',true),
								'CATALOG_WIDTH' => self::GetMessage('CATALOG_FIELD_CATALOG_WIDTH',true),
								'CATALOG_LENGTH' => self::GetMessage('CATALOG_FIELD_CATALOG_LENGTH',true),
								'CATALOG_HEIGHT' => self::GetMessage('CATALOG_FIELD_CATALOG_HEIGHT',true),
								'CATALOG_PURCHASING_PRICE' => self::GetMessage('CATALOG_FIELD_CATALOG_PURCHASING_PRICE',true),
							);
							$Line = isset($arCatalogFieldsNames[$Code]) ? $arCatalogFieldsNames[$Code] : $Code;
						}
						$Line = self::EscapeCsv($Line);
						$Line = trim($Line);
						$arLine[] = $Line;
					}
					$Line = implode(self::CSV_SEPARATOR,$arLine);
					unset($arLine);
					if ($Params['encoding']=='CP1251' && CWDA::IsUtf()) {
						$Line = CWDA::ConvertCharset($Line,'UTF-8','CP1251');
					} elseif ($Params['encoding']=='UTF-8' && !CWDA::IsUtf()){
						$Line = CWDA::ConvertCharset($Line,'CP1251','UTF-8');
					}
					self::WriteToFile($FileName,$Line);
				}
				// Write line
				foreach($arFields as $Code){
					$Line = $Code;
					if(in_array($Code,array('ID','NAME','CODE','XML_ID','SORT','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','DETAIL_TEXT','DETAIL_TEXT_TYPE','DATE_ACTIVE_FROM','DATE_ACTIVE_TO','SHOW_COUNTER','TAGS','DATE_CREATE','CREATED_BY','TIMESTAMP_X','MODIFIED_BY'))) {
						$Line = $arElement['~'.$Code];
					} elseif (in_array($Code,array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
						$Line = is_numeric($arElement[$Code]) && $arElement[$Code]>0 ? CFile::GetPath($arElement[$Code]) : '';
					} elseif (in_array($Code,array('ACTIVE'))) {
						$Line = $arElement[$Code]=='Y'?GetMessage('MAIN_YES'):GetMessage('MAIN_NO');
					} elseif($PropID = CWDA::IsProperty($Code)){
						foreach($arElement['PROPERTIES'] as $arProperty){
							if($arProperty['ID']==$PropID) {
								$Value = $arProperty['VALUE'];
								if($arProperty['PROPERTY_TYPE']=='F') {
									if(!is_array($Value) && is_numeric($Value) && $Value>0) {
										$Value = array($Value);
									}
									foreach($Value as $Key => $Item){
										$Value[$Key] = CFile::GetPath($Item);
									}
								}
								if(is_array($Value)){
									$Value = implode(', ',$Value);
								}
								$Line = $Value;
								break;
							}
						}
					} elseif ($PriceID = CWDA::IsPrice($Code)){
						$Line = $arElement['CATALOG_PRICE_'.$PriceID];
					} elseif (CWDA::IsCatalogField($Code)){
						$Line = $arElement[$Code];
					}
					$Line = self::EscapeCsv($Line);
					$Line = trim($Line);
					$arLine[] = $Line;
				}
				$Line = implode(self::CSV_SEPARATOR,$arLine);
				unset($arLine);
				if ($Params['encoding']=='CP1251' && CWDA::IsUtf()) {
					$Line = CWDA::ConvertCharset($Line,'UTF-8','CP1251');
				} elseif ($Params['encoding']=='UTF-8' && !CWDA::IsUtf()){
					$Line = CWDA::ConvertCharset($Line,'CP1251','UTF-8');
				}
				self::WriteToFile($FileName,$Line);
				break;
		}
		return true;
	}
}
?>