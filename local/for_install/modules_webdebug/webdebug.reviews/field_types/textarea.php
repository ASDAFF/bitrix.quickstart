<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews_FieldTypes_TextArea extends CWD_Reviews2_FieldTypes_All {
	CONST CODE = 'TEXTAREA';
	CONST NAME = '��������� �������';
	CONST SORT = '120';
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
			'ERROR_VALUE_EMPTY' => '�� ������� �������� ���� "%s"',
			'ERROR_VALUE_SHORT' => '�������� ���� "%s" ������ ���� �� ����� %d %s',
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
			'COLS' => '����� ��������',
			'COLS_HINT' => '������� (� ������ �������������) �������������� ������ ���� - �.�. ���������� ��������, ������� � ���� ����� ��� ���������.',
			'ROWS' => '����� �����',
			'ROWS_HINT' => '������� (� ������ �������������) ������������ ������ ���� - �.�. ���������� �����, ������� ������������ ������������ � ����.',
			'MAXLENGTH' => '����������� ����',
			'MAXLENGTH_HINT' => '����� �� ������ ������� ������������ ������ (maxlength) ���� - �.�. ������������ ���������� ��������, ������� ����� �������� � ����. ������ ����� ��� ������� ���� ����� ����� �� �������� � ��������� ���������.',
			'DEFAULT_VALUE' => '�������� �� ���������',
			'DEFAULT_VALUE_HINT' => '������� ��������, ������� ����� �������� �� ���������.',
			'IS_REVIEW' => '������������ ��� ����� ������',
			'IS_REVIEW_HINT' => '������ ����� ���������, ��� � ������ ���� ������������ ����� ������ (��� �����������) - ���� ����� ����� ������������ � e-mail ������������ � ���� ������.',
			'HEADER_VISUAL_EDITOR' => '���������� ��������',
			'USE_VISUAL_EDITOR' => '������������ ���������� ��������',
			'USE_VISUAL_EDITOR_HINT' => '� ������� ������ ����� �� ������ ��������� �������, ������� ������� ������ ��� ��������, �������� ���������� ����������, �.�. ����������, � ������� ����� ����������� ��������������.',
			'VISUAL_EDITOR_HEIGHT' => '��������� ������ ����������� ���������',
			'VISUAL_EDITOR_HEIGHT_HINT' => '������� ����� ������ ����������� ���������. ��������, "200" (���� ������� ����� ��� ������� ��������� - ���������, ��� ��� �������� � ��������), ��� "200px" (��� �������),  ������ �����, ��� ���������� ���������� ������ ���������� ��������� ����� "�����" ����. � ������, ���� ���� �������� �������� ������, ������ ����������� ��������� ����� ����������� �������������, ������� � ������������ ������� (1 ������).',
			'VISUAL_EDITOR_WIDTH' => '������ ����������� ���������',
			'VISUAL_EDITOR_WIDTH_HINT' => '������� ����� ������ ����������� ���������. ��������, "320" (���� ������� ����� ��� ������� ��������� - ���������, ��� ��� �������� � ��������), "320px", ��� "80%" (��� �������). � ������, ���� �������� �� ������ - ���������� �������� ����� �������� ��� ��������������� ��� ������.',
			'HEADER_VALUE_CHECK' => '�������� ��������� ������',
			'CHECK_MIN_LENGTH' => '����������� �����',
			'CHECK_MIN_LENGTH_HINT' => '� ������� ������ ����� �� ������ ������������ �������� ������������ �������� �� ����������� �����.',
			'CHECK_REGEXP' => '���������� ���������',
			'CHECK_REGEXP_HINT' => '� ������� ������ ����� �� ������ ������������ �������� ������������ �������� �� ����������� ���������, ��������, ���� ���������� ������ 6 ����: #^\d{6}$#',
			'ERROR_MESSAGE' => '��������� �� ������ ��� ������������� ������������ ����',
			'ERROR_MESSAGE_HINT' => '����� �� ������ ������� ���������, ������� ������������ � ������, ���� ������ ���� �������� ��� ������������, �� �� ��������� �������������.',
			'EDITOR_BOLD' => '������',
			'EDITOR_ITALIC' => '������',
			'EDITOR_UNDERLINE' => '�����������',
			'EDITOR_LEFT' => '������������ �����',
			'EDITOR_CENTER' => '������������ �� ������',
			'EDITOR_RIGHT' => '������������ ������',
			'EDITOR_JUSTIFY' => '������������ �� ������',
			'EDITOR_OL' => '������������ ������',
			'EDITOR_UL' => '������������� ������',
			'EDITOR_SUBSCRIPT' => '������ ������',
			'EDITOR_SUPERSCRIPT' => '������� ������',
			'EDITOR_STRIKETHROUGH' => '�����������',
			'EDITOR_REMOVEFORMAT' => '������� ��������������',
			'EDITOR_INDENT' => '��������� ������',
			'EDITOR_OUTDENT' => '��������� ������',
			'EDITOR_HR' => '�������������� �����',
			'EDITOR_HR' => '������ ������',
			'EDITOR_FONTFAMILY' => '�����',
			'EDITOR_LINK' => '�������� ������',
			'EDITOR_UNLINK' => '������� ������',
			'EDITOR_FORECOLOR' => '���� ������',
			'EDITOR_BGCOLOR' => '���� ����',
			'EDITOR_SAVE' => '���������',
			'EDITOR_ADDLINKTITLE' => '��������/������� ������',
			'EDITOR_ADDLINKURL' => '�����: ',
			'EDITOR_ADDLINKTEXT' => '���������: ',
			'EDITOR_SUBMIT' => '���������',
			'EDITOR_ERROR_EMPTY_URL' => '������� ����� ������',
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
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_CLASS_HINT'));?> <?=self::GetMessage('CSS_CLASS');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_class]" value="<?=htmlspecialcharsbx($arSavedValues['css_class']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_ID_HINT'));?> <?=self::GetMessage('CSS_ID');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_id]" value="<?=htmlspecialcharsbx($arSavedValues['css_id']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CSS_STYLE_HINT'));?> <?=self::GetMessage('CSS_STYLE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[css_style]" value="<?=htmlspecialcharsbx($arSavedValues['css_style']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ATTRIBUTES_HINT'));?> <?=self::GetMessage('ATTRIBUTES');?>:</label>
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
								<label for=""><?WDR2_ShowHint(self::GetMessage('COLS_HINT'));?> <?=self::GetMessage('COLS');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[cols]" value="<?=htmlspecialcharsbx($arSavedValues['cols']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ROWS_HINT'));?> <?=self::GetMessage('ROWS');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[rows]" value="<?=htmlspecialcharsbx($arSavedValues['rows']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('MAXLENGTH_HINT'));?> <?=self::GetMessage('MAXLENGTH');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[maxlength]" value="<?=htmlspecialcharsbx($arSavedValues['maxlength']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('DEFAULT_VALUE_HINT'));?> <?=self::GetMessage('DEFAULT_VALUE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<textarea name="data[default_value]" rows="4" cols="60" style="width:92%"><?=htmlspecialcharsbx($arSavedValues['default_value']);?></textarea>
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('IS_REVIEW_HINT'));?> <?=self::GetMessage('IS_REVIEW')?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[is_review]" value="Y"<?if($arSavedValues['is_review']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_VISUAL_EDITOR');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('USE_VISUAL_EDITOR_HINT'));?> <?=self::GetMessage('USE_VISUAL_EDITOR');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[use_visual_editor]" value="Y"<?if($arSavedValues['use_visual_editor']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('VISUAL_EDITOR_HEIGHT_HINT'));?> <?=self::GetMessage('VISUAL_EDITOR_HEIGHT');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[visual_editor_height]" value="<?=$arSavedValues['visual_editor_height'];?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('VISUAL_EDITOR_WIDTH_HINT'));?> <?=self::GetMessage('VISUAL_EDITOR_WIDTH');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[visual_editor_width]" value="<?=$arSavedValues['visual_editor_width'];?>" style="width:92%" />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_VALUE_CHECK');?></td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CHECK_MIN_LENGTH_HINT'));?> <?=self::GetMessage('CHECK_MIN_LENGTH');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[check_min_length]" value="<?=htmlspecialcharsbx($arSavedValues['check_min_length']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('CHECK_REGEXP_HINT'));?> <?=self::GetMessage('CHECK_REGEXP');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[check_regexp]" value="<?=htmlspecialcharsbx($arSavedValues['check_regexp']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ERROR_MESSAGE_HINT'));?> <?=self::GetMessage('ERROR_MESSAGE');?>:</label>
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
			$Value = $arParams['default_value'];
		}
		$UniqID = rand(100000000,999999999);
		if ($arParams['use_visual_editor']=='Y') {
			if (trim($arParams['css_id'])=='') {
				$arParams['css_id'] = 'wd_reviews2_'.$UniqID;
			}
			if(!is_numeric($arParams['visual_editor_height']) && !preg_match('#^(\d+)px$#',$arParams['visual_editor_height'])) {
				$arParams['visual_editor_height'] = false;
			} elseif(is_numeric($arParams['visual_editor_height'])) {
				$arParams['visual_editor_height'] .= 'px';
			}
			if(!is_numeric($arParams['visual_editor_width']) && !preg_match('#^(\d+)%$#',$arParams['visual_editor_width']) && !preg_match('#^(\d+)px$#',$arParams['visual_editor_width'])) {
				$arParams['visual_editor_width'] = false;
			} elseif (is_numeric($arParams['visual_editor_width'])) {
				$arParams['visual_editor_width'] .= 'px';
			}
		}
		ob_start();
		?>
		<textarea
			name="<?=$InputName;?>[<?=$arFields['CODE'];?>]"
			style="overflow:auto; resize:vertical; <?if(strlen($arParams['css_style'])):?><?=$arParams['css_style'];?><?endif?>"
			<?if(strlen($arParams['cols'])):?>cols="<?=$arParams['cols'];?>"<?endif?>
			<?if(strlen($arParams['rows'])):?>rows="<?=$arParams['rows'];?>"<?endif?>
			<?if(strlen($arParams['maxlength'])):?>maxlength="<?=$arParams['maxlength'];?>"<?endif?>
			<?if(strlen($arParams['css_class'])):?>class="<?=$arParams['css_class'];?>"<?endif?>
			<?if(strlen($arParams['css_id'])):?>id="<?=$arParams['css_id'];?>"<?endif?>
			<?if(strlen($arParams['attr'])):?> <?=$arParams['attr'];?><?endif?>
		><?=htmlspecialcharsbx($Value);?></textarea>
		<?if($arParams['use_visual_editor']=='Y'):?>
			<?$HtmlEditorID = $arParams['css_id'].'_editor';?>
			<?if($arParams['visual_editor_width']!==false):?><div id="<?=$HtmlEditorID;?>_wrapper" style="width:<?=$arParams['visual_editor_width']?>"><?endif?>
			<div id="<?=$HtmlEditorID;?>"<?if($arParams['visual_editor_height']!==false):?> style="height:<?=$arParams['visual_editor_height']?>;"<?endif?>></div>
			<?if($arParams['visual_editor_width']!==false):?></div><?endif?>
			<script type="text/javascript">
			var WDR2_NicEdit = {
				'bold': '<?=self::GetMessage('EDITOR_BOLD');?>',
				'italic': '<?=self::GetMessage('EDITOR_ITALIC');?>',
				'underline': '<?=self::GetMessage('EDITOR_UNDERLINE');?>',
				'left': '<?=self::GetMessage('EDITOR_LEFT');?>',
				'center': '<?=self::GetMessage('EDITOR_CENTER');?>',
				'right': '<?=self::GetMessage('EDITOR_RIGHT');?>',
				'justify': '<?=self::GetMessage('EDITOR_JUSTIFY');?>',
				'ol': '<?=self::GetMessage('EDITOR_OL');?>',
				'ul': '<?=self::GetMessage('EDITOR_UL');?>',
				'subscript': '<?=self::GetMessage('EDITOR_SUBSCRIPT');?>',
				'superscript': '<?=self::GetMessage('EDITOR_SUPERSCRIPT');?>',
				'strikethrough': '<?=self::GetMessage('EDITOR_STRIKETHROUGH');?>',
				'removeformat': '<?=self::GetMessage('EDITOR_REMOVEFORMAT');?>',
				'indent': '<?=self::GetMessage('EDITOR_INDENT');?>',
				'outdent': '<?=self::GetMessage('EDITOR_OUTDENT');?>',
				'hr': '<?=self::GetMessage('EDITOR_HR');?>',
				'fontSize': '<?=self::GetMessage('EDITOR_HR');?>',
				'fontFamily': '<?=self::GetMessage('EDITOR_FONTFAMILY');?>',
				'link': '<?=self::GetMessage('EDITOR_LINK');?>',
				'unlink': '<?=self::GetMessage('EDITOR_UNLINK');?>',
				'forecolor': '<?=self::GetMessage('EDITOR_FORECOLOR');?>',
				'bgcolor': '<?=self::GetMessage('EDITOR_BGCOLOR');?>',
				'save': '<?=self::GetMessage('EDITOR_SAVE');?>',
				'addLinkTitle': '<?=self::GetMessage('EDITOR_ADDLINKTITLE');?>',
				'addLinkUrl': '<?=self::GetMessage('EDITOR_ADDLINKURL');?>',
				'addLinkText': '<?=self::GetMessage('EDITOR_ADDLINKTEXT');?>',
				'errorUrlEmpty': '<?=self::GetMessage('EDITOR_ERROR_EMPTY_URL');?>',
				'submit': '<?=self::GetMessage('EDITOR_SUBMIT');?>'
			};
			</script>
			<script type="text/javascript" src="/bitrix/js/webdebug.reviews/nicEdit.js"></script>
			<script type="text/javascript">
			if (window.wdr2_visual_editor_init_<?=$arParams['css_id'];?>==undefined) {
				document.getElementById('<?=$HtmlEditorID;?>').innerHTML = document.getElementById('<?=$arParams['css_id'];?>').value;
				document.getElementById('<?=$arParams['css_id'];?>').style.display = 'none';
				//alert('<?=$arParams['css_id'];?>_html');
				new nicEditor({
					fullPanel : true,
					id: '<?=$arParams['css_id'];?>_html'
				}).panelInstance('<?=$HtmlEditorID;?>',{hasPanel : true});
				var wdr2_Html_<?=$arParams['css_id'];?> = document.getElementById("<?=$arParams['css_id'];?>_html");
				var wdr2_Textarea_<?=$arParams['css_id'];?> = document.getElementById("<?=$arParams['css_id'];?>");
				function WDR2_AddEventHandler_<?=$UniqID;?>(Element, Type, Handler) {
					if (Element.addEventListener){
						Element.addEventListener(Type, Handler, false)
					} else {
						Element.attachEvent("on"+Type, Handler)
					}
				}
				WDR2_AddEventHandler_<?=$UniqID;?>(wdr2_Html_<?=$arParams['css_id'];?>, 'blur', function WDR2_UpdateTextarea_<?=$UniqID;?>() {
					wdr2_Textarea_<?=$arParams['css_id'];?>.value = wdr2_Html_<?=$arParams['css_id'];?>.innerHTML;
				});
				window.wdr2_visual_editor_init_<?=$arParams['css_id'];?> = true;
			}
			</script>
		<?endif?>
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
		$NewValue = str_replace(' target=""','',$NewValue);
		$NewValue = str_replace(' title=""','',$NewValue);
		$NewValue = CWD_Reviews2::ProtectText($NewValue);
		return $NewValue;
	}
	
	function GetValue($Value, $arField) {
		return $Value;
	}
	
	function GetDisplayValue($Value, $arField) {
		$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
		$Value = preg_replace('#(<a.*?>.*?</a>)#is'.$u,'<!--noindex-->$1<!--/noindex-->',$Value);
		return $Value;
	}
	
	function GetNotifyValue($Value, $arField) {
		$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
		$Value = preg_replace('#(<a.*?>.*?</a>)#is'.$u,'<!--noindex-->$1<!--/noindex-->',$Value);
		return $Value;
	}
}

?>