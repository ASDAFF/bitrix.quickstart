<?php
/**
 * Created by Artmix.
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 * Date: 27.10.2014. Time: 12:49
 */

/** @var string $REQUEST_METHOD */
/** @var string $Apply */
/** @var string $RestoreDefaults */
/** @var string $mid */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/interface/admin_lib.php");

if (!$USER->IsAdmin())
    return;

$module_id = 'artmix.noticefixcore';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\SystemException;

Loader::includeModule($module_id);

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$fileConfigPath = sprintf('%s/.settings_%s.php', __DIR__, LANGUAGE_ID);

$configData = null;

if (is_file($fileConfigPath)) {
    $configData = require($fileConfigPath);

    if (!is_array($configData)) {
        $configData = array(
            'notice_text' => '',
        );
    }

}

$arAllOptions = Array(
    array("notice_text", Loc::getMessage("ARTMIX_NOTICEFIXCORE_SETTINGS_NOTICE_TEXT"), "", array("textarea", 20, 100)),
);

$aTabs = array(
    array("DIV" => "edit1", "TAB" => Loc::getMessage("ARTMIX_NOTICEFIXCORE_SETTINGS_TAB_SET"), "ICON" => "olegpro_rollbar_settings", "TITLE" => Loc::getMessage("ARTMIX_NOTICEFIXCORE_SETTINGS_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && check_bitrix_sessid()) {
    if (strlen($RestoreDefaults) > 0) {
        Option::delete($module_id);
    } else {
        foreach ($arAllOptions as $arOption) {
            if(isset($_REQUEST[$arOption[0]])) {
                $val = $_REQUEST[$arOption[0]];
            }else{
                $val = '';
            }

            if ($arOption[3][0] == "checkbox" && $val != "Y")
                $val = "N";

            if ($arOption[0] == 'notice_text') {

                $optionsData = array(
                    'notice_text' => $val,
                );

                $fp = fopen($fileConfigPath, 'w');

                if($fp === false) {
                    throw new SystemException(
                        GetMessage('ARTMIX_NOTICEFIXCORE_SETTINGS_NOTICE_TEXT_ERROR_CREATE_SETTINGS_FILE', array(
                            '#FILE#' => $fileConfigPath,
                        ))
                    );
                }

                if (!fwrite($fp, sprintf("<?php\nreturn %s;", var_export($optionsData, true)))) {
                    throw new SystemException(
                        GetMessage('ARTMIX_NOTICEFIXCORE_SETTINGS_NOTICE_TEXT_ERROR_WRITE_SETTINGS_FILE', array(
                            '#FILE#' => $fileConfigPath,
                        ))
                    );
                }

                fclose($fp);

            } else {
                Option::set($module_id, $arOption[0], $val);
            }

        }

    }
    if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
}


$tabControl->Begin();
?>
<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
    <? $tabControl->BeginNextTab(); ?>
    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" class="internal">
                <?php
                foreach ($arAllOptions as $arOption) {

                    if ($arOption[0] == 'notice_text') {
                        $val = is_array($configData) ? $configData['notice_text'] : '';
                    } else {
                        $val = Option::get($module_id, $arOption[0], $arOption[2]);
                    }

                    $type = $arOption[3];
                    ?>

                    <tr>
                        <td valign="top" width="15%"><?
                            if ($type[0] == "checkbox")
                                echo "<label for=\"" . htmlspecialchars($arOption[0]) . "\">" . $arOption[1] . "</label>";
                            else
                                echo $arOption[1];?>:
                        </td>
                        <td valign="top" width="85%">
                            <? if ($type[0] == "checkbox"): ?>
                                <input type="checkbox" id="<? echo htmlspecialchars($arOption[0]) ?>"
                                       name="<? echo htmlspecialchars($arOption[0]) ?>"
                                       value="Y"<? if ($val == "Y") echo " checked"; ?>>
                            <? elseif ($type[0] == "text"): ?>
                                <input type="text" size="<? echo $type[1] ?>" maxlength="50"
                                       value="<? echo htmlspecialchars($val) ?>" name="<? echo htmlspecialchars($arOption[0]) ?>">
                            <?
                            elseif ($type[0] == "textarea"): ?>
                                <textarea id="artmix_noticefixcore_<? echo htmlspecialchars($arOption[0]) ?>" rows="<? echo $type[1] ?>" cols="<? echo $type[2] ?>"
                                          name="<? echo htmlspecialchars($arOption[0]) ?>"><? echo htmlspecialchars($val) ?></textarea>
                            <?elseif($type[0] == "datetime"): ?>
                                <?=\CAdminCalendar::CalendarDate($arOption[0], $val, '10', true)?>

                            <?endif; ?>
                            <?php
                            if($arOption[0] == 'notice_text'){
                                echo BeginNote(), htmlspecialcharsbx(Loc::getMessage('ARTMIX_NOTICEFIXCORE_SETTINGS_NOTICE_TEXT_DESCRIPTION')), EndNote();
                            }
                            ?>
                        </td>
                    </tr>
                <? } ?>
            </table>
        </td>
    </tr>

    <? $tabControl->BeginNextTab(); ?>

    <? $tabControl->Buttons(); ?>
    <input type="submit" name="Update" value="<?= Loc::getMessage("MAIN_SAVE") ?>"
           title="<?= Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>">
    <input type="submit" name="Apply" value="<?= Loc::getMessage("MAIN_OPT_APPLY") ?>"
           title="<?= Loc::getMessage("MAIN_OPT_APPLY_TITLE") ?>">
    <? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
        <input type="button" name="Cancel" value="<?= Loc::getMessage("MAIN_OPT_CANCEL") ?>"
               title="<?= Loc::getMessage("MAIN_OPT_CANCEL_TITLE") ?>"
               onclick="window.location='<? echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
        <input type="hidden" name="back_url_settings"
               value="<?= htmlspecialchars($_REQUEST["back_url_settings"]) ?>">
    <? endif ?>
    <input type="submit" name="RestoreDefaults" title="<? echo Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           OnClick="return confirm('<? echo AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<? echo Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>">
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
    </td>
    </tr>

</form>

<?php
if(COption::GetOptionString('fileman', "use_code_editor", "Y") == "Y" && CModule::IncludeModule('fileman'))
{
    CCodeEditor::Show(array(
        'textareaId' => 'artmix_noticefixcore_notice_text',
        'height' => 350,
        'width' => 900,
        'forceSyntax' => 'php',
    ));
}
