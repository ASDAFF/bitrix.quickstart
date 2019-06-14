<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
  <input type="submit" name="" onclick=" window.location.href = 'wizard_install.php?lang=ru&wizardName=kriteris.win2utf:kriteris:win2utf&sessid=<?=bitrix_sessid()?>'; return false;" value="<?echo GetMessage("SCOM_GO_TO_WIZARD")?>">
<form>
