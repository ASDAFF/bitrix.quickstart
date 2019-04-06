<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="tatonimus.abc">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">

	<p><?=GetMessage('TABC_STEP1_INSTALL_MESS_1')?></p>
	<p><input type=checkbox name="INSTALL_COMPONENTS" id="INSTALL_COMPONENTS" value="Y" checked />
		<label for="INSTALL_COMPONENTS"><?=GetMessage('TABC_INSTALL_MESS_2')?></label><br />
		<font color="green"><?=GetMessage('TABC_INSTALL_MESS_2_COMM')?></font>
	</p>
	<p><input type=checkbox name="INSTALL_DEMO" id="INSTALL_DEMO" value="Y" checked />
		<label for="INSTALL_DEMO"><?=GetMessage('TABC_INSTALL_MESS_3')?></label><br/>
		<font color="green"><?=GetMessage("TABC_INSTALL_MESS_3_COMM")?></font>
	</p>

	<input type="submit" name="inst" value="<?=GetMessage('TABC_STEP1_INSTALL_MESS_2')?>" />
</form>
