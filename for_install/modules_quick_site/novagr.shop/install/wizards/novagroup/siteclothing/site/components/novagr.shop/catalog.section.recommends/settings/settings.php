<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'] . '/local/components/novagr.shop/catalog.section.recommends/lang/' . LANGUAGE_ID . '/settings.php');

$obJSPopup = new CJSPopup('',
    array(
        'TITLE' => GetMessage('MYMV_SET_POPUP_TITLE'),
        'SUFFIX' => 'novagr_shop_csr_map_data',
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
    <script type="text/javascript" src="/local/components/novagr.shop/catalog.section.recommends/settings/serialize-0.2.js"></script>
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
                <th width="50%"><?= GetMessage('MYMV_SET_TITLE1') ?></th>
                <th width="50%"><?= GetMessage('MYMV_SET_TITLE2') ?></th>
            </tr>
            <?
            $ITERATION = 0;
            if (is_array($arData['URL']) and is_array($arData['TITLE'])):
                ksort($arData['URL']);
                ksort($arData['TITLE']);
                foreach ($arData['URL'] as $KEY => $URL):
                    $TITLE = (isset($arData['TITLE'][$KEY])) ? $arData['TITLE'][$KEY] : "";
                    $TITLE = (strtolower(LANG_CHARSET)=='utf-8') ? $TITLE : iconv('UTF-8',LANG_CHARSET,$TITLE);
                    if (trim($URL) <> "" || trim($TITLE) <> ""):  $ITERATION = $ITERATION + 1;
                        ?>
                        <tr>
                            <td><input style="width:300px" type="text" value="<?= $URL ?>"
                                       name="MAP_DATA[URL][<?= $ITERATION ?>]"></td>

                            <td><input style="width:300px" type="text" value="<?= htmlspecialcharsbx($TITLE) ?>"
                                       name="MAP_DATA[TITLE][<?= $ITERATION ?>]"></td>
                        </tr>
                    <? endif; endforeach; endif;
            $ITERATION = $ITERATION + 1; ?>
            <? for ($i = $ITERATION; $i < ($ITERATION + 5); $i++): ?>
                <tr>
                    <td><input style="width:300px" type="text" name="MAP_DATA[URL][<?= $i ?>]"></td>
                    <td><input style="width:300px" type="text" name="MAP_DATA[TITLE][<?= $i ?>]"></td>
                </tr>
            <? endfor; ?>
        </table>
    </div>
<?
$obJSPopup->StartButtons();
?>
    <input type="submit"
           onclick="window.jsNovagrShopCSRCEOpener.saveData(serialize(document.bx_popup_form_novagr_shop_csr_map_data)); return false;"
           value="<? echo GetMessage('MYMV_SET_SUBMIT') ?>"/>
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php"); ?>