<form action="<?=$APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="rarus.sms4b">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage("CAUTION_MESS"))?>
	<p><?=GetMessage('UNINST_MESS_1')?></p>
	<p><input type="checkbox" name="save_tables" id="save_tables" value="Y" checked><label for="save_tables"><?=GetMessage('UNINST_MESS_2')?></label></p>
	<p><input type="checkbox" name="SAVE_COMPONENTS" id="SAVE_COMPONENTS" value="Y" /><label for="SAVE_COMPONENTS"><?=GetMessage('UNINST_MESS_3')?></label><br />
		<font color="red"><?=GetMessage('CAUTION_MESS_1')?></font></p>
	<p><input type="checkbox" name="SAVE_DEMO" id="SAVE_DEMO" value="Y" /><label for="SAVE_DEMO"><?=GetMessage('SAVE_DEMO_PART')?></label><br />
		<font color="red"><?=GetMessage('CAUTION_MESS_2')?></font>
	</p>
	<p><input type="checkbox" name="SAVE_HELP" id="SAVE_HELP" value="Y" /><label for="SAVE_HELP"><?=GetMessage('SAVE_DOCS')?></label></p>
	<input type="submit" name="inst" value="<?=GetMessage('UNINST_MOD')?>">
</form>