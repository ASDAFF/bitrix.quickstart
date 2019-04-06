<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

if($ex = $APPLICATION->GetException())
{
        echo CAdminMessage::ShowMessage(Array(
                "TYPE" => "ERROR",
                "MESSAGE" => GetMessage("MOD_INST_ERR"),
                "DETAILS" => $ex->GetString(),
                "HTML" => true,
        ));
}
else
        echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
?>
<p><?=GetMessage("PANEL_STEP_L1")?> <a href="/bitrix/admin/settings.php?mid=itena.panel&lang=ru"><?=GetMessage("PANEL_STEP_L2")?></a>.</p>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>