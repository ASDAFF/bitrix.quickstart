<?
class CWDA_SetValues extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'SET_VALUES';
	CONST NAME = 'Заполнение значений';
	//
	CONST COUNTER_KEY = 'WDA_SET_VALUES_COUNTER_VALUE';
	//
	static function GetDescription() {
		$Descr = 'Плагин выполняет массовое заполнение полей. В настоящее время не поддерживаются поля «Картинка для анонса» и «Детальная картинка», а также свойства типа «Файл» и «Видео».';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'ALERT_NO_FIELD_TARGET' => 'Укажите поле для заполнения значениями',
			'ALERT_NO_FIELD_TARGET_VALUE' => '--- сначала выберите поле, свойство, цену, или параметр каталога ---',
			//
			'WARNING_SEQUENCE_NO_WRITE' => '<i>Для свойства не указана возможность редактирования (галочка «Разрешается изменять значения» в настройках свойства на странице <a href="/bitrix/admin/iblock_edit.php?type=%s&tabControl_active_tab=edit2&lang=ru&ID=%d&admin=Y" target="_blank">настроек инфоблока</a>).</i><br/><br/>',
			//
			'FIELD_TARGET' => 'Поле, свойство, цена, параметр',
			'FIELD_VALUE' => 'Значение',
			'COUNTER_OPTION' => 'Использовать счетчик, чтобы заменять <input type="text" name="params[counter_search]" value="#VALUE#" style="width:80px" /> с шагом <input type="text" name="params[counter_step]" value="1" style="width:80px" />, начиная со значения <input type="text" name="params[counter_first]" value="1" style="width:80px" />',
			'RANDOM_OPTION' => 'Генерировать случайное числовое значение в интервале от <input type="text" name="params[random_from]" value="" style="width:80px" /> до <input type="text" name="params[random_to]" value="" style="width:80px" />',
			//
			'PARAM_TEXT_TYPE_TEXT' => 'Текст',
			'PARAM_TEXT_TYPE_HTML' => 'HTML',
			//
			'SIZE_UNIT_MM' => ' мм',
			'SIZE_UNIT_G' => ' г',
			//
			'OPTION_PURCHASING_PRICE' => 'Закупочная цена',
			'OPTION_QUANTITY_RESERVED' => 'Зарезервированное количество',
			'OPTION_QUANTITY_TRACE' => 'Количественный учет',
			'OPTION_CAN_BUY_ZERO' => 'Разрешить покупку при отсутствии товара (включая разрешение отрицательного количества товара)',
			'OPTION_SUBSCRIBE' => 'Разрешить подписку при отсутствии товара',
			'OPTION_VAT_INCLUDED' => 'НДС включен в цену',
			'OPTION_VAT_ID' => 'Величина НДС',
			'OPTION_WEIGHT' => 'Вес, г',
			'OPTION_WIDTH' => 'Ширина, мм',
			'OPTION_LENGTH' => 'Длина, мм',
			'OPTION_HEIGHT' => 'Высота, мм',
			'OPTION_MEASURE' => 'Единица измерения',
			'OPTION_MEASURE_RATIO' => 'Коэффициент единицы измерения',
			//
			'PARAM_D' => 'по умолчанию (из настроек модуля)',
			'PARAM_Y' => 'да',
			'PARAM_N' => 'нет',
			//
			'SELECT_PRICE_TARGET' => 'Выберите поле, свойство, цену или параметр каталога для заполнения значений.',
			//
			'OR_SET_PRICE_EXTRA' => 'или укажите наценку: ',
			'OR_SET_PRICE_EXTRA_NOTICE' => '(если выбрана наценка, то применяется наценки, иначе применяется цена)',
			//
			'LOADING' => 'Загрузка...',
			//
			'CATALOG_STORE' => 'Остаток на складе',
			//
			'OPTGROUP_SEO' => 'SEO (Мета-теги)',
			'SEO_ELEMENT_META_TITLE' => '[TITLE] Заголовок окна браузера',
			'SEO_ELEMENT_META_KEYWORDS' => '[KEYWORDS] Ключевые слова',
			'SEO_ELEMENT_META_DESCRIPTION' => '[DESCRIPTION] Описание страницы',
			'SEO_ELEMENT_PAGE_TITLE' => '[PAGE_TITLE] Заголовок страницы',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		$arStores = CWDA::GetStoresList();
		$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/components/bitrix/main.lookup.input/templates/iblockedit/style.css');
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/main.lookup.input/script.js');
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/main.lookup.input/templates/iblockedit/script2.js');
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
			SelectTarget.find('[value=ID],[value=ACTIVE_DATE],[value=SECTION_GLOBAL_ACTIVE],[value=CATALOG_AVAILABLE],[value=TIMESTAMP_X],[data-type=F],[data-type="S:Video"]').remove();
			if (IBlock>0) {
				SelectTarget.append('<optgroup label="<?=self::GetMessage('OPTGROUP_SEO',true);?>" data-group="SEO"></label>');
			}
			SelectTarget.change();
			// Add custom options
			var CustomOptions = [
				// Catalog
				{VALUE:'CATALOG_PURCHASING_PRICE',NAME:'<?=self::GetMessage('OPTION_PURCHASING_PRICE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_PURCHASING_PRICE',true);?>',TYPE:'P',GROUP:'PRICES'},
				{VALUE:'CATALOG_QUANTITY_RESERVED',NAME:'<?=self::GetMessage('OPTION_QUANTITY_RESERVED',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_QUANTITY_RESERVED',true);?>',TYPE:'N',GROUP:'CATALOG'},
				{VALUE:'CATALOG_QUANTITY_TRACE',NAME:'<?=self::GetMessage('OPTION_QUANTITY_TRACE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_QUANTITY_TRACE',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_CAN_BUY_ZERO',NAME:'<?=self::GetMessage('OPTION_CAN_BUY_ZERO',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_CAN_BUY_ZERO',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_SUBSCRIBE',NAME:'<?=self::GetMessage('OPTION_SUBSCRIBE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_SUBSCRIBE',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_VAT_INCLUDED',NAME:'<?=self::GetMessage('OPTION_VAT_INCLUDED',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_VAT_INCLUDED',true);?>',TYPE:'C',GROUP:'CATALOG'},
				{VALUE:'CATALOG_VAT_ID',NAME:'<?=self::GetMessage('OPTION_VAT_ID',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_VAT_ID',true);?>',TYPE:'L',GROUP:'CATALOG'},
				{VALUE:'CATALOG_LENGTH',NAME:'<?=self::GetMessage('OPTION_LENGTH',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_LENGTH',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_WIDTH',NAME:'<?=self::GetMessage('OPTION_WIDTH',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_WIDTH',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_HEIGHT',NAME:'<?=self::GetMessage('OPTION_HEIGHT',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_HEIGHT',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_MEASURE',NAME:'<?=self::GetMessage('OPTION_MEASURE',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_MEASURE',true);?>',TYPE:'N:INT',GROUP:'CATALOG'},
				{VALUE:'CATALOG_MEASURE_RATIO',NAME:'<?=self::GetMessage('OPTION_MEASURE_RATIO',true);?>',NAME_FULL:'<?=self::GetMessage('OPTION_MEASURE_RATIO',true);?>',TYPE:'N',GROUP:'CATALOG'},
				// Stores
				<?foreach($arStores as $arStore):?>
				{VALUE:'<?=$arStore['WDA_CODE'];?>',NAME:'<?=$arStore['WDA_NAME'];?>',NAME_FULL:'<?=self::GetMessage('CATALOG_STORE',true);?> <?=$arStore['WDA_NAME_FULL'];?>',TYPE:'N',GROUP:'CATALOG'},
				<?endforeach?>
				// SEO
				{VALUE:'SEO_ELEMENT_META_TITLE',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_TITLE',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_TITLE',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_META_KEYWORDS',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_KEYWORDS',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_KEYWORDS',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_META_DESCRIPTION',NAME:'<?=self::GetMessage('SEO_ELEMENT_META_DESCRIPTION',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_META_DESCRIPTION',true);?>',TYPE:'S',GROUP:'SEO'},
				{VALUE:'SEO_ELEMENT_PAGE_TITLE',NAME:'<?=self::GetMessage('SEO_ELEMENT_PAGE_TITLE',true);?>',NAME_FULL:'<?=self::GetMessage('SEO_ELEMENT_PAGE_TITLE',true);?>',TYPE:'S',GROUP:'SEO'},
			];
			for(var i in CustomOptions) {
				if (!CustomOptions.hasOwnProperty(i)) continue;
				var Option = CustomOptions[i];
				SelectTarget.find('optgroup[data-group='+Option.GROUP+']').append('<option value="'+Option.VALUE+'" data-name="'+Option.NAME+'" data-type="'+Option.TYPE+'" data-group="'+Option.GROUP+'">'+Option.NAME_FULL+'</option>')
			}
			// Move weight before length
			$('#wda_field_target option[value=CATALOG_WEIGHT]').insertBefore('#wda_field_target option[value=CATALOG_LENGTH]').text('<?=self::GetMessage('OPTION_WEIGHT');?>');
			// Event handlers
			SelectTarget.on('change',function(){
				window.GLOBAL_arMapObjects = undefined;
				window.ymaps = undefined;
				window.bYandexMapScriptsLoaded = undefined;
				var TargetValue = $(this).val();
				$('#wda_additional_settings_<?=self::CODE?>').html('<div class="loading"><?=self::GetMessage('LOADING',true);?></div>');
				$.ajax({
					url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam('show_additional_settings=Y&action='.self::CODE,array('show_action_settings','show_additional_settings','ACTION','IBLOCK_ID'));?>&iblock_id='+$('#wda_select_iblock').val()+'&target='+TargetValue,
					type: 'GET',
					data: '',
					success: function(HTML) {
						$('#wda_additional_settings_<?=self::CODE?>').html(HTML).find('input[type=checkbox]').each(function(){
							BX.adminFormTools.modifyCheckbox(this);
						});
						BX.onCustomEvent(window, 'wda_field_callback', [this, $('#wda_additional_settings_<?=self::CODE?>')]); 
					}
				});
			});
		}
		</script>
		<style>
		#wda_form #wda_additional_settings_SET_VALUES .loading {padding-left:34px; text-indent:0;}
		</style>
		<?
	}
	static function GetFieldArray($Field, $IBlockID) {
		$Field = ToUpper($Field);
		$arResult = array(
			'ID' => $Field,
			'IBLOCK_ID' => $IBlockID,
			'CODE' => $Field,
			'TIMESTAMP_X' => '',
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => '',
			'MULTIPLE' => 'N',
			'MULTIPLE_CNT' => '1',
			'WITH_DESCRIPTION' => 'N',
			'DEFAULT_VALUE' => '',
			'COL_COUNT' => '66',
			'USER_TYPE_SETTINGS' => '',
			'TMP_ID' => '',
			'XML_ID' => '',
			'SEARCHABLE' => 'N',
			'FILTRABLE' => 'N',
			'IS_REQUIRED' => 'N',
			'VERSION' => '1',
			'HINT' => '1',
		);
		if(in_array($Field,array('NAME','CODE','SORT','EXTERNAL_ID','XML_ID','ACTIVE','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','DETAIL_TEXT','DETAIL_TEXT_TYPE','PREVIEW_PICTURE','DETAIL_PICTURE','DATE_ACTIVE_FROM','DATE_ACTIVE_TO','SHOW_COUNTER','TAGS','DATE_CREATE','CREATED_BY','TIMESTAMP_X','MODIFIED_BY'))) {
			$arResult['FIELD_TYPE'] = 'FIELD';
			switch($Field) {
				case 'SORT':
					$arResult['PROPERTY_TYPE'] = 'N';
					$arResult['COL_COUNT'] = '10';
					break;
				case 'ACTIVE':
					$arResult['NAME'] = 'Active';
					$arResult['PROPERTY_TYPE'] = 'L:WDA_ACTIVE';
					$arResult['LIST_TYPE'] = 'C';
					break;
				case 'PREVIEW_TEXT':
				case 'DETAIL_TEXT':
					$arResult['COL_COUNT'] = '60';
					$arResult['ROW_COUNT'] = '6';
					$arResult['USER_TYPE'] = 'HTML';
					break;
				case 'PREVIEW_TEXT_TYPE':
				case 'DETAIL_TEXT_TYPE':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_TEXT_TYPE';
					break;
				case 'PREVIEW_PICTURE':
				case 'DETAIL_PICTURE':
					$arResult['PROPERTY_TYPE'] = 'F:WDA';
					$arResult['FILE_TYPE'] = 'jpg,jpeg,jpe,gif,png';
					break;
				case 'DATE_ACTIVE_FROM':
				case 'DATE_ACTIVE_TO':
				case 'DATE_CREATE':
				case 'TIMESTAMP_X':
					$arResult['USER_TYPE'] = 'DateTime';
					break;
				case 'SHOW_COUNTER':
					$arResult['PROPERTY_TYPE'] = 'N';
					$arResult['COL_COUNT'] = '10';
					break;
				case 'TAGS':
					$arResult['COL_COUNT'] = '60';
					break;
				case 'CREATED_BY':
				case 'MODIFIED_BY':
					$arResult['USER_TYPE'] = 'UserID';
					break;
			}
		} elseif (preg_match('#^CATALOG_PRICE_(\d+)$#',$Field,$M) || $Field=='CATALOG_PURCHASING_PRICE') {
			$arResult['FIELD_TYPE'] = 'PRICE';
			$arResult['NAME'] = 'Price #'.$M[1];
			$arResult['PROPERTY_TYPE'] = 'N';
			$arResult['COL_COUNT'] = '15';
		} elseif (preg_match('#^CATALOG_([\w\d_]+)$#',$Field,$M)) {
			$arResult['FIELD_TYPE'] = 'CATALOG';
			switch($Field) {
				case 'CATALOG_QUANTITY':
				case 'CATALOG_QUANTITY_RESERVED':
				case 'CATALOG_WEIGHT':
				case 'CATALOG_LENGTH':
				case 'CATALOG_WIDTH':
				case 'CATALOG_HEIGHT':
					$arResult['PROPERTY_TYPE'] = 'N:INT';
					$arResult['COL_COUNT'] = '15';
					break;
				case 'CATALOG_QUANTITY_TRACE':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_QUANTITY_TRACE';
					break;
				case 'CATALOG_CAN_BUY_ZERO':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_CAN_BUY_ZERO';
					break;
				case 'CATALOG_SUBSCRIBE':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_SUBSCRIBE';
					break;
				case 'CATALOG_VAT_INCLUDED':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_VAT_INCLUDED';
					break;
				case 'CATALOG_VAT_ID':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_VAT_ID';
					break;
				case 'CATALOG_MEASURE':
					$arResult['PROPERTY_TYPE'] = 'L:WDA_MEASURE';
					break;
				case 'CATALOG_MEASURE_RATIO':
					$arResult['PROPERTY_TYPE'] = 'N';
					$arResult['COL_COUNT'] = '15';
					break;
				default:
					if (preg_match('#^CATALOG_STORE_(\d+)$#',$Field,$M)) {
						$arResult['PROPERTY_TYPE'] = 'N';
						$arResult['COL_COUNT'] = '15';
					}
					break;
			}
		} elseif (preg_match('#^SEO_([\w\d_]+)$#',$Field,$M)) {
			$arResult['PROPERTY_TYPE'] = 'S';
			$arResult['COL_COUNT'] = '60';
			$arResult['ROW_COUNT'] = '3';
		}
		return $arResult;
	}
	static function ShowAdditionalSettings() {
		$IBlockID = IntVal($_GET['iblock_id']);
		$Target = htmlspecialcharsbx($_GET['target']);
		CModule::IncludeModule('iblock');
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin_tools.php");
		CModule::IncludeModule('fileman');
		$arProperty = false;
		$strFieldType = false;
		if (preg_match('#^PROPERTY_(\d+)$#',$Target,$M)) {
			$strFieldType = 'PROP';
			$resProperty = CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>$IBlockID,'ID'=>$M[1]));
			$arProperty = $resProperty->GetNext(false,false);
		} else {
			$arProperty = self::GetFieldArray($Target, $IBlockID);
			$strFieldType = $arProperty['FIELD_TYPE'];
			unset($arProperty['FIELD_TYPE']);
		}
		if (is_array($arProperty)) {
			ob_start();
			if ($arProperty['PROPERTY_TYPE']=='N' && $arProperty['USER_TYPE']=='Sequence') {
				if ($arProperty['USER_TYPE_SETTINGS']['write']!='Y') {
					$arIBlock = CIBlock::GetArrayByID($IBlockID);
					$IBlockType = $arIBlock['IBLOCK_TYPE_ID'];
					printf (self::GetMessage('WARNING_SEQUENCE_NO_WRITE',true), $IBlockType, $IBlockID);
				}
			} elseif ($arProperty['PROPERTY_TYPE']=='S' && $arProperty['USER_TYPE']=='FileMan') {
				print "";
			}
			switch($arProperty['PROPERTY_TYPE']) {
				case 'L:WDA_ACTIVE':
					print '<input name="PROP['.$arProperty['CODE'].'][n0]" value="Y" type="checkbox" checked="checked" />';
					break;
				case 'L:WDA_TEXT_TYPE':
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
						print '<option value="text">'.self::GetMessage('PARAM_TEXT_TYPE_TEXT',true).'</option>';
						print '<option value="html">'.self::GetMessage('PARAM_TEXT_TYPE_HTML',true).'</option>';
					print '</select>';
					break;
				case 'F:WDA':
					if(IntVal($GLOBALS['history_id'])>0) {
						print CFileInput::Show($arProperty['CODE'], 0, array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => array("W" => COption::GetOptionString("iblock", "detail_image_size"), "H" => COption::GetOptionString("iblock", "detail_image_size"))));
					} else {
						if (class_exists('\Bitrix\Main\UI\FileInput', true)) {
							print \Bitrix\Main\UI\FileInput::createInstance(array( "name" => $arProperty['CODE'], "description" => true, "upload" => true, "allowUpload" => "I", "medialib" => true, "fileDialog" => true, "cloud" => true, "delete" => true, "maxCount" => 1))->show($str_PREVIEW_PICTURE);
						} else {
							print CFileInput::Show($arProperty['CODE'], 0, array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => array("W" => COption::GetOptionString("iblock", "detail_image_size"), "H" => COption::GetOptionString("iblock", "detail_image_size"))), array('upload' => true, 'medialib' => true, 'file_dialog' => true, 'cloud' => true, 'del' => true, 'description' => true));
						}
					}
				case 'L:WDA_VAT_INCLUDED':
					print '<input name="PROP['.$arProperty['CODE'].'][n0]" value="Y" type="checkbox" />';
					break;
				case 'L:WDA_MEASURE':
					$arMeasureList = CWDA::GetMeasureList();
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
					foreach($arMeasureList as $arMeasure) {
						print '<option value="'.$arMeasure['ID'].'">'.$arMeasure['MEASURE_TITLE'].'</option>';
					}
					print '</select>';
					break;
				case 'L:WDA_VAT_ID':
					$arVatList = CWDA::GetVatList();
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
					foreach($arVatList as $arVat) {
						$strRate = '';
						if ($arVat['ID']>0) {
							$strRate = ' ('.number_format($arVat['RATE'],2,'.','').'%)';
						}
						print '<option value="'.$arVat['ID'].'">'.$arVat['NAME'].$strRate.'</option>';
					}
					print '</select>';
					break;
				case 'L:WDA_QUANTITY_TRACE':
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
						print '<option value="D">'.self::GetMessage('PARAM_D',true).'</option>';
						print '<option value="Y">'.self::GetMessage('PARAM_Y',true).'</option>';
						print '<option value="N">'.self::GetMessage('PARAM_N',true).'</option>';
					print '</select>';
					break;
				case 'L:WDA_CAN_BUY_ZERO':
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
						print '<option value="D">'.self::GetMessage('PARAM_D',true).'</option>';
						print '<option value="Y">'.self::GetMessage('PARAM_Y',true).'</option>';
						print '<option value="N">'.self::GetMessage('PARAM_N',true).'</option>';
					print '</select>';
					break;
				case 'L:WDA_SUBSCRIBE':
					print '<select name="PROP['.$arProperty['CODE'].'][n0]">';
						print '<option value="D">'.self::GetMessage('PARAM_D',true).'</option>';
						print '<option value="Y">'.self::GetMessage('PARAM_Y',true).'</option>';
						print '<option value="N">'.self::GetMessage('PARAM_N',true).'</option>';
					print '</select>';
					break;
				default:
					if (strlen($arProperty['ID'])) {
						_ShowPropertyField('PROP['.$arProperty["ID"].']', $arProperty, $Value=false, $InitDef=true, $VarsFromForm=false, $MaxFileSizeShow=50000, $FormName='wda_form', $Copy=false);
					}
					if($arProperty['ID']>0 && in_array($arProperty['PROPERTY_TYPE'],array('S','N')) && empty($arProperty['USER_TYPE']) || in_array($Target,array('NAME','CODE','SORT','EXTERNAL_ID'))) {
						?>
						<br/>
						<div>
							<table>
								<tbody>
									<tr>
										<td class="label">
											<input type="checkbox" name="params[use_counter]" value="Y" id="wda_use_counter" />
										</td>
										<td class="value">
											<label for="wda_use_counter">
												<?=self::GetMessage('COUNTER_OPTION',true);?>
											</label>
										</td>
									</tr>
									<tr>
										<td class="label">
											<input type="checkbox" name="params[use_random]" value="Y" id="wda_use_random" />
										</td>
										<td class="value">
											<label for="wda_use_random">
												<?=self::GetMessage('RANDOM_OPTION',true);?>
											</label>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?
					}
					break;
			}
			$HTML = ob_get_clean();
			if ($strFieldType=='PRICE') {
				$arCurrencies = CWDA::GetCurrencyList();
				$strCurrencySelect = '<select name="CURRENCY">';
				foreach($arCurrencies as $Key => $arCurrency) {
					$strCurrencySelect .= '<option value="'.$Key.'">'.$Key.'</option>';
				}
				$strCurrencySelect .= '</select>';
				if(preg_match('#^CATALOG_PRICE_(\d+)$#',$Target,$M)) {
					$arBasePrice = CCatalogGroup::GetBaseGroup();
					if(is_array($arBasePrice) && $arBasePrice['ID']!=$M[1] && CModule::IncludeModule('catalog')) {
						$arExtras = array();
						$resExtra = CExtra::GetList();
						while($arExtra = $resExtra->GetNext()){
							$arExtras[] = $arExtra;
						}
						if(!empty($arExtras)) {
							$strExtra = '<br/><br/><span>'.self::GetMessage('OR_SET_PRICE_EXTRA',true).'</span><br/><br/>';
							$strExtra .= '<select name="EXTRA_ID" style="min-width:125px">';
							$strExtra .= '<option value=""></option>';
							foreach($arExtras as $arExtra){
								$strExtra .= '<option value="'.$arExtra['ID'].'">'.$arExtra['NAME'].' ['.$arExtra['PERCENTAGE'].'%]</option>';
							}
							$strExtra .= '</select>';
							$strExtra .= ' &nbsp; <span>'.self::GetMessage('OR_SET_PRICE_EXTRA_NOTICE',true).'</span>';
							$strCurrencySelect .= $strExtra;
						}
					}
				}
				$HTML = str_replace('<br></td>','&nbsp;&nbsp;'.$strCurrencySelect.'</td>',$HTML);
			} elseif (in_array($Target,array('CATALOG_WIDTH','CATALOG_HEIGHT','CATALOG_LENGTH'))) {
				$HTML = str_replace('<br></td>','&nbsp;&nbsp;'.self::GetMessage('SIZE_UNIT_MM',true).'</td>',$HTML);
			} elseif (in_array($Target,array('CATALOG_WEIGHT'))) {
				$HTML = str_replace('<br></td>','&nbsp;&nbsp;'.self::GetMessage('SIZE_UNIT_G',true).'</td>',$HTML);
			}
			if (!CWDA::IsUtf()) {
				$HTML = CWDA::ConvertCharset($HTML,'CP1251','UTF-8');
			}
			$HTML = trim($HTML);
			if ($HTML=='') {
				$HTML = self::GetMessage('ALERT_NO_FIELD_TARGET_VALUE');
			}
			print $HTML;
		}
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_TARGET');?></div>
			<div>
				<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field" data-callback="wda_field_callback"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_PRICE_TARGET'));?></div>
			</div>
			<br/>
			<div class="wda_settings_header"><?=self::GetMessage('FIELD_VALUE');?></div>
			<div class="wda_additional_settings" id="wda_additional_settings_<?=self::CODE?>"><?=self::GetMessage('ALERT_NO_FIELD_TARGET_VALUE');?></div>
		</div>
		<?
	}
	static function GetSingleValue($Value) {
		if (is_array($Value)) {
			foreach($Value as $ValueItem) {
				return $ValueItem;
			}
		}
		return $Value;
	}
	static function TransformValueArray($Array, $Mode=1){
		// Mode = 1: array('n0'=>array('VALUE'=>'123')) => array('n0'=>'123')
		$arResult = array();
		switch($Mode) {
			case 1:
				foreach($Array as $Key => $Value) {
					$Item = $Value;
					if (is_array($Item) && isset($Item['VALUE'])) {
						$Item = $Item['VALUE'];
					}
					$arResult[] = $Item;
				}
				break;
		}
		return $arResult;
	}
	static function ReplaceCounter($Subject, $Params) {
		self::InitCounter($Params);
		$Storage = defined('WDA_CRON') && WDA_CRON===true ? $Storage = &$GLOBALS : $Storage = &$_SESSION;
		$Search = $Params['counter_search'];
		$CounterValue = $Storage['WDA_CUSTOM_'.self::CODE][self::COUNTER_KEY];
		$Subject = str_replace($Search,$CounterValue,$Subject);
		return $Subject;
	}
	static function InitCounter($Params){
		$Storage = defined('WDA_CRON') && WDA_CRON===true ? $Storage = &$GLOBALS : $Storage = &$_SESSION;
		$First = FloatVal($Params['counter_first']);
		if(!isset($Storage['WDA_CUSTOM_'.self::CODE][self::COUNTER_KEY])) {
			$Storage['WDA_CUSTOM_'.self::CODE][self::COUNTER_KEY] = $First;
		}
	}
	static function IncCounter($Params){
		$Storage = defined('WDA_CRON') && WDA_CRON===true ? $Storage = &$GLOBALS : $Storage = &$_SESSION;
		$Step = FloatVal($Params['counter_step']);
		$CounterValue = FloatVal($Storage['WDA_CUSTOM_'.self::CODE][self::COUNTER_KEY]);
		$CounterValue += $Step;
		$Storage['WDA_CUSTOM_'.self::CODE][self::COUNTER_KEY] = $CounterValue;
	}
	static function GenerateRandom($Subject, $Params) {
		$Params['random_from'] = IntVal($Params['random_from']);
		$Params['random_to'] = IntVal($Params['random_to']);
		$Subject = rand($Params['random_from'],$Params['random_to']);
		return $Subject;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		$bCatalogModule = CModule::IncludeModule('catalog');
		$Target = $Params['field_target'];
		$ValueArray = $Params['PROP'][$Target]; // Для свойств - далее будет переопределение
		$ValueSingle = self::GetSingleValue($ValueArray);
		if (strlen($Target)) {
			$IBlockElement = new CIBlockElement;
			if(in_array($Target,array('NAME','CODE','SORT','EXTERNAL_ID','XML_ID','ACTIVE','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','DETAIL_TEXT','DETAIL_TEXT_TYPE','PREVIEW_PICTURE','DETAIL_PICTURE','DATE_ACTIVE_FROM','DATE_ACTIVE_TO','SHOW_COUNTER','TAGS','DATE_CREATE','CREATED_BY','TIMESTAMP_X','MODIFIED_BY'))) {
				$arFields = array();
				$Value = $ValueSingle;
				if (in_array($Target,array('SORT','SHOW_COUNTER'))) {
					if(in_array($Target,array('SORT'))){
						if($Params['use_counter']=='Y') {
							$Value = self::ReplaceCounter($Value,$Params);
						}
						if($Params['use_random']=='Y'){
							$Value = self::GenerateRandom($Value,$Params);
						}
					}
					$Value = IntVal($Value);
				} elseif (in_array($Target,array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
					// ToDo
				} elseif (in_array($Target,array('ACTIVE'))) {
					$Value = ($Value=='Y' ? 'Y' : 'N');
				} elseif (in_array($Target,array('DATE_ACTIVE_FROM','DATE_ACTIVE_TO','DATE_CREATE','TIMESTAMP_X'))) {
					$Value = $Value['VALUE'];
				} elseif ($Target=='PREVIEW_TEXT') {
					$Value = $Params['PROP_PREVIEW_TEXT__n0__VALUE__TEXT_'];
					$arFields['PREVIEW_TEXT_TYPE'] = $Params['PROP_PREVIEW_TEXT__n0__VALUE__TYPE_']=='text' ? 'text' : 'html';
				} elseif ($Target=='DETAIL_TEXT') {
					$Value = $Params['PROP_DETAIL_TEXT__n0__VALUE__TEXT_'];
					$arFields['DETAIL_TEXT_TYPE'] = $Params['PROP_DETAIL_TEXT__n0__VALUE__TYPE_']=='text' ? 'text' : 'html';
				} elseif (in_array($Target,array('CREATED_BY','MODIFIED_BY'))) {
					$Value = $Value['VALUE'];
				}
				if (!CWDA::IsUtf()) {
					$Value = CWDA::ConvertCharset($Value);
				}
				if(in_array($Target,array('NAME','CODE','EXTERNAL_ID'))){
					if($Params['use_counter']=='Y') {
						$Value = self::ReplaceCounter($Value,$Params);
					}
					if($Params['use_random']=='Y') {
						$Value = self::GenerateRandom($Value,$Params);
					}
				}
				$arFields[$Target] = $Value;
				if ($IBlockElement->Update($ElementID,$arFields)) {
					CWDA::Log('Updated element #'.$ElementID.', fields: '.print_r($arFields,1));
					$bResult = true;
				} else {
					CWDA::Log('Error update element #'.$ElementID.', ['.$IBlockElement->LAST_ERROR.'] fields: '.print_r($arFields,1));
				}
			} elseif (preg_match('#^CATALOG_PRICE_(\d+)$#',$Target,$M)) {
				$PriceID = $M[1];
				$arCurrencies = CWDA::GetCurrencyList();
				$Currency = htmlspecialchars($Params['CURRENCY']);
				if (strlen($Currency) && isset($arCurrencies[$Currency])) {
					$Price = $ValueSingle;
					$ExtraID = false;
					if(!isset($GLOBALS['WDI_BASE_PRICE'])) {
						CModule::IncludeModule('catalog');
						$arBasePrice = CCatalogGroup::GetBaseGroup();
						$GLOBALS['WDI_BASE_PRICE'] = $arBasePrice;
					}
					if(is_array($GLOBALS['WDI_BASE_PRICE']) && $GLOBALS['WDI_BASE_PRICE']['ID']!=$PriceID) {
						$ExtraID = $Params['EXTRA_ID'];
					}
					if (CWDA::SetProductPrice($ElementID, $PriceID, $Price, $Currency, $ExtraID)) {
						$bResult = true;
					}
				}
			} elseif (preg_match('#^PROPERTY_(\d+)$#',$Target,$M)) {
				$PropertyID = $M[1];
				$Value = $Params['PROP'][$PropertyID];
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$PropertyID);
				switch($arProp['PROPERTY_TYPE']) {
					case 'S':
						switch($arProp['USER_TYPE']) {
							case 'UserID':
								$Value = self::TransformValueArray($Value, 1);
								break;
							case 'Date':
							case 'DateTime':
								$Value = self::TransformValueArray($Value, 1);
								break;
							case 'HTML':
								foreach ($Value as $Key => $Item) {
									$Type = $Params['PROP_'.$PropertyID.'__'.$Key.'__VALUE__TYPE_'];
									$Text = $Params['PROP_'.$PropertyID.'__'.$Key.'__VALUE__TEXT_'];
									$Value[$Key] = array('VALUE'=>array('TYPE'=>$Type,'TEXT'=>$Text));
								}
								break;
							case 'Video':
								// ToDo
								break;
						}
						if ($arProp['MULTIPLE']=='N') {
							$Value = self::GetSingleValue($Value);
						}
						break;
				}
				#CWDA::Log('Save property '.$PropertyID.', value: '.print_r($Value,1));
				if (!CWDA::IsUtf()) {
					if (is_array($Value)) {
						foreach($Value as $Key => $Item) {
							$Value[$Key] = CWDA::ConvertCharset($Item);
						}
					} else {
						$Value = CWDA::ConvertCharset($Value);
					}
				}
				if(in_array($arProp['PROPERTY_TYPE'],array('S','N'))){
					if($Params['use_counter']=='Y') {
						if (is_array($Value)) {
							foreach($Value as $Key => $Item) {
								$Value[$Key] = self::ReplaceCounter($Item,$Params);
							}
						} else {
							$Value = self::ReplaceCounter($Value,$Params);
						}
					}
					if($Params['use_random']=='Y') {
						if (is_array($Value)) {
							foreach($Value as $Key => $Item) {
								$Value[$Key] = self::GenerateRandom($Item,$Params);
							}
						} else {
							$Value = self::GenerateRandom($Value,$Params);
						}
					}
				}
				CIBlockElement::SetPropertyValuesEx($ElementID, $arElement['IBLOCK_ID'], array($PropertyID=>$Value));
				$bResult = true;
			} elseif (preg_match('#^CATALOG_([\w\d_]+)$#',$Target,$M)) {
				if ($bCatalogModule) {
					$Value = $ValueSingle;
					$arCurrencies = CWDA::GetCurrencyList();
					$Currency = htmlspecialchars($Params['CURRENCY']);
					if (in_array($Target,array('CATALOG_QUANTITY','CATALOG_PURCHASING_PRICE','CATALOG_QUANTITY_RESERVED','CATALOG_QUANTITY_TRACE','CATALOG_CAN_BUY_ZERO','CATALOG_SUBSCRIBE','CATALOG_VAT_INCLUDED','CATALOG_VAT_ID','CATALOG_WIDTH','CATALOG_LENGTH','CATALOG_HEIGHT','CATALOG_WEIGHT','CATALOG_MEASURE'))) {
						$arFields = array(
							'ID' => $ElementID
						);
						switch ($Target) {
							case 'CATALOG_QUANTITY':
								$arFields['QUANTITY'] = $Value;
								break;
							case 'CATALOG_PURCHASING_PRICE':
								$arFields['PURCHASING_PRICE'] = $Value;
								$arFields['PURCHASING_CURRENCY'] = $Currency;
								break;
							case 'CATALOG_QUANTITY_RESERVED':
								$arFields['QUANTITY_RESERVED'] = $Value;
								break;
							case 'CATALOG_QUANTITY_TRACE':
								$arFields['QUANTITY_TRACE'] = $Value;
								break;
							case 'CATALOG_CAN_BUY_ZERO':
								$arFields['CAN_BUY_ZERO'] = $Value;
								$arFields['NEGATIVE_AMOUNT_TRACE'] = $Value;
								break;
							case 'CATALOG_SUBSCRIBE':
								$arFields['SUBSCRIBE'] = $Value;
								break;
							case 'CATALOG_VAT_INCLUDED':
								$arFields['VAT_INCLUDED'] = $Value;
								break;
							case 'CATALOG_VAT_ID':
								$arFields['VAT_ID'] = $Value;
								break;
							case 'CATALOG_WIDTH':
								$arFields['WIDTH'] = $Value;
								break;
							case 'CATALOG_HEIGHT':
								$arFields['HEIGHT'] = $Value;
								break;
							case 'CATALOG_LENGTH':
								$arFields['LENGTH'] = $Value;
								break;
							case 'CATALOG_WEIGHT':
								$arFields['WEIGHT'] = $Value;
								break;
							case 'CATALOG_MEASURE':
								$arFields['MEASURE'] = $Value;
								break;
						}
						if (CCatalogProduct::Add($arFields)) {
							CWDA::Log('Set '.$Target.'='.$Value.' for element#'.$ElementID);
							$bResult = true;
						} else {
							CWDA::Log('Error update '.$Target.' for element #'.$ElementID.', fields: '.print_r($arFields,1));
						}
					} elseif ($Target=='CATALOG_MEASURE_RATIO') {
						$resRatio = CCatalogMeasureRatio::GetList(array(),array('PRODUCT_ID'=>$ElementID));
						if ($arRatio = $resRatio->GetNext(false,false)) {
							if (CCatalogMeasureRatio::Update($arRatio['ID'],array('RATIO'=>$Value))) {
								$bResult = true;
							}
						} else {
							if (CCatalogMeasureRatio::Add(array('PRODUCT_ID'=>$ElementID,'RATIO'=>$Value))) {
								$bResult = true;
							}
						}
					} elseif (preg_match('#^CATALOG_STORE_(\d+)$#',$Target,$M)) {
						if ($bCatalogModule && class_exists('CCatalogStore')) {
							$Value = $ValueSingle;
							$StoreID = $M[1];
							if (CWDA::SetProductStoreQuantity($ElementID, $StoreID, $Value)) {
								$bResult = true;
							}
						}
					}
				}
			} elseif (preg_match('#^SEO_([\w\d_]+)$#',$Target,$M)) {
				$Value = $ValueSingle;
				if (!CWDA::IsUtf()) {
					$Value = CWDA::ConvertCharset($Value);
				}
				$arFields = array(
					'IPROPERTY_TEMPLATES' => array(
						$M[1] => $Value
					),
				);
				if ($IBlockElement->Update($ElementID,$arFields)) {
					CWDA::Log('Updated element #'.$ElementID.', fields: '.print_r($arFields,1));
					$bResult = true;
				} else {
					CWDA::Log('Error update element #'.$ElementID.', ['.$IBlockElement->LAST_ERROR.'] fields: '.print_r($arFields,1));
				}
			}
		}
		self::IncCounter($Params);
		return $bResult;
	}
}
/*
ToDo:
- Files (for fields and properties)
*/
?>