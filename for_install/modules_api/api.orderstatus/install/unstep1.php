<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
IncludeModuleLangFile(__FILE__);
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="api.orderstatus">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<p><?=GetMessage("MOD_UNINST_SAVE")?></p>
	<p>
		<label for="savedata">
			<input type="checkbox" name="savedata" id="savedata" value="Y" checked>
			<?=GetMessage("MOD_UNINST_SAVE_DATA")?>
		</label>
	</p>
	<input type="submit" name="inst" value="<?=GetMessage("MOD_UNINST_DEL")?>">
</form>