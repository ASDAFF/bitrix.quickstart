<form action="<?=$APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="tatonimus.topsale">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage('TTSML_CAUTION_MESS'))?>
	<p><?=GetMessage('TTSML_UNINST_MESS_1')?></p>
	<p><input type="checkbox" name="save_tables" id="save_tables" value="Y" checked><label for="save_tables"><?=GetMessage('TTSML_UNINST_MESS_2')?></label></p>
	<input type="submit" name="inst" value="<?=GetMessage('TTSML_UNINST_MOD')?>">
</form>