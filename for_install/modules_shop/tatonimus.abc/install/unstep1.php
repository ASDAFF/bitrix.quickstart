<form action="<?=$APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="tatonimus.abc">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage('TABC_CAUTION_MESS'))?>
	<p><?=GetMessage('TABC_UNINST_MESS_1')?></p>
	<p><input type="checkbox" name="SAVE_COMPONENTS" id="SAVE_COMPONENTS" value="Y" /><label for="SAVE_COMPONENTS"><?=GetMessage('TABC_UNINST_MESS_3')?></label><br />
		<font color="red"><?=GetMessage('TABC_CAUTION_MESS_1')?></font></p>
	<p><input type="checkbox" name="SAVE_DEMO" id="SAVE_DEMO" value="Y" /><label for="SAVE_DEMO"><?=GetMessage('TABC_SAVE_DEMO_PART')?></label><br />
		<font color="red"><?=GetMessage('TABC_CAUTION_MESS_2')?></font>
	</p>
	<input type="submit" name="inst" value="<?=GetMessage('TABC_UNINST_MOD')?>">
</form>