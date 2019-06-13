<?
IncludeModuleLangFile(__FILE__);

if (IntVal($WD_Reviews2_InterfaceID)<=0) {
	return;
}

$WD_Reviews2_FieldID = IntVal($WD_Reviews2_FieldID);
$FieldMode = $WD_Reviews2_FieldID>0 ? 'edit' : 'add';

if ($FieldMode=='edit') {
	$resFields = CWD_Reviews2_Fields::GetByID($WD_Reviews2_FieldID);
	$arFields = $resFields->GetNext();
}
if (!is_array($arFields)) {
	$arFields = array(
		'SORT' => '100',
	);
}
$arFieldTypes = WDR2_GetFieldTypes();

if (webdebug_reviews_demo_expired()) {
	webdebug_pageprops_show_demo();
	die();
}

$arTabs = array(
	array('DIV' => 'subtab1', 'TAB' => GetMessage('WD_REVIEWS2_TAB_GENERAL_NAME'), 'TITLE' => GetMessage('WD_REVIEWS2_TAB_GENERAL_DESC')),
	array('DIV' => 'subtab2', 'TAB' => GetMessage('WD_REVIEWS2_TAB_TYPE_NAME'), 'TITLE' => GetMessage('WD_REVIEWS2_TAB_TYPE_DESC')),
);

$TabControl = new CAdminViewTabControl('WD_Reviews2_Field_edit', $arTabs);
?>

<div style="display:none"><iframe src="" name="wd_reviews2_field_edit_iframe" id="wd_reviews2_field_edit_iframe"></iframe></div>
<form action="/bitrix/admin/wd_reviews2_fields.php?action=save&interface=<?=$WD_Reviews2_InterfaceID;?>&field=<?=$WD_Reviews2_FieldID;?>&lang=<?=LANGUAGE_ID;?>" method="post" id="wd_reviews2_field_edit_form" enctype="multipart/form-data" target="wd_reviews2_field_edit_iframe">
	<?$TabControl->Begin();?>
	<?$TabControl->BeginNextTab();?>
		<table class="adm-detail-content-table edit-table wd_reviews2_field_edit_table" id="wd_reviews2_field_edit_table_1">
			<tbody>
				<tr id="tr_name">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_NAME_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_NAME');?>:</td>
					<td class="field-data">
						<input type="text" name="fields[NAME]" value="<?=$arFields['NAME'];?>" size="60" maxlength="255" style="width:90%" data-required="Y" />
					</td>
				</tr>
				<tr id="tr_code">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_CODE_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_CODE');?>:</td>
					<td class="field-data">
						<input type="text" name="fields[CODE]" value="<?=$arFields['CODE'];?>" size="60" maxlength="255" style="width:90%" data-required="Y" />
					</td>
				</tr>
				<tr id="tr_sort">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_SORT_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_SORT');?>:</td>
					<td class="field-data">
						<input type="text" name="fields[SORT]" value="<?=$arFields['SORT'];?>" size="10" maxlength="10" style="width:90%" />
					</td>
				</tr>
				<tr id="tr_description">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_DESCRIPTION_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_DESCRIPTION');?>:</td>
					<td class="field-data">
						<textarea name="fields[DESCRIPTION]" cols="60" rows="2" style="overflow:auto; resize:vertical; width:90%;"><?=$arFields['DESCRIPTION'];?></textarea>
					</td>
				</tr>
				<tr id="tr_required">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_REQUIRED_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_REQUIRED');?>:</td>
					<td class="field-data">
						<input type="checkbox" name="fields[REQUIRED]" id="wdr2_field_required" value="Y"<?if($arFields['REQUIRED']=='Y'):?> checked="checked"<?endif?> />
						<script>BX.adminFormTools.modifyCheckbox(document.getElementById('wdr2_field_required'));</script>
					</td>
				</tr>
				<tr id="tr_required">
					<td class="field-name" width="40%"><?=WDR2_ShowHint(GetMessage('WD_REVIEWS2_FIELD_HIDDEN_HINT'));?> <?=GetMessage('WD_REVIEWS2_FIELD_HIDDEN');?>:</td>
					<td class="field-data">
						<input type="checkbox" name="fields[HIDDEN]" id="wdr2_field_hidden" value="Y"<?if($arFields['HIDDEN']=='Y'):?> checked="checked"<?endif?> />
						<script>BX.adminFormTools.modifyCheckbox(document.getElementById('wdr2_field_hidden'));</script>
					</td>
				</tr>
			</tbody>
		</table>
	<?$TabControl->BeginNextTab();?>
		<table class="adm-detail-content-table edit-table wd_reviews2_field_edit_table" id="wd_reviews2_field_edit_table_2">
			<tbody>
				<tr id="tr_type">
					<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_FIELD_TYPE');?>:</td>
					<td class="field-data">
						<select name="fields[TYPE]" id="wd_reviews2_select_field_type">
							<?foreach($arFieldTypes as $arFieldType):?>
								<option value="<?=$arFieldType['CODE'];?>"<?if($arFields['TYPE']==$arFieldType['CODE']):?> selected="selected"<?endif?>>[<?=$arFieldType['CODE'];?>] <?=$arFieldType['NAME'];?></option>
							<?endforeach?>
						</select>
						<script>
						$('#wd_reviews2_select_field_type').change(function(){
							var TypeID = $('#wd_reviews2_select_field_type').val();
							jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_fields.php?&action=type&interface=<?=$WD_Reviews2_InterfaceID;?>&field=<?=$WD_Reviews2_FieldID;?>&type='+TypeID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
								$('#wd_reviews2_field_data').html(HTML);
								$('#wd_reviews2_field_data input[type=checkbox]').each(function(){
									if ($(this).attr('id')==undefined) {
										$(this).attr('id','wdr2_field_'+Math.random());
									}
									BX.adminFormTools.modifyCheckbox(document.getElementById($(this).attr('id')));
								});
							});
						}).change();
						</script>
					</td>
				</tr>
				<tr id="tr_params">
					<td class="field-data" colspan="2">
						<div id="wd_reviews2_field_data"></div>
					</td>
				</tr>
			</tbody>
		</table>
	<?$TabControl->End();?>
	<div style="display:none"><input type="submit" value="" /></div>
</form>
<script>
$('#wd_reviews2_field_edit_form').find('input[data-required=Y]').parents('tr').find('td.field-name').addClass('adm-required-field');
$('#wd_reviews2_field_edit_form').submit(function(Event){
	var CancelSubmit = false;
	$(this).find('input[data-required=Y]').each(function(){
		var Value = $.trim($(this).val());
		if (Value) {
			$(this).removeClass('wd_reviews2_error_field');
		} else {
			$(this).addClass('wd_reviews2_error_field');
			CancelSubmit = true;
		}
	});
	if (CancelSubmit) {
		var Clicked = false;
		$('#subtab1,#subtab2').each(function(){
			if ($(this).find('.wd_reviews2_error_field').size()>0 && !Clicked) {
				$('#view_tab_' + $(this).attr('id')).click();
				Clicked = true;
			}
		});
		Event.preventDefault();
		BX.closeWait();
	}
});
$('#wd_reviews2_field_edit_iframe').load(function(){
	WD_Reviews2_OnFieldSave($(this).contents().find('html > body').html());
});
</script>

<?

?>

