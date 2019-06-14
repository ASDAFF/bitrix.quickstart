<? if (!check_bitrix_sessid())
	return;

$MODULE_ID = "%DOT%";
$MLANG = "%UNDER_CAPS%_";
?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<?=bitrix_sessid_post();?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="<?=$MODULE_ID?>">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage($MLANG."UNINSTALL_WARNING"));?>
	<p><?=GetMessage($MLANG."UNINSTALL_SAVEDATA") ?></p>
	<p>
		<input type="checkbox" name="savedata" id="savedata" value="Y" checked>
		<label for="savedata"><?=GetMessage($MLANG."UNINSTALL_SAVETABLE");?></label>
	</p>
	<input type="submit" name="inst" value="<?=GetMessage($MLANG."UNINSTALL_DEL");?>">
</form>
