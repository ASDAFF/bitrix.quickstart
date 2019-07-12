<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true) return;

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_MYSQL_TAB"), "ICON" => "main_user_edit", "TITLE" => GetMessage("NOVAGROUP_MYSQL_TITLE")),
    array("DIV" => "edit2", "TAB" => GetMessage("NOVAGROUP_MYSQL_TAB2"), "ICON" => "main_user_edit", "TITLE" => GetMessage("NOVAGROUP_MYSQL_TITLE2")),
);

$arTables = array();
$arRows = array();

if (empty($_POST['optimize'])) {
    $alterTable = false;
} else {
    $alterTable = true;
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_MYSQL_OK"));
}

global $DB;
$res = CIBlock::GetList(
    Array("NAME" => "ASC"),
    Array(
        'VERSION' => '2',
    ), true
);
while ($ar_res = $res->Fetch()) {
    $strSql = "SHOW COLUMNS FROM  `b_iblock_element_prop_m{$ar_res['ID']}`";
    $result = $DB->Query($strSql, true);
    if (method_exists($result, 'Fetch')) {
        while ($row = $result->Fetch()) {
            if (strtolower(substr($row['Type'], 0, 3)) == 'int' and empty($row['Key'])) {
                if ($alterTable === true) {
                    $DB->Query("ALTER TABLE `b_iblock_element_prop_m{$ar_res['ID']}` ADD INDEX (  `" . $row['Field'] . "` )");
                } else {
                    $arRows["b_iblock_element_prop_m{$ar_res['ID']}"][] = $row['Field'];
                    $arTables[$ar_res['ID']] = $ar_res['NAME'] . " (IBLOCK_ID: " . $ar_res['ID'] . ")";
                }
            }
        }
    }
    $strSql = "SHOW COLUMNS FROM  `b_iblock_element_prop_s{$ar_res['ID']}`";
    $result = $DB->Query($strSql, true);
    if (method_exists($result, 'Fetch')) {
        while ($row = $result->Fetch()) {
            if (strtolower(substr($row['Type'], 0, 3)) == 'int' and empty($row['Key'])) {
                if ($alterTable === true) {
                    $DB->Query("ALTER TABLE `b_iblock_element_prop_s{$ar_res['ID']}` ADD INDEX (  `" . $row['Field'] . "` )");
                } else {
                    $arRows["b_iblock_element_prop_s{$ar_res['ID']}"][] = $row['Field'];
                    $arTables[$ar_res['ID']] = $ar_res['NAME'] . " (IBLOCK_ID: " . $ar_res['ID'] . ")";
                }
            }
        }
    }
}

if(isset($_POST['optimize_sorting']) and !empty($_POST['sorting']))
{
    $sorting = ($_POST['sorting']>0 && $_POST['sorting']<4) ? $sorting : 1 ;
    Coption::SetOptionInt("novagroup","optimize_sorting",$sorting);
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_MYSQL_SORT_OK"));
}
$sorting = Coption::GetOptionInt("novagroup","optimize_sorting", 1);

$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?lang=<? echo htmlspecialcharsbx(LANG) ?>"
      name="fs1">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td colspan="2" style="padding-bottom:10px;"><? echo GetMessage("NOVAGROUP_MYSQL_TEXT") ?></td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <ul>
                <? if (count($arTables) > 0): ?>
                    <? foreach ($arTables as $table): ?>
                        <li><?= $table ?></li>
                    <? endforeach ?>
                <? else: ?>
                    <li><?= GetMessage("NOVAGROUP_MYSQL_NO_TABLES") ?></li>
                <?endif ?>
            </ul>
        </td>
    </tr>
    <? if (count($arRows) > 0 and count($arTables) > 0): ?>
        <tr>
            <td colspan="2" style="padding-bottom:10px;"><? echo GetMessage("NOVAGROUP_MYSQL_TEXT_ROWS") ?></td>
        </tr>
        <tr>
            <td colspan="2" width="100%">
                <ul>
                    <? foreach ($arRows as $id => $rows): ?>
                        <? foreach ($rows as $row): ?>
                            <li><?= $row ?> (Table: <?= $id ?>)</li>
                        <? endforeach ?>
                    <? endforeach ?>
                </ul>
            </td>
        </tr>
    <? endif ?>
    <?
    if (count($arTables) > 0):
        ?>
        <tr>
            <td colspan="2" width="100%">
                <input class="mybutton"
                       type="submit" name="optimize"
                       value="<? echo GetMessage("NOVAGROUP_MYSQL_SUBMIT") ?>"
                       title="<? echo GetMessage("NOVAGROUP_MYSQL_SUBMIT") ?>"/>
            </td>
        </tr>
    <?
    endif;
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td colspan="2" style="padding-bottom:10px;"><? echo GetMessage("NOVAGROUP_MYSQL_TEXT2") ?></td>
    </tr>
    <tr>
        <td colspan="2" width="100%">

            <ul style="list-style-type: none">
                <li>
                    <input type="radio" name="sorting" <?=($sorting=="1")?"checked=checked":"";?> value="1" id="sorting1">
                    <label for="sorting1"><? echo GetMessage("NOVAGROUP_MYSQL_SORT_TEXT1") ?></label> <sup>- 1</sup>
                </li>
                <li>
                    <input type="radio" name="sorting" <?=($sorting=="2")?"checked=checked":"";?> value="2" id="sorting2">
                    <label for="sorting2"><? echo GetMessage("NOVAGROUP_MYSQL_SORT_TEXT2") ?></label> <sup>- 2</sup>
                </li>
                <li>
                    <input type="radio" name="sorting" <?=($sorting=="3")?"checked=checked":"";?> value="3" id="sorting3">
                    <label for="sorting3"><? echo GetMessage("NOVAGROUP_MYSQL_SORT_TEXT3") ?></label> <sup>- 3</sup>
                </li>
            </ul>
        </td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <input class="mybutton"
                   type="submit" name="optimize_sorting"
                   value="<? echo GetMessage("NOVAGROUP_MYSQL_SUBMIT") ?>"
                   title="<? echo GetMessage("NOVAGROUP_MYSQL_SUBMIT") ?>"/>
        </td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <? echo GetMessage("NOVAGROUP_MYSQL_NOTE2") ?>
        </td>
    </tr>
    <?
    $tabControl->Buttons();
    $tabControl->End();
    ?>
</form>
