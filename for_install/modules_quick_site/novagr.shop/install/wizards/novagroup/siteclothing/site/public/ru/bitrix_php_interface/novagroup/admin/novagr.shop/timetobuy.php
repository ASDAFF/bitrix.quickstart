<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true) return;

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_TIMETOBUY_TAB"), "ICON" => "main_user_edit", "TITLE" => GetMessage("NOVAGROUP_TIMETOBUY_TITLE")),
);
if (!empty($_POST["save"])) {
    // clear cache for catalog

    $rsSites = CSite::GetList($by="sort", $order="desc", Array());
    while ($arSite = $rsSites->Fetch())
    {
        //deb($arSite["LID"]);
        BXClearCache(true, "/".$arSite["LID"]."/novagr.shop/catalog.list/");
        BXClearCache(true, "/".$arSite["LID"]."/novagr.shop/catalog.element.preview/");
        BXClearCache(true, "/".$arSite["LID"]."/novagr.shop/catalog.element/");

    }

    COption::SetOptionString("novagroup","TIMETOBUY_DISABLE",$_POST['DISABLE']);
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_TIMETOBUY_OK"));
}
$DISABLE = COption::GetOptionString("novagroup","TIMETOBUY_DISABLE");

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>
<form method="POST" action="">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><? echo GetMessage("NOVAGROUP_TIMETOBUY_LABEL") ?></td>
        <td width="60%"><? echo InputType("checkbox", "DISABLE", "Y", $DISABLE) ?></td>
    </tr>
    <?
    $tabControl->Buttons(array("btnApply"=>false));
    $tabControl->End();
    ?>
</form>
