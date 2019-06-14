<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

$module_id = "sota.parser";
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT >= "R") :

    IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");

    IncludeModuleLangFile(__FILE__);

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && $POST_RIGHT == "W" && check_bitrix_sessid()) {
        $Update = $Update . $Apply;
        ob_start();
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();

        if (strlen($_REQUEST["back_url_settings"]) > 0) {
            if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
            else
                LocalRedirect($_REQUEST["back_url_settings"]);
        } else {
            LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $tabControl->ActiveTabParam());
        }
    }


    ?>
    <form method="post"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= LANGUAGE_ID ?>">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>
        <? $tabControl->Buttons(); ?>
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="Update"
                                                            value="<?= GetMessage("MAIN_SAVE") ?>"
                                                            title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>">
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="Apply"
                                                            value="<?= GetMessage("MAIN_OPT_APPLY") ?>"
                                                            title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
        <? if (strlen($_REQUEST["back_url_settings"]) > 0):?>
            <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="button" name="Cancel"
                                                                value="<?= GetMessage("MAIN_OPT_CANCEL") ?>"
                                                                title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>"
                                                                onclick="window.location='<? echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
            <input type="hidden" name="back_url_settings"
                   value="<?= htmlspecialchars($_REQUEST["back_url_settings"]) ?>">
        <? endif ?>
        <input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="RestoreDefaults"
                                                            title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
                                                            OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
                                                            value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->End(); ?>
    </form>
<? endif; ?>
