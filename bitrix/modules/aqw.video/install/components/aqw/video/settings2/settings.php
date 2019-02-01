<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/aqw/video/lang/' . LANGUAGE_ID . '/settings2.php');

$obJSPopup = new CJSPopup('',
    array(
        'TITLE' => GetMessage('MYMV_SET_POPUP_TITLE'),
        'SUFFIX' => 'aqw_video_map_data',
        'ARGS' => ''
    )
);

$arData = array();
if ($_REQUEST['MAP_DATA']) {
    CUtil::JSPostUnescape();

    parse_str($_REQUEST['MAP_DATA'], $output);
    if (is_array($output) and isset($output['MAP_DATA'])) {
        $arData = (is_array($output['MAP_DATA'])) ? $output['MAP_DATA'] : array();
    }
}
?>
    <script type="text/javascript" src="/bitrix/components/aqw/video/settings2/serialize-0.2.js"></script>
<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-edit-menu');
?>
    <p><b><? echo GetMessage('MYMV_SET_POPUP_WINDOW_TITLE') ?></b></p>
    <p class="note"><? echo GetMessage('MYMV_SET_POPUP_WINDOW_DESCRIPTION') ?></p>
<?
$obJSPopup->StartContent();
?>
    <div>
        <table width="700">
            <tr>
                <th width="25%"><?= GetMessage('MYMV_SET_VIDEO') ?></th>
                <th width="25%"><?= GetMessage('MYMV_SET_TITLE') ?></th>
                <th width="25%"><?= GetMessage('MYMV_SET_PREVIEW_URL') ?></th>
                <th width="25%"><?= GetMessage('MYMV_SET_TITLE_URL') ?></th>
            </tr>
            <?
            $ITERATION = 0;
            if (is_array($arData['URL']) and is_array($arData['TITLE'])):
                ksort($arData['URL']);
                ksort($arData['TITLE']);
                ksort($arData['PREVIEW_URL']);
                ksort($arData['TITLE_URL']);
                foreach ($arData['URL'] as $KEY => $URL):
                    $TITLE = (isset($arData['TITLE'][$KEY])) ? $arData['TITLE'][$KEY] : "";
                    $TITLE = (strtolower(LANG_CHARSET)=='utf-8') ? $TITLE : iconv('UTF-8',LANG_CHARSET,$TITLE);
                    $PREVIEW_URL = (isset($arData['PREVIEW_URL'][$KEY])) ? $arData['PREVIEW_URL'][$KEY] : "";
                    $TITLE_URL = (isset($arData['TITLE_URL'][$KEY])) ? $arData['TITLE_URL'][$KEY] : "";
                    if (trim($URL) <> "" || trim($TITLE) <> ""):  $ITERATION = $ITERATION + 1;
                        ?>
                        <tr>
                            <td><input style="width:300px" type="text" value="<?= $URL ?>"
                                       name="MAP_DATA[URL][<?= $ITERATION ?>]"></td>

                            <td><input style="width:300px" type="text" value="<?= htmlspecialcharsbx($TITLE) ?>"
                                       name="MAP_DATA[TITLE][<?= $ITERATION ?>]"></td>

                            <td><input style="width:300px" type="text" value="<?= htmlspecialcharsbx($PREVIEW_URL) ?>"
                                       name="MAP_DATA[PREVIEW_URL][<?= $ITERATION ?>]"></td>

                            <td><input style="width:300px" type="text" value="<?= htmlspecialcharsbx($TITLE_URL) ?>"
                                       name="MAP_DATA[TITLE_URL][<?= $ITERATION ?>]"></td>
                        </tr>
                    <? endif; endforeach; endif;
            $ITERATION = $ITERATION + 1; ?>
            <? for ($i = $ITERATION; $i < ($ITERATION + 5); $i++): ?>
                <tr>
                    <td><input style="width:300px" type="text" name="MAP_DATA[URL][<?= $i ?>]"></td>
                    <td><input style="width:300px" type="text" name="MAP_DATA[TITLE][<?= $i ?>]"></td>
                    <td><input style="width:300px" type="text" name="MAP_DATA[PREVIEW_URL][<?= $i ?>]"></td>
                    <td><input style="width:300px" type="text" name="MAP_DATA[TITLE_URL][<?= $i ?>]"></td>
                </tr>
            <? endfor; ?>
        </table>
    </div>
<?
$obJSPopup->StartButtons();
?>
    <input type="submit"
           onclick="window.jsAqwVideoCEOpener.saveData(serialize(document.bx_popup_form_aqw_video_map_data)); return false;"
           value="<? echo GetMessage('MYMV_SET_SUBMIT') ?>"/>
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php"); ?>