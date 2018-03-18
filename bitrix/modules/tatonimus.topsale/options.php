<?php
$module_id = "tatonimus.topsale";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($MODULE_RIGHT >= "R") {
    CModule::IncludeModule($module_id);
    CModule::IncludeModule('iblock');

    $arIBlockList = array();
    $rsIBlockType = CIBlockType::GetList(array("SORT" => "ASC"));
    while ($arIBlockType = $rsIBlockType->GetNext()) {
        $arIBlockType['LANG'] = CIBlockType::GetByIDLang($arIBlockType["ID"],
                LANG);
        $arIBlockList[$arIBlockType['ID']] = $arIBlockType;
    }
    $rsIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'));
    while ($arIBlock = $rsIBlock->GetNext()) {
        $arIBlockList[$arIBlock['IBLOCK_TYPE_ID']]['IBLOCK_LIST'][$arIBlock['ID']] = $arIBlock;
    }

    $arIBlockProperty = array();
    $rsProperty = CIBlockProperty::GetList();
    while ($arProperty = $rsProperty->GetNext()) {
        $arIBlockProperty[$arProperty['IBLOCK_ID']][$arProperty['ID']] = $arProperty;
    }

    $arPropertyList = CTopsale::GetListArray();

    $arAllOptions = array(
        array("order", GetMessage("TTSML_ORDER"), COption::GetOptionString($module_id,
                "order"), array("checkbox", "Y")),
        array("period", GetMessage("TTSML_PERIOD"), COption::GetOptionString($module_id,
                "period"), array("text", 3)),
        array("level_count", GetMessage("TTSML_LEVEL_COUNT"), COption::GetOptionString($module_id,
                "level_count"), array("text", 3)),
    );

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("TTSML_TAB_PARAM"), "ICON" => "replica_settings", "TITLE" => GetMessage("TTSML_TAB_TITLE_PARAM")),
        array("DIV" => "edit2", "TAB" => GetMessage("TTSML_TAB_IBLOCK"), "ICON" => "replica_settings", "TITLE" => GetMessage("TTSML_TAB_IBLOCK_OPTIONS")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && $MODULE_RIGHT >= "W" && check_bitrix_sessid()) {
        if (strlen($RestoreDefaults) > 0) {
            COption::RemoveOption($module_id);
        } else {
            COption::RemoveOption($module_id, "sid");

            foreach ($arAllOptions as $arOption) {
                $name = $arOption[0];
                $val = $_REQUEST[$name];
                if ($arOption[2][0] == "checkbox" && $val != "Y")
                    $val = "N";
                COption::SetOptionString($module_id, $name, $val, $arOption[1]);
            }

            $arPropertyListPost = array();
            foreach ($_REQUEST['PROPERTY_LIST'] as $keyDB => $arValues) {
                foreach ($arValues as $ID => $value) {
                    $arPropertyListPost[$ID][$keyDB] = $value;
                }
            }
            foreach ($arPropertyListPost as $ID => $arFields) {
                if ($arFields['DELETE']
                    || (empty($arFields['IBLOCK_ID'])
                    && isset($arPropertyList[$ID])
                    )) {
                    CTopsale::Delete($ID);
                } elseif (isset($arPropertyList[$ID])) {
                    CTopsale::Update($ID, $arFields);
                } elseif (!empty($arFields['IBLOCK_ID'])) {
                    CTopsale::Add($arFields);
                }
            }
        }
    }
?>
<script>
    function TopsaleChangeIBlock(obj)
    {
        var objList = BX.findChild(BX.findNextSibling(BX.findParent(obj, {tag: 'td'}), {tag: 'td'}), {tag: 'option'}, true, true);
        for (var i = 0; i < objList.length; i++) {
            if (objList[i].hasAttribute('iblock')) {
                if (objList[i].getAttribute('iblock') == obj.value) {
                    objList[i].style.display = '';
                } else {
                    objList[i].style.display = 'none';
                }
            }
        }
    }
</script>
<form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>">
<?
    if (!empty(CTopsale::$LAST_ERROR)) {
        CAdminMessage::ShowMessage(CTopsale::$LAST_ERROR);
    }

    $tabControl->Begin();
    $tabControl->BeginNextTab();
    foreach ($arAllOptions as $arOption) {
        __AdmSettingsDrawRow($module_id, $arOption);
    }

    $agentName = 'CTopsale::AgentRefresh();';
    $rsAgent = CAgent::GetList(array(), array('MODULE_ID' => $module_id, 'NAME' => $agentName));
    if ($rsAgent->SelectedRowsCount() > 0) {
        while ($arAgent = $rsAgent->Fetch()) {
?>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('TTSML_AGENT')?></td>
        <td width="50%" class="adm-detail-content-cell-r"><a href="/bitrix/admin/agent_edit.php?ID=<?=$arAgent['ID']?>&lang=<?=LANG?>"><?=$arAgent['ID']?></a> (<?=$arAgent['ACTIVE'] == 'Y' ? GetMessage('TTSML_ACTIVE') : GetMessage('TTSML_INACTIVE') ?>)</td>
    </tr>
<?
        }
    } else {
?>
    <tr>
        <td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('TTSML_AGENT')?></td>
        <td width="50%" class="adm-detail-content-cell-r"><a href="/bitrix/admin/agent_edit.php?lang=<?=LANG?>"><?=GetMessage('TTSML_AGENT_EMPTY')?> &laquo;<?=$agentName?>&raquo;</a></td>
    </tr>
<?
    }

    $tabControl->BeginNextTab();
?>
        <tr><td colspan="2">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" class="internal">
                    <tr class="heading">
                        <td valign="top"><?= GetMessage("TTSML_ID") ?></td>
                        <td valign="top"><?= GetMessage("TTSML_IBLOCK_ID") ?></td>
                        <td valign="top"><?= GetMessage("TTSML_FIELDS") ?></td>
                        <td valign="top"><?= GetMessage("TTSML_VALUE") ?><sup style="color:red;">1</sup></td>
                        <td valign="top"><?= GetMessage("TTSML_TRIGGER") ?><sup style="color:red;">2</sup></td>
                        <td valign="top"><?= GetMessage("TTSML_DELETE") ?></td>
                    </tr>
<?
    if (!empty($arPropertyList)) {
        $arPropertyList = array_values($arPropertyList);
    }
    for ($i = 0; $i < count($arPropertyList) + 4; $i++) {
        if (isset($arPropertyList[$i]) && !empty($arPropertyList[$i])) {
            $arField = $arPropertyList[$i];
        } else {
            $arField = array(
                'ID' => 'new_' . $i,
                'IBLOCK_ID' => '',
                'FIELDS' => '',
                'VALUE' => '',
                'TRIGGER' => '',
            );
        }
?>
        <tr align="center">
            <td><?= intval($arField['ID']) ? $arField['ID'] : '' ?></td>
            <td>
                <select name="PROPERTY_LIST[IBLOCK_ID][<?= $arField['ID'] ?>]" onchange="TopsaleChangeIBlock(this)">
                    <option value="0"><?= GetMessage('TTSML_SELECTED') ?></option>
<?
        foreach ($arIBlockList as $arIBlockType) {
            if (empty($arIBlockType['IBLOCK_LIST'])) {
                continue;
            }
?>
                    <option disabled><?= $arIBlockType['LANG']['NAME'] ?></option>
<?
            foreach ($arIBlockType['IBLOCK_LIST'] as $arIBlock) {
?>
                    <option value="<?= $arIBlock['ID'] ?>" <?= $arField['IBLOCK_ID'] == $arIBlock['ID'] ? 'selected' : '' ?>>&nbsp;<?= $arIBlock['NAME'] ?> (<?= $arIBlock['ID'] ?>)</option>
<?
            }
        }
?>
                </select>
            </td>
            <td>
                <select name="PROPERTY_LIST[FIELDS][<?= $arField['ID'] ?>]">
                    <option value="0"><?= GetMessage('TTSML_SORT') ?></option>
<?
            foreach ($arIBlockProperty as $iblockID => $arIBlock) {
                foreach ($arIBlock as $arProperty) {
?>
                    <option value="<?= $arProperty['ID'] ?>" iblock="<?= $iblockID ?>"<?= $arField['FIELDS'] == $arProperty['ID'] ? ' selected' : '' ?><?= $arField['IBLOCK_ID'] != $iblockID ? ' style="display: none;"' : '' ?>><?= $arProperty['NAME'] ?> (<?= $arProperty['CODE'] ?>)</option>
<?
                }
            }
?>
                </select>
            </td>
            <td><input type="text" size="4" name="PROPERTY_LIST[VALUE][<?= $arField['ID'] ?>]" value="<?=$arField['VALUE']?>" /></td>
            <td><input type="text" size="4" name="PROPERTY_LIST[TRIGGER][<?= $arField['ID'] ?>]" value="<?=$arField['TRIGGER']?>" /></td>
            <td><input type="checkbox" name="PROPERTY_LIST[DELETE][<?= $arField['ID'] ?>]" value="Y" /></td>
        </tr>
        <? } ?>
        </table>
        <p><span style="color:red;">1</span> - <?= GetMessage("TTSML_VALUE_1") ?></p>
        <p><span style="color:red;">2</span> - <?= GetMessage("TTSML_TRIGGER_2") ?></p>
    </td></tr>
<?

    if ($_SERVER['REQUEST_METHOD'] == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && check_bitrix_sessid()) {
        if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0) {
            LocalRedirect($_REQUEST["back_url_settings"]);
        } else {
            LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
        }
    }

    $tabControl->Buttons();
?>
    <input <?= $MODULE_RIGHT < "W" ? "disabled" : "" ?> type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>" title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>">
    <input <?= $MODULE_RIGHT < "W" ? "disabled" : "" ?> type="submit" name="Apply" value="<?= GetMessage("MAIN_OPT_APPLY") ?>" title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
<?
    if (strlen($_REQUEST["back_url_settings"]) > 0) {
?>
    <input type="button" name="Cancel" value="<?= GetMessage("MAIN_OPT_CANCEL") ?>" title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>" onclick="window.location='<?= htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
    <input type="hidden" name="back_url_settings" value="<?= htmlspecialchars($_REQUEST["back_url_settings"]) ?>">
<?
    }
?>
    <input <?= $MODULE_RIGHT < "W" ? "disabled" : "" ?> type="submit" name="RestoreDefaults" title="<?= GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" OnClick="confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')" value="<?= GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
<? } else { ?>
    <?= CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING')); ?>
<? } ?>