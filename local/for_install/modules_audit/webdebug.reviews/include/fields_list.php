<?
IncludeModuleLangFile(__FILE__);

if (IntVal($WD_Reviews2_InterfaceID)<=0) {
	return;
}

if (webdebug_reviews_demo_expired()) {
	webdebug_pageprops_show_demo();
	die();
}

$arFieldTypes = WDR2_GetFieldTypes();

$bFieldsExists = false;
$bEmailExists = 0;
$bNameExists = 0;
$bReviewExists = 0;

// Form fields (from DB)
$arFormFields = array();
$resFormFields = CWD_Reviews2_Fields::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$WD_Reviews2_InterfaceID));
while ($arFormField = $resFormFields->GetNext()) {
	$arFormField['PARAMS'] = unserialize($arFormField['~PARAMS']);
	if ($arFormField['PARAMS']['is_email']=='Y') {
		$bEmailExists++;
	}
	if ($arFormField['PARAMS']['is_name']=='Y') {
		$bNameExists++;
	}
	if ($arFormField['PARAMS']['is_review']=='Y') {
		$bReviewExists++;
	}
	$arFormFields[] = $arFormField;
	$bFieldsExists = true;
}

$strWarning = false;
if (!$bFieldsExists) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_FIELDS');
} elseif ($bEmailExists===0) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_EMAIL_FIELD');
} elseif ($bNameExists===0) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_NAME_FIELD');
} elseif ($bReviewExists===0) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_NO_REVIEW_FIELD');
} elseif ($bEmailExists>1) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_TOO_MAY_EMAIL_FIELDS');
} elseif ($bNameExists>1) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_TOO_MAY_NAME_FIELDS');
} elseif ($bReviewExists>1) {
	$strWarning = GetMessage('WD_REVIEWS2_ERROR_TOO_MAY_REVIEW_FIELDS');
}
?>

<div class="adm-list-table-wrap" id="wd_reviews2_interface_fields" style="position:relative">
	<div class="adm-list-table-top">
		<input type="button" class="adm-btn adm-btn-add" value="<?=GetMessage('WD_REVIEWS2_BTN_ADD');?>" onclick="WD_Reviews2_Field_ShowPopup(true, <?=$WD_Reviews2_InterfaceID;?>);" />
		<input type="button" class="adm-btn" value="<?=GetMessage('WD_REVIEWS2_BTN_REFRESH');?>" id="wd_reviews2_fields_list_refresh" onclick="WD_Reviews2_Fields_Refresh(<?=$WD_Reviews2_InterfaceID;?>);" />
	</div>
	<table class="adm-list-table">
		<tbody>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner">ID</div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_NAME');?></div></td>
				<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_TYPE');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_CODE');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_REQUIRED');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_HIDDEN');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"><?=GetMessage('WD_REVIEWS2_FIELD_SORT');?></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"></div></td>
				<td class="adm-list-table-cell" style="width:1px;"><div class="adm-list-table-cell-inner"></div></td>
			</tr>
			<?foreach($arFormFields as $arFormField):?>
				<tr class="adm-list-table-row">
					<td class="adm-list-table-cell"><?=$arFormField['ID'];?></td>
					<td class="adm-list-table-cell"><b><?=$arFormField['NAME'];?></b></td>
					<td class="adm-list-table-cell">[<?=$arFormField['TYPE'];?>] <?=$arFieldTypes[$arFormField['TYPE']]['NAME'];?></td>
					<td class="adm-list-table-cell"><?=$arFormField['CODE'];?></td>
					<td class="adm-list-table-cell align-center"><?=($arFormField['REQUIRED']=='Y'?GetMessage('WD_REVIEWS2_Y'):GetMessage('WD_REVIEWS2_N'));?></td>
					<td class="adm-list-table-cell align-center"><?=($arFormField['HIDDEN']=='Y'?GetMessage('WD_REVIEWS2_Y'):GetMessage('WD_REVIEWS2_N'));?></td>
					<td class="adm-list-table-cell align-right"><?=$arFormField['SORT'];?></td>
					<td class="adm-list-table-cell align-right" style="padding:4px 2px;">
						<input type="button" value="<?=GetMessage('WD_REVIEWS2_FIELDS_EDIT');?>" onclick="WD_Reviews2_Fields_Edit(this, <?=$WD_Reviews2_InterfaceID;?>, <?=$arFormField['ID'];?>);" class="btn-edit" />
					</td>
					<td class="adm-list-table-cell align-right" style="padding:4px 2px;">
						<input type="button" value="<?=GetMessage('WD_REVIEWS2_FIELDS_DELETE');?>" onclick="WD_Reviews2_Fields_Delete(this, <?=$WD_Reviews2_InterfaceID;?>, <?=$arFormField['ID'];?>);" class="btn-delete" />
					</td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
	<div class="adm-list-table-footer">
		<span class="adm-table-counter adm-table-counter-visible"><?if($strWarning!==false):?><span class="wd_reviews2_warning_16" title="<?=$strWarning;?>"></span><?endif?><?=GetMessage('WD_REVIEWS2_FIELDS_COUNT');?>: <span><?=count($arFormFields);?></span></span>
	</div>
</div>
<script>
var WD_Reviews2_Popup_Field;
function WD_Reviews2_Field_ShowPopup(CreateNew, InterfaceID, FieldID) {
	BX.showWait();
	WD_Reviews2_Popup_Field = new BX.CDialog({
		title: (CreateNew?'<?=GetMessage('WD_REVIEWS2_FIELDS_POPUP_ADD');?>':'<?=GetMessage('WD_REVIEWS2_FIELDS_POPUP_EDIT');?>'),
		content: '',
		width: 900,
		height: 400,
		resizable: true,
		draggable: true
	});
	BX.addCustomEvent(WD_Reviews2_Popup_Field, 'onWindowClose', function(){
		$(this.DIV).remove();
	});
	WD_Reviews2_Popup_Field.SetButtons([
		{
			title: '<?=GetMessage('WD_REVIEWS2_FIELDS_POPUP_SAVE');?>',
			id: 'wd_reviews2_save_form_button',
			name: 'action_send',
			className: 'adm-btn-save',
			action: function(){
				BX.showWait();
				BX.ajax.submit(BX('wd_reviews2_field_edit_form'));
			}
		}, {
			title: '<?=GetMessage('WD_REVIEWS2_FIELDS_POPUP_CANCEL');?>',
			id: 'wd_reviews2_save_form_cancel',
			name: 'cancel',
			action: function(){
				WD_Reviews2_Popup_Field.Close();
			}
		}
	]);
	WD_Reviews2_Popup_Field.SetContent('');
	if (InterfaceID==undefined) {InterfaceID='';}
	if (FieldID==undefined) {FieldID='';}
	jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_fields.php?&action=edit&new='+(CreateNew?'Y':'N')+'&interface='+InterfaceID+'&field='+FieldID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
		WD_Reviews2_Popup_Field.SetContent(HTML);
		WD_Reviews2_Popup_Field.Show();
		BX.closeWait();
	});
}
function WD_Reviews2_OnFieldSave(HTML) {
	if (HTML.indexOf('#'+'WD_REVIEWS2_FIELD_SAVE_ERROR#')>-1) {
		alert('<?=GetMessage('WD_REVIEWS2_ERROR_SAVE');?>');
	} else if (HTML.indexOf('#'+'WD_REVIEWS2_FIELD_SAVE_SUCCESS#')>-1){
		WD_Reviews2_Popup_Field.Close();
		$('#wd_reviews2_interface_fields').replaceWith(HTML);
	}
	BX.closeWait();
}
function WD_Reviews2_Fields_Edit(Sender, InterfaceID, FieldID) {
	WD_Reviews2_Field_ShowPopup(Sender==false, InterfaceID, FieldID);
}
function WD_Reviews2_Fields_Delete(Sender, InterfaceID, FieldID) {
	BX.showWait();
	if (confirm('<?=GetMessage('WD_REVIEWS2_FIELDS_DELETE_CONFIRM');?>')) {
		jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_fields.php?&action=delete&interface='+InterfaceID+'&field='+FieldID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
			$('#wd_reviews2_interface_fields').replaceWith(HTML);
			BX.closeWait();
		});
	} else {
		BX.closeWait();
	}
}
function WD_Reviews2_Fields_Refresh(InterfaceID) {
	BX.showWait();
	jsAjaxUtil.LoadData('/bitrix/admin/wd_reviews2_fields.php?&action=list&interface='+InterfaceID+'&lang=<?=LANGUAGE_ID?>&' + Math.random(), function(HTML){
		$('#wd_reviews2_interface_fields').replaceWith(HTML);
		BX.closeWait();
	});
}
if (window.jQuery) {
	$('#wd_reviews2_interface_fields .adm-list-table-row').dblclick(function(){
		$(this).find('.btn-edit').click();
	});
}

</script>