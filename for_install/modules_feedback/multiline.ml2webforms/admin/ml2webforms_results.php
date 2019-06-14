<?php
define("ADMIN_MODULE_NAME", "multiline.ml2webforms");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__.'/menu.php');

/** @var CMain $APPLICATION */
$module_right = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if ($module_right == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!\CModule::IncludeModule(ADMIN_MODULE_NAME))
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

use \Bitrix\Main\Localization\Loc;
use \Ml2WebForms\WebFormsRequestController;
use \Ml2WebForms\WebFormResult;
use \Ml2WebForms\WebForm;
use \Ml2WebForms\MlAdminPanelBuilder;

$wfrc = new WebFormsRequestController($_GET['id']);
$webForm = $wfrc->getWebForm();
$webFormName = include __DIR__ . '/../lib/forms/' . $webForm->getId() . '/name.php';
$webFormFields = $webForm->getFields();
foreach ($webForm->fieldsVariantsLists as $field => $list) {
    $webFormFields[$field]['list'] = $list;
}
$APPLICATION->SetTitle($webFormName[LANGUAGE_ID] . ': ' . Loc::getMessage('ml2webforms_results_list_title'));

$sTableID = 'ml2webforms_results_list';
$oSort = new \CAdminSorting($sTableID, "datetime", "desc" );
$lAdmin = new \CAdminList($sTableID, $oSort);

$by = $_REQUEST['by'] ? $_REQUEST['by'] : 'datetime';
$order = in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'desc';
//$limit = (int)$_GET['SIZEN_1'] ? (int)$_GET['SIZEN_1'] : 20;
//$offset = ((int)$_GET['PAGEN_1'] ? (int)$_GET['PAGEN_1'] : 1) * $limit;
$arHeaders = Array(
    Array(
        "id" => "id",
        "content" => Loc::getMessage("ml2webforms_result_id"),
        "sort" => "id",
        "default" => true
    ),
    Array(
        "id" => "datetime",
        "content" => Loc::getMessage("ml2webforms_result_datetime"),
        "sort" => "datetime",
        "default" => true
    ),
);

foreach ($webFormFields as $field => $params) {
    $arHeaders[] = array(
        "id" => $field,
        "content" => $params['title'][LANGUAGE_ID],
        "sort" => $field,
        "default" => true
    );
}

$lAdmin->AddHeaders($arHeaders);

$filter = array();
$arOrder = array();
if ($by && $order) {
    $arOrder[$by] = $order;
}

$filterFieldsValueTypesAdapter = array(
    WebForm::FIELD_VALUE_TYPE_TEXT => 'text',
    WebForm::FIELD_VALUE_TYPE_STRING => 'string',
    WebForm::FIELD_VALUE_TYPE_INTEGER => 'integer',
    WebForm::FIELD_VALUE_TYPE_REAL => 'float',
    WebForm::FIELD_VALUE_TYPE_DATETIME => 'datetime',
    WebForm::FIELD_VALUE_TYPE_DATE => 'datetime',
);

$filterFieldsTypesAdapter = array(
    WebForm::FIELD_TYPE_TEXT => 'input_text',
    WebForm::FIELD_TYPE_SELECT => 'M:1',
    WebForm::FIELD_TYPE_TEXTAREA => 'textarea',
    WebForm::FIELD_TYPE_RADIO => 'M:1',
    WebForm::FIELD_TYPE_HIDDEN => 'input_text',
    WebForm::FIELD_TYPE_CHECKBOX => 'checkbox',
    WebForm::FIELD_TYPE_SELECT_MULTIPLE => 'M:N',
    WebForm::FIELD_TYPE_FILE => 'file',
);

$filterFieldsTypesValuesAdapter = array(
    WebForm::FIELD_TYPE_TEXT => 'varchar',
    WebForm::FIELD_TYPE_SELECT => 'M:1',
    WebForm::FIELD_TYPE_TEXTAREA => 'text',
    WebForm::FIELD_TYPE_RADIO => 'M:1',
    WebForm::FIELD_TYPE_HIDDEN => 'varchar',
    WebForm::FIELD_TYPE_CHECKBOX => 'checkbox',
    WebForm::FIELD_TYPE_SELECT_MULTIPLE => 'M:N',
    WebForm::FIELD_TYPE_FILE => 'text',
);

if ($_GET['set_filter'] === 'Y' && !isset($_GET['del_filter'])) {
    $filter = array_merge(
        $filter,
        MlAdminPanelBuilder::PrepareFilterFieldValue(
            'id',
            $_GET['f_id'],
            'int'
        )
    );
    $filter = array_merge(
        $filter,
        MlAdminPanelBuilder::PrepareFilterFieldValue(
            'datetime',
            $_GET['f_datetime'],
            'datetime'
        )
    );
    foreach($webFormFields as $field => $params) {
        if (!$params['filterable']) {
            continue;
        }

        $type = $filterFieldsTypesValuesAdapter[$params['type']];
        $filterValue = MlAdminPanelBuilder::PrepareFilterFieldValue(
            $field,
            $_GET['f_' . $field],
            $type
        );
        if ($params['type'] == WebForm::FIELD_TYPE_SELECT_MULTIPLE) {
            if (strlen(current($filterValue)) > 0) {
                $filterValue = array(
                    array(
                        'LOGIC' => 'OR',
                        array('LOGIC' => 'OR', '~' . $field => '%|' . current($filterValue) . '|%'),
                        //array('LOGIC' => 'OR', '~' . $field => current($filterValue) . '|%'),
                        //array('LOGIC' => 'OR', '~' . $field => '%|' . current($filterValue)),
                        array('LOGIC' => 'OR', $field => current($filterValue)),
                    ),
                );
            } else {
                $filterValue = array();
            }
        }
        $filter = array_merge(
            $filter,
            $filterValue
        );
    }
}

$oWebFormResults = new WebFormResult($webForm->getId(), $webForm->getExtDBCon());
$rsWebFormResults = $oWebFormResults->getList(array(
    'order' => $arOrder,
    'filter' => $filter,
    //'offset' => $offset,
    //'limit' => $limit,
), $webFormFields, true);

$rsData = new \CAdminResult($rsWebFormResults, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage('ml2webforms_results_list_title')));

while ($arRes = $rsData->fetch()) {
    $row = &$lAdmin->AddRow($arRes['id'], $arRes);

    $row->AddViewField(
        'id',
        MlAdminPanelBuilder::PrepareListHtml(
            $arRes['id'],
            'input_text'
        )
    );
    $row->AddViewField(
        'datetime',
        MlAdminPanelBuilder::PrepareListHtml(
            $arRes['datetime'],
            'input_text'
        )
    );
    foreach ($webFormFields as $field => $params) {
        switch ($params['type']) {
            case WebForm::FIELD_TYPE_FILE:
                $val = '<a href="' . $arRes[$field] . '" target="_blank">' . basename($arRes[$field]) . '</a>';
                break;
            default:
                $val = MlAdminPanelBuilder::PrepareListHtml(
                    $arRes[$field],
                    'input_text'
                );
                break;
        }
        $row->AddViewField(
            $field,
            $val
        );
    }
    $arActions = Array();

    $row->AddActions($arActions);
}

$lAdmin->AddFooter(
    array(
        array(
            "title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
            "value" => count($arForms)
        ),
        array(
            "counter" => true,
            "title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
            "value" => "0"
        ),
    )
);

$aContext = array(
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method="GET" name="find_form" id="find_form" action="<?=$APPLICATION->GetCurUri()?>">
    <?php
    $arFindFields = Array(
        'F_id' => Loc::getMessage("ml2webforms_result_id"),
        'F_datetime' => Loc::getMessage("ml2webforms_result_datetime"),
    );
    foreach ($webFormFields as $field => $params) {
        if (!$params['filterable']) {
            continue;
        }

        $arFindFields["F_{$field}"] = $params['title'][LANGUAGE_ID];
    }

    $oFilter = new \CAdminFilter($sTableID."_filter", $arFindFields);
    ?>
    <script type="text/javascript">
        var arClearHiddenFields = [];
        function applyFilter(el)
        {
            BX.adminPanel.showWait(el);
            <?=$sTableID?>_filter.OnSet('<?=\CUtil::JSEscape($sTableID)?>', '<?=\CUtil::JSEscape($APPLICATION->GetCurUri() . '&')?>');
            return false;
        }

        function deleteFilter(el)
        {
            BX.adminPanel.showWait(el);
            if (0 < arClearHiddenFields.length)
            {
                for (var index = 0; index < arClearHiddenFields.length; index++)
                {
                    if (undefined != window[arClearHiddenFields[index]])
                    {
                        if ('ClearForm' in window[arClearHiddenFields[index]])
                        {
                            window[arClearHiddenFields[index]].ClearForm();
                        }
                    }
                }
            }
            <?=$sTableID?>_filter.OnClear('<?=\CUtil::JSEscape($sTableID)?>', '<?=\CUtil::JSEscape($APPLICATION->GetCurUri() . '&')?>');
            return false;
        }
    </script>
    <?php
    $oFilter->Begin();

    echo MlAdminPanelBuilder::PrepareFilterFieldHtml(
        Loc::getMessage("ml2webforms_result_id"),
        'id',
        $_REQUEST['f_id'],
        'integer',
        'input_text'
    );

    echo MlAdminPanelBuilder::PrepareFilterFieldHtml(
        Loc::getMessage("ml2webforms_result_datetime"),
        'datetime',
        $_REQUEST['f_datetime'],
        'datetime',
        'input_text'
    );

    foreach($webFormFields as $field => $params) {
        if (!$params['filterable']) {
            continue;
        }

        $list = array();
        if (in_array($params['type'], array(WebForm::FIELD_TYPE_RADIO, WebForm::FIELD_TYPE_SELECT, WebForm::FIELD_TYPE_SELECT_MULTIPLE))) {
            foreach ($params['list'] as $itemId => $item) {
                $list[] = array('id' => $itemId, 'text' => $item['title'][LANGUAGE_ID]);
            }
        }
        echo MlAdminPanelBuilder::PrepareFilterFieldHtml(
            $params['title'][LANGUAGE_ID],
            $field,
            $_REQUEST['f_' . $field],
            $filterFieldsValueTypesAdapter[$params['value_type']],
            $filterFieldsTypesAdapter[$params['type']],
            $list
        );
    }

    $oFilter->Buttons();
    ?>
    <span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="set_filter" value="<?=Loc::getMessage("admin_lib_filter_set_butt"); ?>" title="<?=Loc::getMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return applyFilter(this);"></span>
    <span class="adm-btn-wrap"><input type="submit"  class="adm-btn" name="del_filter" value="<?=Loc::getMessage("admin_lib_filter_clear_butt"); ?>" title="<?=Loc::getMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="deleteFilter(this); return false;"></span>
    <?php
    $oFilter->End();
    ?>
</form>
<?php

$lAdmin->DisplayList();

if ($note = Loc::getMessage("ml2webforms_results_list_note")) {
    echo BeginNote();
    echo $note;
    echo EndNote();
}


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
