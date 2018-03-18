<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="rarus.sms4b">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">

	<p><?=GetMessage('INSTALL_MESS_1')?></p>
	<p><input type=checkbox name="INSTALL_COMPONENTS" id="INSTALL_COMPONENTS" value="Y" checked />
		<label for="INSTALL_COMPONENTS"><?=GetMessage('INSTALL_MESS_2')?></label><br />
		<font color="green"><?=GetMessage('INSTALL_MESS_2_COMM')?></font>
	</p>
	<p><input type=checkbox name="INSTALL_DEMO" id="INSTALL_DEMO" value="Y" checked /> 
		<label for="INSTALL_DEMO"><?=GetMessage('INSTALL_MESS_3')?></label><br/>
		<font color="green"><?=GetMessage("INSTALL_MESS_3_COMM")?></font>
	</p>	
	<p><input type=checkbox name="INSTALL_HELP" id="INSTALL_HELP" value="Y" checked />
		<label for="INSTALL_HELP"><?=GetMessage('INSTALL_MESS_4')?></label><br/>
		<font color="green"><?=GetMessage('INSTALL_MESS_4_COMM')?></font>
	</p>
	<input type="submit" name="inst" value="<?=GetMessage('INSTALL_MESS_5')?>" />
</form>

<p><font color="red">*</font><?=GetMessage('HELP_MESS_1')?></p>
<p><font color="red">**</font><?=GetMessage('HELP_MESS_2')?></p>