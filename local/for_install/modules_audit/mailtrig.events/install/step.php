<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MAILTRIG_EVENTS_INSTALL_STEP"));
?>
<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="mailtrig.events" />
	<input type="hidden" name="install" value="Y" />
	<input type="hidden" name="step" value="2" />
	<h2><?=GetMessage("MAILTRIG_EVENTS_DO_YOU_HAVE_ACCOUNT")?></h2>
	<p>
		<input type="submit" name="ContinueRegister" id="" value="<?=GetMessage("MAILTRIG_EVENTS_DO_YOU_HAVE_ACCOUNT_NO_I_WANT_REGISTER")?>" class="adm-btn-save" />
		<input type="submit" name="Continue" id="" value="<?=GetMessage("MAILTRIG_EVENTS_DO_YOU_HAVE_ACCOUNT_YES")?>" />
	</p>
</form>