<?if (!check_bitrix_sessid()) return;?>


<form name="form1" action="<?=$APPLICATION->GetCurPageParam()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>"/>
	<input type="hidden" name="id" value="infospice.favorites"/>
	<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>

	<input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL")?>"/>

</form>

