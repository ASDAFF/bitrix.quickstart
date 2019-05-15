<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews_FieldTypes_CheckBox extends CWD_Reviews2_FieldTypes_All {
	CONST CODE = 'CHECKBOX';
	CONST NAME = 'Флажок';
	CONST SORT = '140';
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
			'ERROR_NOT_CHECKED' => 'Необходимо отметить поле "%s"',
			'HEADER_CSS_HTML' => 'CSS / HTML',
			'CSS_CLASS' => 'CSS-класс',
			'CSS_CLASS_HINT' => 'CSS-класс, добавляемый данному элементу форму. Например, укажите TEST чтобы получилось class="TEST".',
			'CSS_ID' => 'CSS-идентификатор',
			'CSS_ID_HINT' => 'CSS-идентификатор, добавляемый данному элементу форму. Например, укажите TEST чтобы получилось id="TEST".',
			'CSS_STYLE' => 'CSS-стиль',
			'CSS_STYLE_HINT' => 'CSS-стиль, добавляемый данному элементу формы. Допускаются любые из возможныз стилей (жирный, курсив, подчеркнутый, отступы, цвета, границы и др). Допускаются также и современные стили (тени, закругления и др), но их отображение зависит от браузера.',
			'ATTRIBUTES' => 'Дополнительные атрибуты',
			'ATTRIBUTES_HINT' => 'Дополнительные атрибуты, добавляемые данному элементу формы. Например, можете указать: data-title="TEST" autocomplete="off".',
			'HEADER_ADDITIONAL_SETTINGS' => 'Дополнительные настройки',
			'CHECKED_BY_DEFAULT' => 'Отмечено по умолчанию',
			'CHECKED_BY_DEFAULT_HINT' => 'Отметьте опцию, если необходимо, чтобы по умолчанию галочка была отмечена.',
			'SHOW_TEXT' => 'Отображать название галочки',
			'SHOW_TEXT_HINT' => 'Данная опция позволяет выводить название галочки рядом с ней.',
			'HEADER_VALUE_CHECK' => 'Проверка введенных данных',
			'ERROR_MESSAGE' => 'Сообщение об ошибке при незаполненном обязательном поле',
			'ERROR_MESSAGE_HINT' => 'Здесь Вы можете указать сообщение, которое отображается в случае, если данное поле отмечено как обязательное, но не заполнено пользователем.',
			'Y' => 'Да',
			'N' => 'Нет',
		);
		return self::_GetMessage($arMess[$Item], $Values);
	}
	function ShowSettings($arSavedValues) {
		ob_start();
		?>
			<div id="wd_reviews2_settings_field_type_checkbox">
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
								<label for=""><?WDR2_ShowHint(self::GetMessage('CHECKED_BY_DEFAULT_HINT'));?> <?=self::GetMessage('CHECKED_BY_DEFAULT')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[checked_by_default]" value="Y"<?if($arSavedValues['checked_by_default']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('SHOW_TEXT_HINT'));?> <?=self::GetMessage('SHOW_TEXT')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[show_name]" value="Y"<?if($arSavedValues['show_name']=='Y'):?> checked="checked"<?endif?> />
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
		if (defined('ADMIN_SECTION') && ADMIN_SECTION===true) {
			$arParams['show_name'] = 'N';
		}
		if ($arParams['show_name']=='Y') {
			if (stripos($arParams['css_style'],'vertical-align')===false) {
				$arParams['css_style'] = 'vertical-align:middle; '.$arParams['css_style'];
			}
		}
		ob_start();
		?>
		<?if($arParams['show_name']=='Y'):?><label style="vertical-align:middle"><?endif?>
		<input
			type="checkbox"
			name="<?=$InputName;?>[<?=$arFields['CODE'];?>]"
			value="Y"
			<?if(strlen($arParams['css_class'])):?>class="<?=$arParams['css_class'];?>"<?endif?>
			<?if(strlen($arParams['css_id'])):?>id="<?=$arParams['css_id'];?>"<?endif?>
			<?if(strlen($arParams['css_style'])):?>style="<?=$arParams['css_style'];?>"<?endif?>
			<?if(strlen($arParams['attr'])):?> <?=$arParams['attr'];?><?endif?>
			<?if($Value=='Y' || ($Value!='N' && $arParams['checked_by_default']=='Y' && $_REQUEST['checkbox_'.ToLower(MD5($InputName.$arFields['CODE']))]!='Y')):?>checked="checked"<?endif?>
		/>
		<?if($arParams['show_name']=='Y'):?> <?=$arFields['NAME'];?></label><?endif?>
		<input type="hidden" name="checkbox_<?=ToLower(MD5($InputName.$arFields['CODE']));?>" value="Y" />
		<?
		$HTML = ob_get_clean();
		return $HTML;
	}
	function CheckFieldError($arFields, $Value) {
		$arParams = $arFields['PARAMS'];
		$bReq = $arFields['REQUIRED']=='Y';
		$Value = trim($Value);
		if (!is_array($arParams)) {
			$arParams = array();
		}
		if ($bReq && $Value!='Y') {
			return strlen($arParams['error_message']) ? $arParams['error_message'] : self::GetMessage('ERROR_NOT_CHECKED', array($arFields['NAME']));
		}
		return false;
	}
	
	function SaveValue($Code, $OldValue, $NewValue, $Operation=false) {
		if ($NewValue=='Y') {
			return 'Y';
		}
		return 'N';
	}
	
	function GetValue($Value, $arField) {
		return ($Value=='Y' ? self::GetMessage('Y') : self::GetMessage('N'));
	}
	
	function GetDisplayValue($Value, $arField) {
		return self::GetValue($Value, $arField);
	}
	
	function GetNotifyValue($Value, $arField) {
		return self::GetValue($Value, $arField);
	}
}

?>