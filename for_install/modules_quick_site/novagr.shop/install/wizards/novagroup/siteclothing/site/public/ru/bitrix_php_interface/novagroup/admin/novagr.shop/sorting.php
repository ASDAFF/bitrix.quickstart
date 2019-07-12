<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true) return;

include getenv("DOCUMENT_ROOT") . '/local/php_interface/novagroup/classes/shop/CatalogOffers.php';
$class = new Novagroup_Classes_General_CatalogOffers();
$selectArr = $class->getParam('orderRows');

if (isset($_POST['sortingCatalogSelect'])) {
    $sortingValue = $_POST['sortingCatalogSelect'];
    COption::SetOptionString("novagroup", "sorting_catalog", $sortingValue);
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_SORTING_OK"));

} else {
    $sortingValue = COption::GetOptionString("novagroup", "sorting_catalog");
}

if (empty($sortingValue)) {
    $sortingValue = key($selectArr);
}

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => GetMessage("NOVAGROUP_SORTING_TAB"),
        "ICON" => "main_user_edit",
        "TITLE" => GetMessage("NOVAGROUP_SORTING_TITLE")
    )
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>
<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?lang=<? echo htmlspecialcharsbx(LANG) ?>" name="fs1">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="100%">
            <label><? echo GetMessage("NOVAGROUP_SORTING_TEXT") ?></label>
            <select name="sortingCatalogSelect" id="sortingCatalogSelect">
                <?php
                foreach ($selectArr as $key => $value) {
                    ?>
                    <option <? if ($key == $sortingValue) echo "selected"; ?>
                        value="<?= $key ?>"><?echo $value['NAME'] ?></option>
                <?php
                }
                ?>
            </select>
        </td>
    </tr>

    <?
    $tabControl->Buttons();
    ?>
    <input class="mybutton"
           type="submit" name="save"
           value="<? echo GetMessage("NOVAGROUP_SORTING_SAVE") ?>"
           title="<? echo GetMessage("NOVAGROUP_SORTING_SAVE") ?>"/>
    <?
    $tabControl->End();
    ?>
</form>
