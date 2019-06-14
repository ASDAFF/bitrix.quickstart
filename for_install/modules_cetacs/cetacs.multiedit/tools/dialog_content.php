<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/iblock/admin_tools.php");

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(dirname(__FILE__) . "/../lib/core.php");

if (!Loader::includeModule("fileman"))
    exit;
if (!Loader::includeModule("iblock"))
    exit;
if (!Loader::includeModule("cetacs.multiedit"))
    exit;

$propertyId = htmlspecialcharsbx($_POST["PROPERTY_ID"]);
if (intval($propertyId) > 0) {
    $prop = CIBlockProperty::GetPropertyArray($propertyId, intval($_POST["IBLOCK_ID"]));
    if (strlen($prop["USER_TYPE_SETTINGS"]) > 0) {
        $prop["USER_TYPE_SETTINGS"] = unserialize($prop["USER_TYPE_SETTINGS"]);
    }
}

$tableId = htmlspecialcharsbx($_POST["TABLE_ID"]);
$checkInputName = htmlspecialcharsbx($_POST["CHECK_INPUT_NAME"]);

?>
    <form action="" method="post" id="cetacs_multiedit_dialog_form" enctype="multipart/form-data">
        <table class="adm-detail-content-table">
            <tr>
                <? if ($propertyId == "PREVIEW_PICTURE" || $propertyId == "DETAIL_PICTURE"): ?>
                    <td class="adm-detail-content-cell-l" width="40%">
                        <?= Loc::getMessage("CETACS_MULTIEDIT_" . $propertyId) ?>
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <?
                        echo CFileInput::Show(
                            "PROP[" . $propertyId . "]", "",
                            array(
                                "IMAGE" => "Y",
                                "PATH" => "Y",
                                "FILE_SIZE" => "Y",
                                "DIMENSIONS" => "Y",
                                "IMAGE_POPUP" => "Y",
                                "MAX_SIZE" => array(
                                    "W" => COption::GetOptionString("iblock", "detail_image_size"),
                                    "H" => COption::GetOptionString("iblock", "detail_image_size"),
                                )
                            ), array(
                                'upload' => true,
                                'medialib' => true,
                                'file_dialog' => true,
                                'cloud' => true,
                                'del' => true,
                                'description' => false
                            )
                        );
                        ?>
                    </td>
                <? elseif ($propertyId == "SORT"): ?>
                    <td class="adm-detail-content-cell-l" width="40%">
                        <?= Loc::getMessage("CETACS_MULTIEDIT_SORT") ?>
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <input type="text" value="" name="PROP[SORT]">
                    </td>
                <? else: ?>
                    <td class="adm-detail-content-cell-l" width="40%"><?= $prop["NAME"] ?></td>
                    <td class="adm-detail-content-cell-r">
                        <? _ShowPropertyField("PROP[{$prop["ID"]}]", $prop, ""); ?>
                        <? if ($prop["PROPERTY_TYPE"] == "L"): ?>
                            <input type="hidden" value="" name="PROP[<?= $prop["ID"] ?>][]">
                        <? endif; ?>
                    </td>
                <? endif; ?>
            </tr>
        </table>
        <input type="hidden" name="action" value="cetacs_multiedit_go"/>
        <input type="hidden" name="PROPERTY_ID" value="<?= $propertyId ?>"/>
        <input type="hidden" name="TABLE_ID" value="<?= $tableId ?>"/>
        <?= bitrix_sessid_post() ?>
    </form>
    <script>
        var checkInputName = '<?=$checkInputName?>[]';
        var tableId = '<?=$tableId?>';

        BX("cetacs_multiedit_dialog_form").setAttribute("action", top.location.href);

        var inputs = BX.findChild(BX(tableId), {attr: {name: checkInputName}}, true, true);
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].checked) {
                var inp = BX.create("INPUT", {attrs: {type: "hidden", name: checkInputName, value: inputs[i].value}});
                BX("cetacs_multiedit_dialog_form").appendChild(inp);
            }
        }
        if (checkInputName == "ID[]" && BX("action_target").checked) {
            var inp = BX.create("INPUT", {attrs: {type: "hidden", name: "action_target", value: "selected"}});
            BX("cetacs_multiedit_dialog_form").appendChild(inp);
        }
    </script>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");