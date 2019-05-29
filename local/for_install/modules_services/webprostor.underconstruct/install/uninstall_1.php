<form action="<?=$APPLICATION->GetCurPage()?>" onsubmit="this['inst'].disabled=true; return true;">
<?=bitrix_sessid_post()?>
<?
$rsSites = CSite::GetList();
$placeHolders = Array();
while ($arSite = $rsSites->Fetch())
{
	$placeHolders[$arSite["LID"]] = $arSite["NAME"];
}
?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="webprostor.underconstruct">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	<p><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_DELETE_LOGO_DESCRIPTION")?></p>
	<p><input type="checkbox" name="deletelogodir" id="deletelogodir" value="Y" checked><label for="deletelogodir"><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_DELETE_LOGO_DIR")?></label></p>
	<hr />
	<p><?=GetMessage("WEBPROSTOR_UNDERCONSTRUCT_DELETE_FILES_DESCRIPTION")?></p>
	<?foreach($placeHolders as $lid => $site):?>
	<p><input type="checkbox" name="delete_placeholder_<?=$lid?>" id="delete_placeholder_<?=$lid?>" value="Y" checked><label for="delete_placeholder_<?=$lid?>">[<?=$lid?>] <?=$site?></label></p>
	<?endforeach;?>
	<input type="submit" name="inst" value="<?=GetMessage("MOD_UNINST_DEL")?>" />
</form>