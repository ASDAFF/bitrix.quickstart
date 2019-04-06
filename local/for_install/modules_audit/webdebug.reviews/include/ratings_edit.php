<?
IncludeModuleLangFile(__FILE__);

if (IntVal($WD_Reviews2_InterfaceID)<=0) {
	return;
}

$WD_Reviews2_RatingID = IntVal($WD_Reviews2_RatingID);
$RatingMode = $WD_Reviews2_RatingID>0 ? 'edit' : 'add';

if ($RatingMode=='edit') {
	$resFields = CWD_Reviews2_Ratings::GetByID($WD_Reviews2_RatingID);
	$arFields = $resFields->GetNext();
}
if (!is_array($arFields)) {
	$arFields = array(
		'MIN' => '0',
		'MAX' => '5',
		'SORT' => '100',
		'PARTICIPATES' => 'Y',
	);
}

if (webdebug_reviews_demo_expired()) {
	webdebug_pageprops_show_demo();
	die();
}

$arTabs = array(
	array('DIV' => 'tab_rating_edit_general', 'TAB' => GetMessage('WD_REVIEWS2_TAB_GENERAL_NAME'), 'TITLE' => GetMessage('WD_REVIEWS2_TAB_GENERAL_DESC')),
);

$TabControl = new CAdminViewTabControl('WD_Reviews2_Rating_edit', $arTabs);
?>

<div style="display:none"><iframe src="" name="wd_reviews2_rating_edit_iframe" id="wd_reviews2_rating_edit_iframe"></iframe></div>
<form action="/bitrix/admin/wd_reviews2_ratings.php?action=save&interface=<?=$WD_Reviews2_InterfaceID;?>&rating=<?=$WD_Reviews2_RatingID;?>&lang=<?=LANGUAGE_ID;?>" method="post" id="wd_reviews2_rating_edit_form" enctype="multipart/form-data" target="wd_reviews2_rating_edit_iframe">
	<?$TabControl->Begin();?>
	<?$TabControl->BeginNextTab();?>
		<table class="adm-detail-content-table edit-table wd_reviews2_rating_edit_table" id="wd_reviews2_rating_edit_table_1">
			<tbody>
				<tr id="tr_name">
					<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_RATING_NAME');?>:</td>
					<td class="field-data">
						<input type="text" name="fields[NAME]" value="<?=$arFields['NAME'];?>" size="60" maxlength="255" style="width:90%" data-required="Y" />
					</td>
				</tr>
				<tr id="tr_sort">
					<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_RATING_SORT');?>:</td>
					<td class="field-data">
						<input type="text" name="fields[SORT]" value="<?=$arFields['SORT'];?>" size="10" maxlength="10" style="width:90%" />
					</td>
				</tr>
				<tr id="tr_description">
					<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_RATING_DESCRIPTION');?>:</td>
					<td class="field-data">
						<textarea name="fields[DESCRIPTION]" cols="60" rows="2" style="overflow:auto; resize:vertical; width:90%;"><?=$arFields['DESCRIPTION'];?></textarea>
					</td>
				</tr>
				<tr id="tr_participates">
					<td class="field-name" width="40%"><?=GetMessage('WD_REVIEWS2_RATING_PARTICIPATES');?>:</td>
					<td class="field-data">
						<input type="checkbox" name="fields[PARTICIPATES]" id="wdr2_rating_participates" value="Y"<?if($arFields['PARTICIPATES']=='Y'):?> checked="checked"<?endif?> />
						<script>BX.adminFormTools.modifyCheckbox(document.getElementById('wdr2_rating_participates'));</script>
					</td>
				</tr>
			</tbody>
		</table>
	<?$TabControl->End();?>
	<div style="display:none"><input type="submit" value="" /></div>
</form>
<script>
$('#wd_reviews2_rating_edit_form').find('input[data-required=Y]').parents('tr').find('td.field-name').addClass('adm-required-field');
$('#wd_reviews2_rating_edit_form').submit(function(Event){
	var CancelSubmit = false;
	$(this).find('input[data-required=Y]').each(function(){
		var Value = $.trim($(this).val());
		if (Value) {
			$(this).removeClass('wd_reviews2_error_rating');
		} else {
			$(this).addClass('wd_reviews2_error_rating');
			CancelSubmit = true;
		}
	});
	if (CancelSubmit) {
		var Clicked = false;
		$('#tab_rating_edit_general').each(function(){
			if ($(this).find('.wd_reviews2_error_rating').size()>0 && !Clicked) {
				$('#view_tab_' + $(this).attr('id')).click();
				Clicked = true;
			}
		});
		Event.preventDefault();
		BX.closeWait();
	}
});
$('#wd_reviews2_rating_edit_iframe').load(function(){
	WD_Reviews2_OnRatingSave($(this).contents().find('html > body').html());
});
</script>
