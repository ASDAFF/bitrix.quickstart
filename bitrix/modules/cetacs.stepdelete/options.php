<?
$module_id = "cetacs.stepdelete";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT >= "R") :

    IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
    IncludeModuleLangFile(__FILE__);

    $arAllOptions = array(
        array("step_time", GetMessage("step_time"), array("text", 5)),
        array("step_delay", GetMessage("step_delay"), array("text", 5)),
    );
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
    );
    $tabControl = new CAdminTabControl("tabControl_cetacs_stepdelete_settings", $aTabs);

    if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && $POST_RIGHT == "W" && check_bitrix_sessid()) {
        if (strlen($RestoreDefaults) > 0) {
            COption::RemoveOption($module_id);
        } else {
            foreach ($arAllOptions as $arOption) {
                if ($arOption[2][0] == "checkbox")
                    if ($_POST[$arOption[0]] <> "Y")
                        $_POST[$arOption[0]] = "N";
                COption::SetOptionString($module_id, $arOption[0], $_POST[$arOption[0]]);

                if (!is_numeric($_POST[$arOption[0]]))
                    COption::RemoveOption($module_id);
            }
        }

        $Update = $Update . $Apply;
        ob_start();
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();

        if (strlen($_REQUEST["back_url_settings"]) > 0) {
            if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
            else
                LocalRedirect($_REQUEST["back_url_settings"]);
        }
        else {
            LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $tabControl->ActiveTabParam());
        }
    }
    ?>
    <form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= LANGUAGE_ID ?>">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        foreach ($arAllOptions as $Option):
            $type = $Option[2];
            $val = COption::GetOptionString($module_id, $Option[0]);
            ?>
            <tr>
                <td valign="top" width="50%"><?
        if ($type[0] == "checkbox")
            echo "<label for=\"" . htmlspecialchars($Option[0]) . "\">" . $Option[1] . "</label>";
        else
            echo $Option[1];
            ?></td>
                <td valign="middle" width="50%"><?
            if ($type[0] == "checkbox"):
                ?><input type="checkbox" name="<? echo htmlspecialchars($Option[0]) ?>" id="<? echo htmlspecialchars($Option[0]) ?>" value="Y"<? if ($val == "Y") echo" checked"; ?>><?
        elseif ($type[0] == "text"):
                ?><input type="text" size="<? echo $type[1] ?>" maxlength="255" value="<? echo htmlspecialchars($val) ?>" name="<? echo htmlspecialchars($Option[0]) ?>"><?
        elseif ($type[0] == "textarea"):
                ?><textarea rows="<? echo $type[1] ?>" cols="<? echo $type[2] ?>" name="<? echo htmlspecialchars($Option[0]) ?>"><? echo htmlspecialchars($val) ?></textarea><?
        elseif ($type[0] == "text-list"):
            $aVal = explode(",", $val);
            for ($j = 0; $j < count($aVal); $j++):
                    ?><input type="text" size="<? echo $type[2] ?>" value="<? echo htmlspecialchars($aVal[$j]) ?>" name="<? echo htmlspecialchars($Option[0]) . "[]" ?>"><br><?
            endfor;
            for ($j = 0; $j < $type[1]; $j++):
                    ?><input type="text" size="<? echo $type[2] ?>" value="" name="<? echo htmlspecialchars($Option[0]) . "[]" ?>"><br><?
            endfor;
        elseif ($type[0] == "selectbox"):
            $arr = $type[1];
            $arr_keys = array_keys($arr);
                ?><select name="<? echo htmlspecialchars($Option[0]) ?>"><?
            for ($j = 0; $j < count($arr_keys); $j++):
                    ?><option value="<? echo $arr_keys[$j] ?>"<? if ($val == $arr_keys[$j]) echo" selected" ?>><? echo htmlspecialchars($arr[$arr_keys[$j]]) ?></option><?
            endfor;
                ?></select><?
            endif;
            ?></td>
            </tr>
        <? endforeach ?>
        <? $tabControl->BeginNextTab(); ?>
        <? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>
        <? $tabControl->Buttons(); ?>
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>" title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>">
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="Apply" value="<?= GetMessage("MAIN_OPT_APPLY") ?>" title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
        <? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
            <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="button" name="Cancel" value="<?= GetMessage("MAIN_OPT_CANCEL") ?>" title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>" onclick="window.location='<? echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
            <input type="hidden" name="back_url_settings" value="<?= htmlspecialchars($_REQUEST["back_url_settings"]) ?>">
        <? endif ?>
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')" value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End(); ?>
    </form>
<? endif; ?>
