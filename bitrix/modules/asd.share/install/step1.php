<?if(!check_bitrix_sessid()) return;?>
<?
CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asd.share/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/", true, true);
RegisterModule("asd.share");
echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" method="get">
<p>
	<input type="hidden" name="lang" value="<?echo LANG?>" />
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>" />
</p>
<form>