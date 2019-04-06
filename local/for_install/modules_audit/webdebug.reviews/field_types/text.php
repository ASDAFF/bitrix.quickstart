<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews_FieldTypes_Text extends CWD_Reviews2_FieldTypes_All {
	CONST CODE = 'TEXT';
	CONST NAME = '��������� ����';
	CONST SORT = '110';
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
			'OPTION_PARAM' => '��������',
			'OPTION_VALUE' => '��������',
			'ERROR_EMAIL_EMPTY' => 'E-mail �� ������',
			'ERROR_EMAIL_WRONG' => '����������� ����� E-mail',
			'ERROR_VALUE_EMPTY' => '�� ������� �������� ���� "%s"',
			'ERROR_VALUE_SHORT' => '�������� ���� "%s" ������ ���� �� ����� %d %s',
			'ERROR_NOT_NUMERIC' => '�������� ���� "%s" ����� ��������� ������ �����',
			'ERROR_VALUE_NOT_MATCHED' => '�������� ���� "%s" ������� �������',
			'SYMBOL_LENGTH_1' => '�������',
			'SYMBOL_LENGTH_2' => '��������',
			'HEADER_CSS_HTML' => 'CSS / HTML',
			'CSS_CLASS' => 'CSS-�����',
			'CSS_CLASS_HINT' => 'CSS-�����, ����������� ������� �������� �����. ��������, ������� TEST ����� ���������� class="TEST".',
			'CSS_ID' => 'CSS-�������������',
			'CSS_ID_HINT' => 'CSS-�������������, ����������� ������� �������� �����. ��������, ������� TEST ����� ���������� id="TEST".',
			'CSS_STYLE' => 'CSS-�����',
			'CSS_STYLE_HINT' => 'CSS-�����, ����������� ������� �������� �����. ����������� ����� �� ��������� ������ (������, ������, ������������, �������, �����, ������� � ��). ����������� ����� � ����������� ����� (����, ����������� � ��), �� �� ����������� ������� �� ��������.',
			'ATTRIBUTES' => '���. ��������',
			'ATTRIBUTES_HINT' => '�������������� ��������, ����������� ������� �������� �����. ��������, ������ �������: data-title="TEST" autocomplete="off".',
			'HEADER_ADDITIONAL_SETTINGS' => '�������������� ���������',
			'SIZE' => '������ ����',
			'SIZE_HINT' => '������� (� ������ �������������) ������ ���� - �.�. ���������� ��������, ������� � ���� ����� ��� ���������.',
			'MAXLENGTH' => '����������� ����',
			'MAXLENGTH_HINT' => '����� �� ������ ������� ������������ ������ (maxlength) ���� - �.�. ������������ ���������� ��������, ������� ����� �������� � ����.',
			'DEFAULT_VALUE' => '�������� �� ���������',
			'DEFAULT_VALUE_HINT' => '������� ��������, ������� ����� �������� �� ���������.',
			'AUTO_FILL' => '������������� ���������',
			'AUTO_FILL_HINT' => '������ ����� ��������� �������� ��-���������. � ������� ���� ����� �� ������ ������� ����������� ������������ ������ � �����, ����� ������������ �� ���� ������������� ��������� ����, ������� ������� � ��� �������.',
			'AUTO_FILL_NO' => '--- �� ��������� ������������� ---',
			'IS_NAME' => '������������ ��� ��� ������������',
			'IS_NAME_HINT' => '������ ����� ���������, ��� � ������ ���� ������������ ��������� ���� ���.',
			'IS_EMAIL' => '������������ ��� e-mail',
			'IS_EMAIL_HINT' => '������ ����� �������� ����� "e-mail" ��� ����, ��������� ���� �������� �� ����� ���� ����� ��������� ������� e-mail ������������ ��� �������� e-mail �����������. � ���� ������ ����������� ������������ �������� e-mail.',
			'HEADER_VALUE_CHECK' => '�������� ��������� ������ (�� ������ �� ���� e-mail)',
			'CHECK_MIN_LENGTH' => '����������� �����',
			'CHECK_MIN_LENGTH_HINT' => '� ������� ������ ����� �� ������ ������������ �������� ������������ �������� �� ����������� �����.',
			'CHECK_REGEXP' => '���������� ���������',
			'CHECK_REGEXP_HINT' => '� ������� ������ ����� �� ������ ������������ �������� ������������ �������� �� ����������� ���������, ��������, ���� ���������� ������ 6 ����: #^\d{6}$#',
			'DIGITS_ONLY' => '��������� ���� ������ ����',
			'DIGITS_ONLY_HINT' => '� ������� ������ ����� � ������ ��������� ������� � ���� ������ �����.',
			'ERROR_MESSAGE' => '��������� �� ������ ��� ������������� ������������ ����',
			'ERROR_MESSAGE_HINT' => '����� �� ������ ������� ���������, ������� ������������ � ������, ���� ������ ���� �������� ��� ������������, �� �� ��������� �������������.',
			'AUTO_FILL_TITLE' => '���������',
			'AUTO_FILL_NAME' => '���',
			'AUTO_FILL_LASTNAME' => '�������',
			'AUTO_FILL_SECONDNAME' => '��������',
			'AUTO_FILL_NAME_FULL' => '�.�.�.',
			'AUTO_FILL_NAME_LASTNAME' => '��� � �������',
			'AUTO_FILL_LASTNAME_NAME' => '������� � ���',
			'AUTO_FILL_NAME_SECONDNAME' => '��� � ��������',
			'AUTO_FILL_PHONE' => '������� (��������� ��� ��������)',
			'AUTO_FILL_PHONE_HOME' => '������� (��������)',
			'AUTO_FILL_PHONE_MOBILE' => '������� (���������)',
			'AUTO_FILL_LOGIN' => '�����',
			'AUTO_FILL_EMAIL' => 'E-mail',
			'AUTO_FILL_PROFESSION' => '���������',
			'AUTO_FILL_WWW' => 'WWW-��������',
			'AUTO_FILL_ICQ' => 'ICQ',
			'AUTO_FILL_GENDER' => '���',
			'AUTO_FILL_BIRTHDAY' => '���� ��������',
			'AUTO_FILL_CITY' => '�����',
			'GENDER_M' => '�������',
			'GENDER_F' => '�������',
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
								<label for=""><?WDR2_ShowHint(self::GetMessage('SIZE_HINT'));?> <?=self::GetMessage('SIZE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[size]" value="<?=htmlspecialcharsbx($arSavedValues['size']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('MAXLENGTH_HINT'));?> <?=self::GetMessage('MAXLENGTH')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[maxlength]" value="<?=htmlspecialcharsbx($arSavedValues['maxlength']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('DEFAULT_VALUE_HINT'));?> <?=self::GetMessage('DEFAULT_VALUE')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[default_value]" value="<?=htmlspecialcharsbx($arSavedValues['default_value']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('AUTO_FILL_HINT'));?> <?=self::GetMessage('AUTO_FILL')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<?
								$arAutoFill = array(
									'title' => '',
									'name' => '',
									'lastname' => '',
									'secondname' => '',
									'name_full' => '',
									'name_lastname' => '',
									'lastname_name' => '',
									'name_secondname' => '',
									'phone' => '',
									'phone_home' => '',
									'phone_mobile' => '',
									'login' => '',
									'email' => '',
									'profession' => '',
									'www' => '',
									'icq' => '',
									'gender' => '',
									'birthday' => '',
									'city' => '',
								);
								foreach($arAutoFill as $Key => $Value) {
									$arAutoFill[$Key] = self::GetMessage('AUTO_FILL_'.ToUpper($Key));
								}
								?>
								<select name="data[auto_fill]">
									<option value=""><?=self::GetMessage('AUTO_FILL_NO');?></option>
									<?foreach($arAutoFill as $Key => $Value):?>
										<option value="<?=$Key;?>"<?if($Key==$arSavedValues['auto_fill']):?> selected="selected"<?endif?>><?=$Value;?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('IS_NAME_HINT'));?> <?=self::GetMessage('IS_NAME')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[is_name]" value="Y"<?if($arSavedValues['is_name']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('IS_EMAIL_HINT'));?> <?=self::GetMessage('IS_EMAIL')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[is_email]" value="Y"<?if($arSavedValues['is_email']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_VALUE_CHECK');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CHECK_MIN_LENGTH_HINT'));?> <?=self::GetMessage('CHECK_MIN_LENGTH')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[check_min_length]" value="<?=htmlspecialcharsbx($arSavedValues['check_min_length']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CHECK_REGEXP_HINT'));?> <?=self::GetMessage('CHECK_REGEXP')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[check_regexp]" value="<?=htmlspecialcharsbx($arSavedValues['check_regexp']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('DIGITS_ONLY_HINT'));?> <?=self::GetMessage('DIGITS_ONLY')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[digits_only]" value="Y"<?if($arSavedValues['digits_only']=='Y'):?> checked="checked"<?endif?> />
							</td>
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
		if ($Value===null || $Value===false) {
			if (trim($arFields['PARAMS']['auto_fill'])!='') {
				$Value = self::GetDefaultValue($arFields['PARAMS']['auto_fill']);
			} elseif (trim($arFields['PARAMS']['default_value'])!='') {
				$Value = $arFields['PARAMS']['default_value'];
			}
		}
		ob_start();
		?>
		<input
			type="text"
			name="<?=$InputName;?>[<?=$arFields['CODE'];?>]"
			value="<?=htmlspecialcharsbx($Value);?>"
			<?if(strlen($arParams['size'])):?>size="<?=$arParams['size'];?>"<?endif?>
			<?if(strlen($arParams['maxlength'])):?>maxlength="<?=$arParams['maxlength'];?>"<?endif?>
			<?if(strlen($arParams['css_class'])):?>class="<?=$arParams['css_class'];?>"<?endif?>
			<?if(strlen($arParams['css_id'])):?>id="<?=$arParams['css_id'];?>"<?endif?>
			<?if(strlen($arParams['css_style'])):?>style="<?=$arParams['css_style'];?>"<?endif?>
			<?if(strlen($arParams['attr'])):?> <?=$arParams['attr'];?><?endif?>
			<?if($arParams['is_email']=='Y'):?>data-email="Y"<?endif?>
			<?if($arParams['digits_only']=='Y'):?>onkeypress="wd_reviews2_digits_input_validate_digits(event)"<?endif?>
		/>
		<?if(strlen($arParams['digits_only'])):?>
			<script type="text/javascript">function wd_reviews2_digits_input_validate_digits(a){a=a||window.event;var b=a.keyCode||a.which,b=String.fromCharCode(b);/[0-9]|\./.test(b)||(a.returnValue=!1,a.preventDefault&&a.preventDefault())};</script>
		<?endif?>
		<?
		$HTML = ob_get_clean();
		return $HTML;
	}
	
	function GetDefaultValue($AutoFill) {
		global $USER;
		if ($USER->IsAuthorized()) {
			$UserID = $USER->GetID();
			if ($UserID>0) {
				$resUser = CUser::GetList($By='ID',$Order='ASC',array('ID'=>$UserID));
				if ($arUser = $resUser->GetNext(false,false)) {
					foreach($arUser as $Key => $Value) {
						$arUser[$Key] = trim($Value);
					}
					$strResult = '';
					switch($AutoFill) {
						case 'title':
							$strResult = $arUser['TITLE'];
							break;
						case 'name':
							$strResult = $arUser['NAME'];
							break;
						case 'lastname':
							$strResult = $arUser['LAST_NAME'];
							break;
						case 'secondname':
							$strResult = $arUser['SECOND_NAME'];
							break;
						case 'name_full':
							$strResult = $arUser['LAST_NAME'].' '.$arUser['NAME'].' '.$arUser['SECOND_NAME'];
							break;
						case 'name_lastname':
							$strResult = $arUser['NAME'].' '.$arUser['LAST_NAME'];
							break;
						case 'lastname_name':
							$strResult = $arUser['LAST_NAME'].' '.$arUser['NAME'];
							break;
						case 'name_secondname':
							$strResult = $arUser['NAME'].' '.$arUser['SECOND_NAME'];
							break;
						case 'phone':
							$strResult = strlen($arUser['PERSONAL_MOBILE']) ? $arUser['PERSONAL_MOBILE'] : $arUser['PERSONAL_PHONE'];
							break;
						case 'phone_home':
							$strResult = $arUser['PERSONAL_PHONE'];
							break;
						case 'phone_mobile':
							$strResult = $arUser['PERSONAL_MOBILE'];
							break;
						case 'login':
							$strResult = $arUser['LOGIN'];
							break;
						case 'email':
							$strResult = $arUser['EMAIL'];
							break;
						case 'profession':
							$strResult = $arUser['PERSONAL_PROFESSION'];
							break;
						case 'www':
							$strResult = $arUser['PERSONAL_WWW'];
							break;
						case 'icq':
							$strResult = $arUser['PERSONAL_ICQ'];
							break;
						case 'gender':
							$strResult = $arUser['PERSONAL_GENDER']=='M' ? self::GetMessage('GENDER_M') : ($arUser['PERSONAL_GENDER']=='F' ? self::GetMessage('GENDER_F') : '');
							break;
						case 'birthday':
							$strResult = $arUser['PERSONAL_BIRTHDAY'];
							break;
						case 'city':
							$strResult = $arUser['PERSONAL_CITY'];
							break;
					}
					return $strResult;
				}
			}
		}
		return '';
	}
	
	function CheckFieldError($arFields, $Value) {
		$arParams = $arFields['PARAMS'];
		$bReq = $arFields['REQUIRED']=='Y';
		$Value = trim($Value);
		if (!is_array($arParams)) {
			$arParams = array();
		}
		if ($arParams['is_email']=='Y') {
			if ($bReq && $Value=='') {
				return self::GetMessage('ERROR_EMAIL_EMPTY');
			} elseif ($Value!='' && !check_email($Value)) {
				return self::GetMessage('ERROR_EMAIL_WRONG');
			}
		}
		if ($arParams['digits_only']=='Y') {
			if ($bReq && $Value=='') {
				return self::GetMessage('ERROR_VALUE_EMPTY', array($arFields['NAME']));
			} elseif (strlen($Value)>0 && !is_numeric($Value)) {
				return self::GetMessage('ERROR_NOT_NUMERIC', array($arFields['NAME']));
			}
		}
		if ($bReq) {
			$arParams['check_min_length'] = IntVal($arParams['check_min_length']);
			if ($Value=='') {
				return strlen($arParams['error_message']) ? $arParams['error_message'] : self::GetMessage('ERROR_VALUE_EMPTY', array($arFields['NAME']));
			} elseif ($arParams['check_min_length']>0 && strlen($Value)<$arParams['check_min_length']) {
				$SymbolWord = CWD_Reviews2::WordForm($arParams['check_min_length'],array(
					'1' => self::GetMessage('SYMBOL_LENGTH_1'),
					'2' => self::GetMessage('SYMBOL_LENGTH_2'),
					'5' => self::GetMessage('SYMBOL_LENGTH_2'),
				));
				return self::GetMessage('ERROR_VALUE_SHORT', array($arFields['NAME'],$arParams['check_min_length'],$SymbolWord));
			} elseif (strlen($arParams['check_regexp'])>0) {
				if (!preg_match($arParams['check_regexp'],$Value)) {
					return self::GetMessage('ERROR_VALUE_NOT_MATCHED', array($arFields['NAME']));
				}
			}
		}
		return false;
	}
	
	function SaveValue($Code, $OldValue, $NewValue, $Operation=false) {
		return CWD_Reviews2::ProtectText($NewValue);
	}
	
	function GetValue($Value, $arField) {
		if ($arField['PARAMS']['is_email']=='Y' && check_email(trim($Value))) {
			$Value = trim($Value);
			$Value = "<a href=\"mailto:{$Value}\">{$Value}</a>";
		}
		return $Value;
	}
	
	function GetDisplayValue($Value, $arField) {
		if ($arField['PARAMS']['is_email']=='Y' && check_email(trim($Value))) {
			$Value = trim($Value);
			$Value = "<a href=\"mailto:{$Value}\">{$Value}</a>";
		} else {
			$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
			$Value = preg_replace('#(<a.*?>.*?</a>)#is'.$u,'<!--noindex-->$1<!--/noindex-->',$Value);
		}
		return $Value;
	}
	
	function GetNotifyValue($Value, $arField) {
		return $Value;
	}
}

?>