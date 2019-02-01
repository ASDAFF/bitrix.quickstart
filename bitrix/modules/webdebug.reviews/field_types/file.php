<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews_FieldTypes_File extends CWD_Reviews2_FieldTypes_All {
	CONST CODE = 'FILE';
	CONST NAME = 'Файл';
	CONST SORT = '150';
	function GetName() {
		$Name = self::NAME;
		if (CWD_Reviews2::IsUtf8()) {
			$Name = $GLOBALS['APPLICATION']->ConvertCharset($Name, 'CP1251', 'UTF-8');
		}
		return $Name;
	}
	function GetCode() {
		return self::CODE;
	}
	function GetSort() {
		return self::SORT;
	}
	function GetMessage($Item, $Values=false) {
		$arMess = array(
			'OPTION_PARAM' => 'Параметр',
			'OPTION_VALUE' => 'Значение',
			'ERROR_VALUE_EMPTY' => 'Не указано значение поля "%s"',
			'HEADER_CSS_HTML' => 'CSS / HTML',
			'CSS_CLASS' => 'CSS-класс',
			'CSS_CLASS_HINT' => 'CSS-класс, добавляемый данному элементу форму. Например, укажите TEST чтобы получилось class="TEST".',
			'CSS_ID' => 'CSS-идентификатор',
			'CSS_ID_HINT' => 'CSS-идентификатор, добавляемый данному элементу форму. Например, укажите TEST чтобы получилось id="TEST".',
			'CSS_STYLE' => 'CSS-стиль',
			'CSS_STYLE_HINT' => 'CSS-стиль, добавляемый данному элементу формы. Допускаются любые из возможныз стилей (жирный, курсив, подчеркнутый, отступы, цвета, границы и др). Допускаются также и современные стили (тени, закругления и др), но их отображение зависит от браузера. Имейте ввиду, что данный элемент управления практически не поддается стилистической настройке стандартными способами.',
			'ATTRIBUTES' => 'Доп. атрибуты',
			'ATTRIBUTES_HINT' => 'Дополнительные атрибуты, добавляемые данному элементу формы. Например, можете указать: data-title="TEST" autocomplete="off".',
			'HEADER_ADDITIONAL_SETTINGS' => 'Дополнительные настройки',
			'IMAGES_ONLY' => 'Разрешить загрузку только изображений',
			'IMAGES_ONLY_HINT' => 'С помощью данной опции Вы можете разрешить пользователям загружать исключительно картинки (формат JPG, PNG, GIF, BMP).',
			'SHOW_UPLOAD_MAX_SIZE' => 'Показывать информацию о максимальном размера файла',
			'SHOW_UPLOAD_MAX_SIZE_HINT' => 'Отметьте опцию, чтобы показывать пользователям информацию о максимально возможном размере загружаемого файла (определяется из настроек сервера).',
			'STYLIZE' => 'Стилизация',
			'STYLIZE_HINT' => 'Отметьте эту опцию, если необходимо использовать нестандартный внешний вид элемента управления для ввода файла. С помощью отдельных CSS-стилей есть возможность полностью управлять внешним видом данного поля.',
			'HEADER_VALUE_CHECK' => 'Проверка введенных данных',
			'ERROR_MESSAGE' => 'Сообщение об ошибке при незаполненном обязательном поле',
			'ERROR_MESSAGE_HINT' => 'Здесь Вы можете указать сообщение, которое отображается в случае, если данное поле отмечено как обязательное, но не заполнено пользователем.',
			'UPLOAD_MAX_SIZE' => 'Максимальный размер файла: <b>%s</b>',
		);
		return self::_GetMessage($arMess[$Item], $Values);
	}
	function ShowSettings($arSavedValues) {
		ob_start();
		?>
			<div id="wd_reviews2_settings_field_type_text">
				<table class="adm-list-table">
					<tbody>
						<tr class="adm-list-table-header">
							<td class="adm-list-table-cell align-left" style="width:40%;">
								<?=self::GetMessage('OPTION_PARAM');?>
							</td>
							<td class="adm-list-table-cell align-left">
								<?=self::GetMessage('OPTION_VALUE');?>
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_CSS_HTML');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_CLASS_HINT'));?> <?=self::GetMessage('CSS_CLASS')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_class]" value="<?=htmlspecialcharsbx($arSavedValues['css_class']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_ID_HINT'));?> <?=self::GetMessage('CSS_ID')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_id]" value="<?=htmlspecialcharsbx($arSavedValues['css_id']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_STYLE_HINT'));?> <?=self::GetMessage('CSS_STYLE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_style]" value="<?=htmlspecialcharsbx($arSavedValues['css_style']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ATTRIBUTES_HINT'));?> <?=self::GetMessage('ATTRIBUTES')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[attr]" value="<?=htmlspecialcharsbx($arSavedValues['attr']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_ADDITIONAL_SETTINGS');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('IMAGES_ONLY_HINT'));?> <?=self::GetMessage('IMAGES_ONLY')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[images_only]" value="Y"<?if($arSavedValues['images_only']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('SHOW_UPLOAD_MAX_SIZE_HINT'));?> <?=self::GetMessage('SHOW_UPLOAD_MAX_SIZE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[show_upload_max_size]" value="Y"<?if($arSavedValues['show_upload_max_size']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('STYLIZE_HINT'));?> <?=self::GetMessage('STYLIZE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[stylize]" value="Y"<?if($arSavedValues['stylize']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_VALUE_CHECK');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ERROR_MESSAGE_HINT'));?> <?=self::GetMessage('ERROR_MESSAGE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[error_message]" value="<?=htmlspecialcharsbx($arSavedValues['error_message']);?>" style="width:92%" />
							</td>
						</tr>
					</tbody>
				</table>
				<hr/>
			</div>
		<?
		return ob_get_clean();
	}
	function Show($Value, $arFields, $InputName=false) {
		$arParams = $arFields['PARAMS'];
		if (!is_array($arParams)) {
			$arParams = array();
		}
		if ($InputName==false) {
			$InputName = COption::GetOptionString(self::ModuleID, 'form_field_name');
		}
		$arFile = false;
		if ($Value>0) {
			$arFile = CFile::GetFileArray($Value);
		}
		ob_start();
		?>
		<style>
		#wd_reviews2_save_file_<?=$arParams['css_id'];?> {background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABj1BMVEUAAAAhcMkhcMkiTIwkTIkhcMmLtOOt4/4iTIwmVJv3+v31+Pzn7PXj6fPq7/b3+/74+/7v9Prs7vHk6O3f5e5siLP2+v32+v4iTIwmVJrX3ufh4+bk5+ns8vlqiLTx9vyewukjTIkpUpLM1ePd4un2+Pvz+P1qh7To8PoiTIt+qdkjTIkiTIsqUpDL1uTj6/jz9/3u9Pzj7fnh7Pnf6vcmS4RxodSt4v0kS4cmS4NmnNOp3vdxtOVvsOBxuvBxufFute9qse1lrOpgpudbn+JTktBOi8tMis1cmtqp3fZxsuVxt+9xt/ButO5rse1mrOphp+hboeNSk9FOjcxIis1FjdhQlNpKgLxJgLxIf7xGfrtEfLpBebk/drY8b6s5bKc2a6gybLEva7JFj9lKfrvL2+32+fvl6Ori5ecsarE9jNhJfLr3+v5hZ29hZ25eY2peY2n2+f0qabE1idhJernj8v3h8PvT4OnQ3eYoaLIvhtkAAABIeLcmaLIBAQEknvEjdb72+fzq7O8jdb0mmOVJWo2kAAAABXRSTlMAv0+/wXOC0tEAAAAJcEhZcwAAAEgAAABIAEbJaz4AAADwSURBVBjTY2BkBQI2KGBlYmBgZefg5OTi5uHl4+YX4ACKgAUEhYRFRDnExDnYJcACklLSMrJyHPLyHAqKIAFJJWUVVTV1Dg0NTS1toICOkq6evgGXoZGxiakZUIW5haWpJgcIaJpaWgHNsLaxtbN3cHRydnF1c/fgYGD19PL28fXzDwgMCg4JDQMKWIdHREZFx8TGxSckJiUDBcxTUgUEBNLSM9LTUjOzgALs2Tm5uXn5Bfl5uYVFxSCBkpzSsvKK8rLS0sLKKqAt1TU5EPnc3MLaOqA76hsahZqEGgVAoLlFm4GZQ0JRW1EC7DIgiwUA1JMynwrXF0YAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTMtMDUtMjlUMDk6NTM6MjctMDU6MDAPPNZZAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDEzLTA1LTI4VDAxOjA3OjU4LTA1OjAwDjtZ0QAAAABJRU5ErkJggg==); background-repeat:no-repeat; height:16px; line-height:16px; margin-left:10px; padding-left:22px;}
		#wd_reviews2_save_file_<?=$arParams['css_id'];?>:hover {text-decoration:none;}
		</style>
		<table style="border-collapse:collapse; width:auto;">
			<tbody>
				<tr>
					<td style="padding:0; vertical-align:middle;">
						<?if($arParams['stylize']=='Y'):?><label class="file_design_wrapper" id="file_<?=$arParams['css_id']?>_wrapper"><span class="panel1" id="file_<?=$arParams['css_id']?>_panel1"></span><span class="panel2" id="file_<?=$arParams['css_id']?>_panel2"></span><?endif?>
						<input
							type="file"
							name="F_<?=$arFields['CODE'];?>"
							<?if(strlen($arParams['css_class'])):?>class="<?=$arParams['css_class'];?>"<?endif?>
							<?if(strlen($arParams['css_id'])):?>id="<?=$arParams['css_id'];?>"<?endif?>
							<?if(strlen($arParams['css_style'])):?>style="<?=$arParams['css_style'];?>"<?endif?>
							<?if(strlen($arParams['attr'])):?> <?=$arParams['attr'];?><?endif?>
							<?if($arParams['images_only']=='Y'):?>accept="image/*"<?endif?>
							<?if($arParams['stylize']=='Y'):?>onchange="wdr2_input_file_changes_<?=$arParams['css_id']?>(this);"<?endif?>
						/>
						<?if($arParams['stylize']=='Y'):?></label><script type="text/javascript">function wdr2_input_file_changes_<?=$arParams['css_id']?>(Input){document.getElementById('file_<?=$arParams['css_id']?>_panel1').innerHTML = Input.value.match(/[^\/\\]+$/);};</script><?endif?>
						<input type="hidden" name="<?=$InputName;?>[<?=$arFields['CODE'];?>]" value="F_<?=$arFields['CODE'];?>" />
					</td>
					<td style="padding:0; vertical-align:middle;">
						<?if(is_array($arFile)):?><a href="<?=$arFile['SRC'];?>" target="_blank" id="wd_reviews2_save_file_<?=$arParams['css_id'];?>"><?=$arFile['ORIGINAL_NAME'];?> (<?=CFile::FormatSize($arFile['FILE_SIZE']);?>)</a><?endif?>
						<?if($arParams['show_upload_max_size']=='Y' && (!defined('ADMIN_SECTION') || ADMIN_SECTION!==true)):?>
							<div class="wd_reviews2_max_upload_size" style="color:#999; padding-left:20px;"><small><?=sprintf(self::GetMessage('UPLOAD_MAX_SIZE'),self::FileUploadMaxSize())?></small>.</div>
						<?endif?>
					</td>
				</tr>
			</tbody>
		</table>
		<?
		$HTML = ob_get_clean();
		return $HTML;
	}
	function CheckFieldError($arFields, $Value) {
		$arParams = $arFields['PARAMS'];
		$bReq = $arFields['REQUIRED']=='Y';
		if (!is_array($arParams)) {
			$arParams = array();
		}
		if ($bReq && (!isset($_FILES[$Value]) || !is_array($_FILES[$Value]) || $_FILES[$Value]['error']!=0)) {
			return strlen($arParams['error_message']) ? $arParams['error_message'] : self::GetMessage('ERROR_VALUE_EMPTY', array($arFields['NAME']));
		}
		return false;
	}
	function SaveValue($Code, $OldValue, $NewValue, $Operation=false) {
		if ($Code!='' && strlen($NewValue) && !is_numeric($NewValue) && is_array($_FILES[$NewValue]) && $_FILES[$NewValue]['error']===0) {
			$arFile = $_FILES[$NewValue];
			$arFile['MODULE_ID'] = CWD_Reviews2::ModuleID;
			$NewValue = CFile::SaveFile($arFile,CWD_Reviews2::ModuleID.'/files');
			if ($NewValue>0) {
				if ($OldValue>0) {
					CFile::Delete($OldValue);
				}
				return $NewValue;
			}
		}
		if ($OldValue===null) {
			return '';
		}
		return $OldValue;
	}

	
	/**
	 *	Get upload max filesize
	 */
	function FileUploadMaxSize() {
		static $MaxSize = -1;
		if ($MaxSize < 0) {
			$MaxSize = self::ParsePhpSize(ini_get('post_max_size'));
			$UploadMax = self::ParsePhpSize(ini_get('upload_max_filesize'));
			if ($UploadMax > 0 && $UploadMax < $MaxSize) {
				$MaxSize = $UploadMax;
			}
		}
		return CFile::FormatSize($MaxSize);
	}
	function ParsePhpSize($Size) {
		$Unit = preg_replace('/[^bkmgtpezy]/i', '', $Size);
		$Size = preg_replace('/[^0-9\.]/', '', $Size);
		if ($Unit) {
			return round($Size * pow(1024, stripos('bkmgtpezy', $Unit[0])));
		} else {
			return round($Size);
		}
	}
	
	function DeleteValue($Code, $Value) {
		if ($Value>0) {
			CFile::Delete($Value);
		}
	}
	
	function GetValue($Value, $arField) {
		if ($Value>0) {
			$arFile = CFile::GetFileArray($Value);
			if (is_array($arFile)) {
				$Size = CFile::FormatSize($arFile['FILE_SIZE']);
				$Link = "<a href=\"{$arFile['SRC']}\" target=\"_blank\" title=\"{$Size}\">{$arFile['ORIGINAL_NAME']}</a>";
				return $Link;
			}
		}
		return '';
	}
	
	function GetDisplayValue($Value, $arField) {
		return self::GetValue($Value, $arField);
	}
	
	function GetNotifyValue($Value, $arField) {
		if ($Value>0) {
			$arFile = CFile::GetFileArray($Value);
			if (is_array($arFile)) {
				return $arFile['SRC'];
			}
		}
		return '';
	}
	
}

?>