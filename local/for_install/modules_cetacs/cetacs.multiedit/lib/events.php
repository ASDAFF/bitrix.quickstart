<?
namespace Cetacs\MultiEdit;

class Events
{
    static function onAdminListDisplay(&$list)
    {
        global $APPLICATION, $USER;
        if (is_object($USER) && !$USER->IsAdmin())
            return;
        $core = new Core($list);
        if (!$core->checkTableId())
            return;
        if ($value = $core->isModifyMode()) {
            $IDS = $core->getIds();
            foreach ($IDS as $id) {
                if (!preg_match('/^E?(\d+)$/', $id, $m))
                    continue;

                $updateFields = array("IBLOCK_ID" => $core->getIblockId(), "MODIFIED_BY" => $USER->GetID());

                if (intval($_POST["PROPERTY_ID"]) > 0) {
                    \CIBlockElement::SetPropertyValuesEx($m[1], $core->getIblockId(), array($_POST["PROPERTY_ID"] => $value));
                } elseif (in_array($_POST["PROPERTY_ID"], $core::$allowEditFields)) {
                    $updateFields[$_POST["PROPERTY_ID"]] = $value;
                }

                $el = new \CIBlockElement();
                $el->Update($m[1], $updateFields, false, true, true);
            }
            LocalRedirect($APPLICATION->GetCurPageParam());
        }
        $core->initGroupOption();
        $core->showScript();
        \CJSCore::Init('file_input');
    }
}