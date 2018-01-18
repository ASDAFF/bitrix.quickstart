<?if(!check_bitrix_sessid()) return;?>
<?
$errors = false;

if (!IsModuleInstalled("statistic"))
	$errors[] = GetMessage("need_module_statistic");

if ($errors === false)
{
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asd.usersonline/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/", true, true);
	RegisterModule("asd.usersonline");
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
}
else
{
	for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br />";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
}
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" method="get">
<p>
	<input type="hidden" name="lang" value="<?echo LANG?>" />
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>" />
</p>
<form>