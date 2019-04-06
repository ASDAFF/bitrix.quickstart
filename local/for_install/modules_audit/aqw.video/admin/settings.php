<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

IncludeModuleLangFile(__FILE__);

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("AQW_VIDEO_SETTINGS_TAB"), "ICON" => "main_user_edit", "TITLE" => GetMessage("AQW_VIDEO_SETTINGS_TITLE")),
);

if (isset($_POST['save'])) {
    Coption::SetOptionString("aqw.video", "jquery", $_POST['jquery']);

    echo CAdminMessage::ShowNote(GetMessage("AQW_VIDEO_MANAGER_SAVE_OK"));
}

$str_jquery = Coption::GetOptionString("aqw.video", "jquery");

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

    <form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?lang=<? echo htmlspecialcharsbx(LANG) ?>"
          name="fs1">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td width="50%" style="padding-bottom:10px;"><?=GetMessage("AQW_VIDEO_SETTINGS")?></td>
            <td width="50%" style="padding-bottom:10px;">
                <input type="hidden" name="jquery" value="Y">
                <input type="checkbox" <?if($str_jquery=="N")echo 'checked="checked"'?> name="jquery" value="N">
            </td>
        </tr>
        <?
        $tabControl->Buttons(array("btnSave"=>true,"btnApply"=>false));
        $tabControl->End();
        ?>
    </form>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>