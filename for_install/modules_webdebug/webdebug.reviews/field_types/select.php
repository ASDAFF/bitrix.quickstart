<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews_FieldTypes_Select extends CWD_Reviews2_FieldTypes_All {
	CONST CODE = 'SELECT';
	CONST NAME = '������';
	CONST SORT = '130';
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
			'COL_PARAM' => '��������',
			'COL_VALUE' => '��������',
			'COL_SORT' => '����.',
			'COL_DELETE' => '�������',
			'SELECT_ROW_DELETE' => '�������',
			'SELECT_ROW_ADD' => '��������',
			'HEADER_CSS_HTML' => 'CSS / HTML',
			'CSS_CLASS' => 'CSS-�����',
			'CSS_CLASS_HINT' => 'CSS-�����, ����������� ������� �������� �����. ��������, ������� TEST ����� ���������� class="TEST".',
			'CSS_ID' => 'CSS-�������������',
			'CSS_ID_HINT' => 'CSS-�������������, ����������� ������� �������� �����. ��������, ������� TEST ����� ���������� id="TEST".',
			'CSS_STYLE' => 'CSS-�����',
			'CSS_STYLE_HINT' => 'CSS-�����, ����������� ������� �������� �����. ����������� ����� �� ��������� ������ (������, ������, ������������, �������, �����, ������� � ��). ����������� ����� � ����������� ����� (����, ����������� � ��), �� �� ����������� ������� �� ��������.',
			'ATTRIBUTES' => '�������������� ��������',
			'ATTRIBUTES_HINT' => '�������������� ��������, ����������� ������� �������� �����. ��������, ������ �������: data-title="TEST" autocomplete="off".',
			'HEADER_ADDITIONAL_SETTINGS' => '�������������� ���������',
			'SIZE' => '���������� �����',
			'SIZE_HINT' => '���������� ������� ����� � ������. ���� ��������� ������ ���������� ������, �������� ��� ���� ������, ��� ������� 1. � ������, ���� ���������� ����� ����� 1, ���������� ������ �������� ������� ������� ������.',
			'MULTIPLE' => '��������� ������������� �����',
			'MULTIPLE_HINT' => '��������� �������� ��������� ������� ������������. ������������ ����������� ��������.',
			'ADD_EMPTY_OPTION' => '��������� ������ ������',
			'ADD_EMPTY_OPTION_HINT' => '�������� �����, ���� ���������� ����������� �������� ������ ��������.',
			'HEADER_SOURCE' => '������ ����� ������ ������',
			'SOURCE' => '�������� ������',
			'SOURCE_HINT' => '�������, ������ ������� ����� ������ ������. ������� "�� ��������� ���������" ���� ����������� ��������� ������ �� �������, ��������, ������ � ��. ������� "�� �������� ���� "������" ���� ����������� ������� ��� �������� �������� �������� "������".',
			'VALUES_CUSTOM' => '������ �������',
			'VALUES_IBLOCK_ELEMENTS' => '�� ��������� ���������',
			'VALUES_IBLOCK_ENUMS' => '�� �������� ���� "������"',
			'IBLOCK_ID' => 'ID ���������',
			'IBLOCK_ID_HINT' => '�������� ��������, �� �������� ���������� ��������� ������ �� ��������� ���������� ��� � ��������� �������� ���� "������".',
			'SECTION_ID' => 'ID ������� (�������������)',
			'SECTION_ID_HINT' => '����� �� ������ ������� �������� ID �������, �� �������� ����� ���������� ��������. �� ������ �������� �������� ������� �� �����.',
			'INCLUDE_SUBSECTIONS' => '������� ����������',
			'INCLUDE_SUBSECTIONS_HINT' => '�������� ������ �����, ���� ����������, ����� ���������� ����� �������� �� ����������� ������������ ���������� �������.',
			'PROPERTY_ID' => 'ID ��������',
			'PROPERTY_ID_HINT' => '������� ����� �������� ID �������� ���� "������". �������� ID �� ������ ������ �� �������� �������������� ���������� ���������, �� ������� "��������", �������� ���������� ��������.',
			'VALUES_VALUE' => '��� �������� ����� ������������ � �������� <b>��������</b> ������ � ������. �������� �� ������������ �� �����, �� ������������ � ���� �����.',
			'VALUES_TITLE' => '��� �������� ����� ������������ � �������� <b>��������</b> ������ � ������.',
			'VALUES_SORT' => '������� ����� ������� ���������� - ����� �����. ��� ������ �� ����� ������ ����� �������� � ������� ����������� ���� ��������.',
			'DEFAULT_VALUE' => '�������� �� ���������',
			'DEFAULT_VALUE_HINT' => '������� ��������, ������� ����� �������� �� ���������. ��� ������� � ����������� �� ���������� ��������� ������:<ul><li><b>������ ������ ������� �������</b> - ������� �������� ������� ������ �� ������� "��������",</li><li><b>������ ������ ������� �� ��������� ���������</b> - ������� ID �������� ���������, ������� ������ ���� ������ �� ���������.</li><li><b>������ ������ ������� �� �������� ���� "������"</b> - ������� ID �������� ������ �� �������� �������.</li></ul>',
			'HEADER_VALUE_CHECK' => '�������� ��������� ������',
			'ERROR_MESSAGE' => '��������� �� ������ ��� ������������� ������������ ����',
			'ERROR_MESSAGE_HINT' => '����� �� ������ ������� ���������, ������� ������������ � ������, ���� ������ ���� �������� ��� ������������, �� �� ��������� �������������.',
		);
		return self::_GetMessage($arMess[$Item], $Values);
	}
	function TransformArray($arData, $DesignMode=false) {
		$arResult = array();
		if (is_array($arData['enum_code'])) {
			foreach ($arData['enum_code'] as $Key => $Code) {
				$Name = $arData['enum_name'][$Key];
				$Sort = $arData['enum_sort'][$Key];
				if ($DesignMode) {
					$arResult[] = array(
						'CODE' => $Code,
						'NAME' => $Name,
						'SORT' => $Sort,
					);
				} else {
					$arResult[$Code] = $Name;
				}
			}
		}
		if ($DesignMode && empty($arResult)) {
			$arResult[] = false;
		} elseif (!$DesignMode && !empty($arResult)) {
			foreach($arResult as $Key => $Value) {
				unset($arResult[$Key]);
				break;
			}
		}
		return $arResult;
	}
	function ShowSettings($arSavedValues) {
		ob_start();
		$arItems = self::TransformArray($arSavedValues, true);
		$arItems = array_merge(array(),$arItems);
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
								<label for=""><?WDR2_ShowHint(self::GetMessage('SIZE_HINT'));?> <?=self::GetMessage('SIZE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[size]" value="<?=htmlspecialcharsbx($arSavedValues['size']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('MULTIPLE_HINT'));?> <?=self::GetMessage('MULTIPLE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[multiple]" value="Y"<?if($arSavedValues['multiple']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_SOURCE');?></td>
						</tr>
						<tr class="adm-list-table-row wd_reviews2_sel_source wd_reviews2_sel_source_elem">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('ADD_EMPTY_OPTION_HINT'));?> <?=self::GetMessage('ADD_EMPTY_OPTION');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[empty_option]" value="Y"<?if($arSavedValues['empty_option']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('SOURCE_HINT'));?> <?=self::GetMessage('SOURCE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<select name="data[source_type]" id="wd_reviews2_type_select_source_type_switcher">
									<option value="cust"<?if($arSavedValues['source_type']=='cust'):?> selected="selected"<?endif?>><?=self::GetMessage('VALUES_CUSTOM');?></option>
									<option value="elem"<?if($arSavedValues['source_type']=='elem'):?> selected="selected"<?endif?>><?=self::GetMessage('VALUES_IBLOCK_ELEMENTS');?></option>
									<option value="prop"<?if($arSavedValues['source_type']=='prop'):?> selected="selected"<?endif?>><?=self::GetMessage('VALUES_IBLOCK_ENUMS');?></option>
								</select>
								<script>
								$('#wd_reviews2_type_select_source_type_switcher').change(function(){
									$('.wd_reviews2_sel_source').hide();
									$('.wd_reviews2_sel_source_'+$(this).val()).show();
								}).change();
								</script>
							</td>
						</tr>
						<tr class="adm-list-table-row wd_reviews2_sel_source wd_reviews2_sel_source_elem wd_reviews2_sel_source_prop">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('IBLOCK_ID_HINT'));?> <?=self::GetMessage('IBLOCK_ID');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<?$arIBlocks = self::GetIBlockList();?>
								<select name="data[iblock_id]">
									<?foreach($arIBlocks as $IBlockTypeID => $arIBlockType):?>
										<optgroup label="[<?=$IBlockTypeID;?>] <?=$arIBlockType["NAME"]?>">
											<?foreach($arIBlockType["ITEMS"] as $arIBlock):?>
												<option value="<?=$arIBlock["ID"]?>"<?if($arIBlock["ID"]==$arSavedValues['iblock_id']):?> selected="selected"<?endif?>>[<?=$arIBlock["ID"]?>] <?=$arIBlock["NAME"]?></option>
											<?endforeach?>
										</optgroup>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<tr class="adm-list-table-row wd_reviews2_sel_source wd_reviews2_sel_source_elem">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('SECTION_ID_HINT'));?> <?=self::GetMessage('SECTION_ID');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[section_id]" value="<?=htmlspecialcharsbx($arSavedValues['section_id']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row wd_reviews2_sel_source wd_reviews2_sel_source_elem">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('INCLUDE_SUBSECTIONS_HINT'));?> <?=self::GetMessage('INCLUDE_SUBSECTIONS');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="checkbox" name="data[with_subfolders]" value="Y"<?if($arSavedValues['with_subfolders']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row wd_reviews2_sel_source wd_reviews2_sel_source_prop">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('PROPERTY_ID_HINT'));?> <?=self::GetMessage('PROPERTY_ID');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[property_id]" value="<?=htmlspecialcharsbx($arSavedValues['property_id']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="adm-list-table-row1 wd_reviews2_sel_source wd_reviews2_sel_source_cust">
							<td class="adm-list-table-cell" colspan="2">
								
								<style>
								#wd_reviews2_settings_selectbox .adm-list-table-cell {padding-left:12px; padding-right:12px;}
								#wd_reviews2_settings_selectbox input[type=text] {width:100%; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; box-sizing:border-box;}
								</style>
								<div id="wd_reviews2_settings_selectbox">
									<table class="adm-list-table">
										<tbody>
											<tr class="adm-list-table-header">
												<td class="adm-list-table-cell" style="width:200px;">
													<div class="adm-list-table-cell-inner" style="white-space:nowrap"><?=self::GetMessage('COL_PARAM');?><?WDR2_ShowHint(self::GetMessage('VALUES_VALUE'));?></div>
												</td>
												<td class="adm-list-table-cell">
													<div class="adm-list-table-cell-inner" style="white-space:nowrap"><?=self::GetMessage('COL_VALUE');?><?WDR2_ShowHint(self::GetMessage('VALUES_TITLE'));?></div>
												</td>
												<td class="adm-list-table-cell" style="width:80px;">
													<div class="adm-list-table-cell-inner" style="white-space:nowrap"><?=self::GetMessage('COL_SORT');?><?WDR2_ShowHint(self::GetMessage('VALUES_SORT'));?></div>
												</td>
												<td class="adm-list-table-cell" style="width:80px;">
													<div class="adm-list-table-cell-inner"><?=self::GetMessage('COL_DELETE');?></div>
												</td>
											</tr>
											<?$MainRow=true?>
											<?foreach($arItems as $Key => $arOption):?>
												<tr class="adm-list-table-row"<?if($MainRow):?> data-main="Y" style="display:none"<?endif?>>
													<td class="adm-list-table-cell">
														<input type="text" name="data[enum_code][]" value="<?=($MainRow?'':$arOption['CODE'])?>" />
													</td>
													<td class="adm-list-table-cell">
														<input type="text" name="data[enum_name][]" value="<?=($MainRow?'':$arOption['NAME'])?>" />
													</td>
													<td class="adm-list-table-cell">
														<input type="text" name="data[enum_sort][]" value="<?=($MainRow?'100':$arOption['SORT'])?>" class="sort" />
													</td>
													<td class="adm-list-table-cell align-center">
														<input type="button" value="<?=self::GetMessage('SELECT_ROW_DELETE');?>" onclick="WD_Reviews2_Selectbox_DeleteRow(this);" />
													</td>
												</tr>
												<?$MainRow=false;?>
											<?endforeach?>
										</tbody>
									</table>
									<script>
									// Adding row
									function WD_Reviews2_Selectbox_AddRow() {
										var NewRow = $('#wd_reviews2_settings_selectbox tr.adm-list-table-row[data-main=Y]').clone().removeAttr('data-main').css('display','');
										NewRow.appendTo($('#wd_reviews2_settings_selectbox tbody')).find('input[type=text]').not('.sort').val('');
									}
									// Delete row
									function WD_Reviews2_Selectbox_DeleteRow(Sender) {
										var Row = $(Sender).parents('tr').eq(0);
										if (Row.attr('data-main')!='Y') {
											Row.remove();
										}
									}
									</script>
									<hr/>
									<div>
										<input type="button" value="<?=self::GetMessage('SELECT_ROW_ADD');?>" onclick="WD_Reviews2_Selectbox_AddRow();" />
									</div>
									<hr/>
								</div>
								
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right">
								<label for=""><?WDR2_ShowHint(self::GetMessage('DEFAULT_VALUE_HINT'));?> <?=self::GetMessage('DEFAULT_VALUE');?>:</label>
							</td>
							<td class="adm-list-table-cell">
								<input type="text" name="data[default_value]" value="<?=htmlspecialcharsbx($arSavedValues['default_value']);?>" style="width:92%" />
							</td>
						</tr>
						<tr class="heading">
							<td colspan="2"><?=self::GetMessage('HEADER_VALUE_CHECK');?></td>
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
	function GetItems($arParams) {
		if($arParams['source_type']=='elem' && $arParams['iblock_id']>0) {
			return self::GetIBlockElements($arParams['iblock_id'], $arParams['section_id'], $arParams['with_subfolders']=='Y');
		} elseif ($arParams['source_type']=='prop' && $arParams['iblock_id']>0 && $arParams['property_id']>0) {
			return self::GetIBlockEnums($arParams['iblock_id'], $arParams['property_id']);
		} else {
			return self::TransformArray($arParams, false);
		}
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
		$arItems = self::GetItems($arParams);
		ob_start();
		?>
		<select 
			name="<?=$InputName;?>[<?=$arFields['CODE'];?>]<?if($arParams['multiple']=='Y'):?>[]<?endif?>"
			<?if(strlen($arParams['size'])):?>size="<?=$arParams['size'];?>"<?endif?>
			<?if($arParams['multiple']=='Y'):?>multiple="multiple"<?endif?>
			<?if(strlen($arParams['css_class'])):?>class="<?=$arParams['css_class'];?>"<?endif?>
			<?if(strlen($arParams['css_id'])):?>id="<?=$arParams['css_id'];?>"<?endif?>
			<?if(strlen($arParams['css_style'])):?>style="<?=$arParams['css_style'];?>"<?endif?>
			<?if(strlen($arParams['attr'])):?> <?=$arParams['attr'];?><?endif?>
		>
			<?if($arParams['empty_option']=='Y' && $arParams['multiple']!='Y'):?><option value=""></option><?endif?>
			<?foreach($arItems as $Key => $Name):?>
				<option value="<?=$Key;?>"<?if((is_array($Value)&&in_array($Key,$Value)) || (!is_array($Value)&&$Key==$Value)):?> selected="selected"<?endif?>><?=$Name;?></option>
			<?endforeach?>
		</select>
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
		if ($bReq && $Value=='') {
			return strlen($arParams['error_message']) ? $arParams['error_message'] : self::GetMessage('ERROR_VALUE_EMPTY', array($arFields['NAME']));
		}
		return false;
	}	
	function GetIBlockElements($IBlockID, $SectionID=false, $WithSubFolders=false) {
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
			$arSorter = array('SORT'=>'ASC','NAME'=>'ASC');
			$arFilter = array('IBLOCK_ID'=>$IBlockID);
			if ($SectionID>0) {
				$arFilter['SECTION_ID'] = $SectionID;
			}
			if ($WithSubFolders) {
				$arFilter['INCLUDE_SUBSECTIONS'] = $SectionID;
			}
			$resItems = CIBlockElement::GetList($arSorter, $arFilter, false, false, array('ID','NAME'));
			while ($arItem = $resItems->GetNext(false,false)) {
				$arResult['E_'.$arItem['ID']] = $arItem['NAME'];
			}
		}
		return $arResult;
	}
	function GetIBlockList() {
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
				$resIBlockTypes = CIBlockType::GetList(array(),array());
				while ($arIBlockType = $resIBlockTypes->GetNext(false,false)) {
					$arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType['ID'], LANGUAGE_ID, false);
					$arResult[$arIBlockType['ID']] = array(
						'NAME' => $arIBlockTypeLang['NAME'],
						'ITEMS' => array(),
					);
				}
			$arFilter = array();
			$resIBlock = CIBlock::GetList(array('SORT'=>'ASC'),$arFilter);
			while ($arIBlock = $resIBlock->GetNext(false,false)) {
				$arResult[$arIBlock['IBLOCK_TYPE_ID']]['ITEMS'][] = $arIBlock;
			}
		}
		return $arResult;
	}
	
	function GetIBlockEnums($IBlockID, $PropertyID) {
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
			$arSorter = array('SORT'=>'ASC','VALUE'=>'ASC');
			$arFilter = array('IBLOCK_ID'=>$IBlockID,'PROPERTY_ID'=>$PropertyID);
			$resItems = CIBlockPropertyEnum::GetList($arSorter, $arFilter);
			while ($arItem = $resItems->GetNext(false,false)) {
				$arResult['P_'.$arItem['ID']] = $arItem['VALUE'];
			}
		}
		return $arResult;
	}
	
	function GetValue($Value, $arField) {
		$arItems = self::GetItems($arField['PARAMS']);
		return $arItems[$Value];
	}
	
	function GetDisplayValue($Value, $arField) {
		$arItems = self::TransformArray($arField['PARAMS'], false);
		return $arItems[$Value];
	}
	
	function GetNotifyValue($Value, $arField) {
		$arItems = self::TransformArray($arField['PARAMS'], false);
		return $arItems[$Value];
	}
}

?>