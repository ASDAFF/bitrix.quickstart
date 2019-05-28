<?

namespace Cetacs\MultiEdit;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Core
{
    private $adminList;
    private $iblockId;
    private $IDS;
    private $checkInputName;
    private $properties = null;
    private static $allowTablesId = array("tbl_iblock_list_", "tbl_iblock_element_", "tbl_iblock_sub_element_", "tbl_product_list_");
    public static $allowEditFields = array("SORT", "PREVIEW_PICTURE", "DETAIL_PICTURE");

    function __construct(\CAdminList $list)
    {
        $this->adminList = $list;
        if ($list instanceof \CAdminSubList) {
            $this->iblockId = $GLOBALS["intSubIBlockID"];
            $this->IDS = is_array($_POST["SUB_ID"]) ? $_POST["SUB_ID"] : array();
            $this->checkInputName = "SUB_ID";
        } else {
            $this->iblockId = $_GET["IBLOCK_ID"];
            $this->IDS = is_array($GLOBALS["arID"]) ? $GLOBALS["arID"] : array();
            $this->checkInputName = "ID";
        }
    }

    function checkTableId()
    {
        foreach (self::$allowTablesId as $tableId)
            if (strpos($this->adminList->table_id, $tableId) === 0)
                return true;
        return false;
    }

    private function getPropertyList()
    {
        if ($this->properties)
            return $this->properties;
        $props = array();
        $params = array(
            "order" => array("SORT" => "ASC", "ID" => "ASC"),
            "filter" => array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iblockId)
        );
        $rsProperties = PropertyTable::getList($params);
        while ($p = $rsProperties->fetch()) {
            $props[$p["ID"]] = $p;
        }
        return $this->properties = $props;
    }

    function showScript()
    {
        // для множественного свойства типа файл
        \CJSCore::Init(array('fileinput'));
        ?>
        <script>
            window.cetacs_multiedit_dialogs = {};
            BX.ready(function () {
                var select = BX.findChild(document, {attr: {"name": "action"}}, true);
                if (select) {
                    BX.bind(select, "change", function () {
                        BX("cetacs_multiedit_props").style.display = this.value == "cetacs_multiedit" ? "block" : "none";
                    })
                }
                BX.bind(BX("cetacs_multiedit_props"), "change", function () {
                    var key = "d" + this.value;
                    top.cetacs_multiedit_dialogs[key].Show();
                });

                var btn_save = {
                    title: BX.message('JS_CORE_WINDOW_SAVE'),
                    id: 'savebtn_multiedit',
                    name: 'savebtn',
                    className: 'adm-btn-save',
                    action: function () {
                        BX("cetacs_multiedit_dialog_form").submit();
                    }
                };

                createDialog("SORT", "<?=Loc::getMessage("CETACS_MULTIEDIT_SORT")?>");
                createDialog("PREVIEW_PICTURE", "<?=Loc::getMessage("CETACS_MULTIEDIT_PREVIEW_PICTURE")?>");
                createDialog("DETAIL_PICTURE", "<?=Loc::getMessage("CETACS_MULTIEDIT_DETAIL_PICTURE")?>");
                <?foreach ($this->getPropertyList() as $p) :
                $p["NAME"] = trim(str_replace('"', '\"', $p["NAME"]));
                ?>
                createDialog("<?=$p["ID"]?>", "<?=$p["NAME"]?>");
                <? endforeach;?>

                function createDialog(id, name) {
                    var propertyId = id;
                    var key = "d" + propertyId;
                    var ajaxUrl = "/bitrix/tools/cetacs.multiedit/dialog_content.php";

                    var postData = {
                        IBLOCK_ID:<?=$this->iblockId?>,
                        PROPERTY_ID: propertyId,
                        TABLE_ID: '<?=$this->adminList->table_id?>',
                        CHECK_INPUT_NAME: '<?=$this->checkInputName?>',
                    };

                    top.cetacs_multiedit_dialogs[key] = new BX.CDialog({
                        title: name + '<?=" - " . Loc::getMessage("CETACS_MULTIEDIT_CORE_SET_VALUE")?>',
                        content_url: ajaxUrl,
                        content_post: postData,
                        draggable: true,
                        resizable: true,
                        buttons: [btn_save, BX.CDialog.btnCancel]
                    });
                }
            });
        </script>
        <?
    }

    private function getPropertiesSelectBox()
    {
        $values = array(
            "FIELDS" => array(
                "NAME" => Loc::getMessage("CETACS_MULTIEDIT_FIELDS"),
                "OPTIONS" => array(
                    "SORT" => Loc::getMessage("CETACS_MULTIEDIT_SORT"),
                    "PREVIEW_PICTURE" => Loc::getMessage("CETACS_MULTIEDIT_PREVIEW_PICTURE"),
                    "DETAIL_PICTURE" => Loc::getMessage("CETACS_MULTIEDIT_DETAIL_PICTURE")
                ),
            ),
            "PROPS" => array(
                "NAME" => Loc::getMessage("CETACS_MULTIEDIT_PROPS"),
                "OPTIONS" => array(),
            ),
        );
        foreach ($this->getPropertyList() as $prop) {
            $values["PROPS"]["OPTIONS"][$prop["ID"]] = $prop["NAME"];
        }
        return $this->selectBox($values);
    }

    private function selectBox(array $values)
    {
        $html = "<select name='a' id='cetacs_multiedit_props' style='display: none'>";
        $html .= "<option value=''>" . Loc::getMessage("CETACS_MULTIEDIT_CORE_SELECT_PROP") . "</option>";
        foreach ($values as $group) {
            $html .= "<optgroup label='{$group["NAME"]}'>";
            foreach ($group["OPTIONS"] as $id => $value) {
                $html .= "<option value='$id'>$value</option>";
            }
            $html .= "</optgroup>";
        }
        $html .= "</select>";
        return $html;
    }

    function initGroupOption()
    {
        $this->adminList->arActions["cetacs_multiedit"] = array(
            "value" => "cetacs_multiedit",
            "name" => Loc::getMessage("CETACS_MULTIEDIT_CORE_SET_VALUE_PROP"),
        );
        $this->adminList->arActions[] = array(
            "type" => "html",
            "value" => $this->getPropertiesSelectBox(),
        );
    }

    function isModifyMode()
    {
        $ok = $_SERVER["REQUEST_METHOD"] == "POST"
            && $_POST["action"] == "cetacs_multiedit_go"
            && $_POST["TABLE_ID"] == $this->adminList->table_id
            && (intval($_POST["PROPERTY_ID"]) > 0 || in_array($_POST["PROPERTY_ID"], self::$allowEditFields));
        if (!$ok)
            return false;
        $this->preparePost();
        return isset($_POST["PROP"][$_POST["PROPERTY_ID"]]) ? $_POST["PROP"][$_POST["PROPERTY_ID"]] : false;
    }

    private function preparePost()
    {
        $props = $this->getPropertyList();
        $pId = $_POST["PROPERTY_ID"];

        if (is_array($_FILES["PROP"]))
            \CFile::ConvertFilesToPost($_FILES["PROP"], $_POST["PROP"]);

        if ($props[$pId]["PROPERTY_TYPE"] == "F" && is_array($_POST["PROP"][$pId])) {
            foreach ($_POST["PROP"][$pId] as &$p) {
                $p = \CIBlock::makeFilePropArray($p);
            }
        }
        if ($pId == "PREVIEW_PICTURE" || $pId == "DETAIL_PICTURE") {
            $_POST["PROP"][$pId] = \CIBlock::makeFilePropArray($_POST["PROP"][$pId]);
            $_POST["PROP"][$pId] = $_POST["PROP"][$pId]["VALUE"];
            if ($_POST["PROP"][$pId]["error"] == 4)
                $_POST["PROP"][$pId]["del"] = "Y";
        }
    }

    function getIds()
    {
        return $this->IDS;
    }

    function getIblockId()
    {
        return $this->iblockId;
    }
}