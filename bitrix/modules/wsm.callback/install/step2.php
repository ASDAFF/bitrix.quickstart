<?IncludeModuleLangFile(__FILE__);?>

<?
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<?if($_REQUEST["install_demo"] == 'Y'):?>
<p><?=GetMessage("GO_TODEMO_SECTION")?>: <a target="_blank" href="/callback_demo/">/callback_demo/</a></p>
<?endif;?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
