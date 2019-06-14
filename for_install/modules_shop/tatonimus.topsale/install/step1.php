<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="tatonimus.topsale">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">

	<p><?=GetMessage('TTSML_STEP1_INSTALL_MESS_1')?></p>

	<input type="submit" name="inst" value="<?=GetMessage('TTSML_STEP1_INSTALL_MESS_2')?>" />
</form>
