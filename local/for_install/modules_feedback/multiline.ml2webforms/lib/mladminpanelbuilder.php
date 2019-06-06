<?php
/**
 * Created by Multiline / A M I O.
 * User: r.panfilov@amio.ru
 * Date: 07.10.14
 * Time: 17:00
 */

namespace Ml2WebForms;

IncludeModuleLangFile(__FILE__);

use \Bitrix\Main;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type as FieldType;
use \Bitrix\Main\Entity;

/**
 * Class MlAdminPanelBuilder Toolkit for simply build admin pages
 * @package Ml2WebForms
 */
class MlAdminPanelBuilder {
    public static $variants_cache = array();
    public static $module_cfg = array();
    public static $module_right = '';

    public static $fieldTypes = array(
        'boolean' => array('checkbox'),
        'date' => array('datetime', 'input_text'),
        'datetime' => array('datetime', 'input_text'),
        'enum' => array('M:1'),
        'float' => array('input_text'),
        'integer' => array('input_text', 'checkbox', 'M:1', 'image', 'file', '1:M', 'M:N'),
        'string' => array('input_text', 'M:1', 'datetime', 'expression'),
        'text' => array('text', 'html'),
    );

    public static $fieldViews = array(
        'input_text' => array('date', 'datetime', 'float', 'integer', 'string'),
        'checkbox' => array('boolean', 'integer'),
        'datetime' => array('date', 'datetime', 'string'),
        'M:1' => array('enum', 'integer', 'string'),
        'image' => array('integer'),
        'file' => array('integer'),
        '1:M' => array('integer'),
        'M:N' => array('integer'),
        'text' => array('text'),
        'html' => array('text'),
        'expression' => array('string'),
    );

    public static function buildAdminPage($module_name, &$module_cfg) {
        // TODO: change to new core without $GLOBALS when it will be able
        global $APPLICATION;

        self::$module_cfg = &$module_cfg;

        self::$module_right = $APPLICATION->GetGroupRight($module_name);
        if (self::$module_right == "D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

        if (!\CModule::IncludeModule($module_name))
        {
            $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
        }

        if ($_REQUEST['sect'] == 'import') {
            if (!defined("MODULE_IMPORT_CLASS")) {
                $APPLICATION->ThrowException('Required to define constant "MODULE_IMPORT_CLASS" with import class name like "MyModuleImport" at ' . $module_name . '_admin.php');
            }
            self::buildAdminImportPage($module_name);
        } else {

            $entity_name = $_REQUEST['entity'];
            if (!isset(self::$module_cfg[$entity_name])) {
                $APPLICATION->ThrowException('Entity "' . $entity_name . '" not found');
            }

            switch ($_REQUEST['sect']) {
                case 'list':
                    self::buildAdminListPage($module_name, $entity_name);
                    break;
                case 'edit':
                    self::buildAdminEditPage($module_name, $entity_name);
                    break;
            }
        }
    }

    public static function buildAdminListPage($module_name, $entity_name) {
        // TODO: change to new core without $GLOBALS when it will be able
        global $APPLICATION, $adminPage, $adminMenu, $USER, $adminChain, $SiteExpireDate;

        $entity_code = strtolower($entity_name);

        $APPLICATION->SetTitle(Loc::getMessage($module_name . '_' . $entity_code . '_list_title'));

        /** @var \Bitrix\Main\Entity\DataManager $entityTable */
        $entityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $entity_name . 'Table';
        $fieldsConfig = &self::$module_cfg[$entity_name];
        $fieldsMap = $entityTable::getMap();
        $sTableID = $entityTable::getTableName();

        $first_field = is_numeric(array_shift(array_keys($fieldsConfig['fields']))) ? array_shift(array_values($fieldsConfig['fields'])) : array_shift(array_keys($fieldsConfig['fields']));
        $by = $_REQUEST['by'] ? $_REQUEST['by'] : $first_field;
        $order = in_array(strtoupper($_REQUEST['order']), array('ASC', 'DESC')) ? strtoupper($_REQUEST['order']) : 'ASC';

        $oSort = new \CAdminSorting($sTableID, $by, $order);
        $lAdmin = new \CAdminList($sTableID, $oSort);

        $arFilter = array();

        if ($_REQUEST['set_filter'] == 'Y') {
            foreach($fieldsConfig['filterable'] as $sField) {
                $data_type = '';
                $entityField = self::getFieldFromMap($sField, $fieldsMap);
                if ($entityField) {
                    $data_type = self::getFieldType($entityField);
                }
                if (!isset($fieldsConfig['fields'][$sField]['type'])) {
                    $fieldsConfig['fields'][$sField]['type'] = $data_type;
                }

                $arFilter = array_merge(
                    $arFilter,
                    self::PrepareFilterFieldValue(
                        $sField,
                        $_REQUEST['f_' . $sField],
                        isset($fieldsConfig['fields'][$sField]['type']) ? $fieldsConfig['fields'][$sField]['type'] : '',
                        isset($fieldsConfig['fields'][$sField]['from']) ? $fieldsConfig['fields'][$sField]['from'] : ''
                    )
                );
            }
        }

        if (self::$module_right == "W" && $arID = $lAdmin->GroupAction()) {
            if ($_REQUEST['action_target'] == 'selected') {
                $rsData = $entityTable::getList(array(
                    'filter' => $arFilter,
                    'order' => array($by => $order),
                    'select' => array('ID')
                ));

                while ($arRes = $rsData->fetch()) {
                    $arID[] = $arRes['ID'];
                }
            }

            foreach ($arID as $ID) {
                if (strlen($ID) <= 0)
                    continue;

                switch($_REQUEST['action']) {
                    case "delete":
                        if (self::$module_right == "W")
                            if (!$entityTable::delete($ID)) {
                                if ($ex = $APPLICATION->GetException())
                                    $lAdmin->AddGroupError($ex->GetString(), $ID);
                                else
                                    $lAdmin->AddGroupError(Loc::getMessage("{$module_name}_{$entity_code}_delete_err") ? Loc::getMessage("{$module_name}_{$entity_code}_delete_err") : Loc::getMessage("mladminpanelbuilder_delete_err"), $ID);
                            }
                        break;
                }
            }
        }

        $arSelect = array();
        $arRuntime = array();
        foreach ($fieldsConfig['fields'] as $fCode => $fData) {
            if (is_array($fData)) {
                if (isset($fData['from'])) {
                    switch ($fData['type']) {
                        case '1:M':
                            $arSelect[] = $fCode;
                            /** @var \Bitrix\Main\Entity\DataManager $runtimeEntityTable */
                            $runtimeEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $fData['from']['entity'] . 'Table';
                            $arRuntime[] = new Entity\ExpressionField($fCode, '(SELECT GROUP_CONCAT(`' . $runtimeEntityTable::getTableName() . '`.`' . $fData['from']['field'] . '` SEPARATOR \', \') FROM `' . $runtimeEntityTable::getTableName() . '` WHERE `' . $runtimeEntityTable::getTableName() . '`.`' . $fData['from']['reference'] . '` = `' . $entityTable::getTableName() . '`.`ID` GROUP BY `' . $entityTable::getTableName() . '`.`ID`)');
                            break;
                        case 'M:N':
                            $arSelect[] = $fCode;
                            /**
                             * @var \Bitrix\Main\Entity\DataManager $runtimeEntityTable
                             * @var \Bitrix\Main\Entity\DataManager $referenceEntityTable
                             */
                            $runtimeEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $fData['from']['entity'] . 'Table';
                            $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $fData['from']['referenceEntity'] . 'Table';
                            $arRuntime[] = new Entity\ExpressionField(
                                $fCode,
                                '(
                                SELECT
                                    GROUP_CONCAT(tbl_childs.`' . $fData['from']['field'] . '` SEPARATOR \', \')
                                FROM
                                    `' . $referenceEntityTable::getTableName() . '`
                                LEFT JOIN
                                    `' . $runtimeEntityTable::getTableName() . '` tbl_childs
                                    ON
                                        tbl_childs.`ID` = `' . $referenceEntityTable::getTableName() . '`.`' . $fData['from']['joined'] . '`
                                WHERE
                                    `' . $referenceEntityTable::getTableName() . '`.`' . $fData['from']['reference'] . '` = `' . $entityTable::getTableName() . '`.`ID`
                                GROUP BY
                                    `' . $entityTable::getTableName() . '`.`ID`
                                )'
                            );
                            break;
                        case 'M:1':
                            if (is_array($fData['from'])) {
                                $arSelect[$fData['from']['reference'] . '_' . $fData['from']['field']] = $fData['from']['reference'] . '.' . $fData['from']['field'];
                            } elseif (is_array($fData['list'])) {
                                $arSelect[] = $fCode;
                                /*$rtsql = '
                                    (CASE ';
                                foreach ($fData['list'] as $lrow) {
                                    $rtsql .= '
                                        WHEN `' . $entityTable::getTableName() . '`.`' . $fCode . '` = "' . $lrow['id'] . '" THEN "' . $lrow['text'] . '"';
                                }
                                $rtsql .= '
                                        ELSE ""
                                    END)';
                                $arRuntime[] = new Entity\ExpressionField($fCode, $rtsql);*/
                            }
                            break;
                    }
                } else {
                    $arSelect[] = $fCode;
                }
            } else {
                $arSelect[] = $fData;
            }
        }
        $rsData = $entityTable::getList(array(
            'filter' => $arFilter,
            'order' => array($by => $order),
            'select' => $arSelect,
            'runtime' => $arRuntime,
        ));

        $rsData = new \CAdminResult($rsData, $sTableID);
        $rsData->NavStart();

        $lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage($module_name . '_' . $entity_code . '_list_title')));

        $arHeaders = Array();

        foreach($fieldsConfig['fields'] as $fCode => $arFieldData) {
            if (!is_array($arFieldData)) {
                $fCode = $arFieldData;
            }
            if ($arFieldData['type'] == 'M:1' && is_array($arFieldData['from'])) {
                $fResCode = $arFieldData['from']['reference'] . '_' . $arFieldData['from']['field'];
            } else {
                $fResCode = $fCode;
            }
            $arHeaders[] = Array(
                "id" => $fResCode,
                "content" => Loc::getMessage($module_name . '_' . $entity_code . '_' . strtolower($fCode)),
                "sort" => $fResCode,
                "default" => true
            );
        }

        $lAdmin->AddHeaders($arHeaders);

        while ($arRes = $rsData->fetch()) {
            $row = &$lAdmin->AddRow($arRes['ID'], $arRes);

            foreach($fieldsConfig['fields'] as $fCode => &$arFieldParams) {
                if (!is_array($arFieldParams)) {
                    $fCode = $arFieldParams;
                    $arFieldParams = array();
                }
                if ($arFieldParams['type'] == 'M:1' && is_array($arFieldParams['from'])) {
                    $fResCode = $arFieldParams['from']['reference'] . '_' . $arFieldParams['from']['field'];
                } else {
                    $fResCode = $fCode;
                }

                $val = $arRes[$fResCode];
                if ($arFieldParams['type'] == 'M:1' && is_array($arFieldParams['list'])) {
                    foreach ($arFieldParams['list'] as $lrow) {
                        if ($lrow['id'] == $arRes[$fResCode]) {
                            $val = $lrow['text'];
                        }
                    }
                }

                $row->AddViewField(
                    $fCode,
                    self::PrepareListHtml(
                        $val,
                        isset($arFieldParams['type']) ? ($arFieldParams['type'] == '1:M' ? $arFieldParams['from']['type'] : $arFieldParams['type']) : ''
                    )
                );
            }

            $arActions = Array();

            $arActions[] = array(
                "ICON" => "edit",
                "DEFAULT" => "Y",
                "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
                "ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage() . "?sect=edit&entity={$entity_name}&ID=" . $arRes['ID'] . "&lang=" . LANG ),
            );

            if (self::$module_right == "W") {
                $arActions[] = array("SEPARATOR" => true);

                $arActions[] = array(
                    "ICON" => "delete",
                    "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
                    "ACTION" => "if(confirm('" . Loc::getMessage('CONFIRM_DEL_MESSAGE') . "')) " . $lAdmin->ActionDoGroup($arRes['ID'], "delete", "sect=list&entity={$entity_name}"),
                );
            }

            $row->AddActions($arActions);
        }

        $lAdmin->AddFooter(
            array(
                array(
                    "title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
                    "value" => $rsData->SelectedRowsCount()
                ),
                array(
                    "counter" => true,
                    "title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
                    "value" => "0"
                ),
            )
        );

        if (self::$module_right == "W") {
            $lAdmin->AddGroupActionTable(
                array(
                    "delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
                )
            );
        }

        $aContext = array(
            array(
                "ICON" => "btn_new",
                "TEXT" => Loc::getMessage("{$module_name}_{$entity_code}_add") ? Loc::getMessage("{$module_name}_{$entity_code}_add") : Loc::getMessage("mladminpanelbuilder_add"),
                "LINK" => $APPLICATION->GetCurPage() . "?sect=edit&entity={$entity_name}&lang=" . LANG,
                "TITLE" => Loc::getMessage("{$module_name}_{$entity_code}_add") ? Loc::getMessage("{$module_name}_{$entity_code}_add") : Loc::getMessage("mladminpanelbuilder_add"),
            ),
        );

        $lAdmin->AddAdminContextMenu($aContext);

        $lAdmin->CheckListMode();

        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

        if (@count($fieldsConfig['filterable']) > 0) {
            ?>
            <form method="GET" name="find_form" id="find_form" action="<?=$APPLICATION->GetCurUri()?>">
            <?php
            $arFindFields = Array();
            foreach ($fieldsConfig['filterable'] as $sField) {
                $arFindFields["F_{$sField}"] = Loc::getMessage("{$module_name}_{$entity_code}_" . strtolower($sField));
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

            foreach($fieldsConfig['filterable'] as $sField) {
                $data_type = '';
                $entityField = self::getFieldFromMap($sField, $fieldsMap);
                if ($entityField) {
                    $data_type = self::getFieldType($entityField);
                }
                echo self::PrepareFilterFieldHtml(
                    Loc::getMessage("{$module_name}_{$entity_code}_" . strtolower($sField)),
                    $sField,
                    $_REQUEST['f_' . $sField],
                    $data_type,
                    $fieldsConfig['fields'][$sField]['type'],
                    $fieldsConfig['fields'][$sField]['from']
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
        }

        $lAdmin->DisplayList();

        if ($note = Loc::getMessage("{$module_name}_{$entity_code}_list_note")) {
            echo BeginNote();
            echo $note;
            echo EndNote();
        }
    }

    public static function buildAdminEditPage($module_name, $entity_name) {
        // TODO: change to new core without $GLOBALS when it will be able
        global $APPLICATION, $adminPage, $adminMenu, $USER, $adminChain, $SiteExpireDate, $message, $bVarsFromForm;

        $ID = (int)$_REQUEST['ID'];

        $entity_code = strtolower($entity_name);

        $APPLICATION->SetTitle(Loc::getMessage($module_name . '_' . $entity_code . '_' . ($ID ? 'edit' : 'add') . '_title'));
        Asset::getInstance()->addJs('/bitrix/js/main/jquery/jquery-1.8.3.min.js');
        Asset::getInstance()->addString('<style type="text/css">.adm-detail-content-cell-l {width: 48% !important;}</style>');

        $arSettings = &self::$module_cfg[$entity_name];

        /** @var \Bitrix\Main\Entity\DataManager $entity_class */
        $entity_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $entity_name . 'Table';
        $fieldsMap = $entity_class::getMap();

        Asset::getInstance()->addString(self::getAdminEditJS());

        $message = null;
        $bVarsFromForm = false;

        $aTabs = array();
        if (@count($arSettings['tabs']) > 0) {
            foreach($arSettings['tabs'] as $sTabCode => &$arTabParams) {
                $aTabs[] = array(
                    "DIV" => "tab_edit_{$entity_code}_{$sTabCode}",
                    "TAB" => Loc::getMessage("{$module_name}_{$entity_code}_{$sTabCode}"),
                    "ICON" => "main_user_edit",
                    "TITLE" => Loc::getMessage("{$module_name}_{$entity_code}_{$sTabCode}_settings"),
                );
            }
        } else {
            $aTabs[] = array(
                "DIV" => "tab_edit_mladminpanelbuilder_general",
                "TAB" => Loc::getMessage("mladminpanelbuilder_general"),
                "ICON" => "main_user_edit",
                "TITLE" => Loc::getMessage("mladminpanelbuilder_general_settings"),
            );
            $arSettings['tabs'] = array(
                'general' => array_keys($arSettings['fields']),
            );
        }

        $tabControl = new \CAdminTabControl("tabControl", $aTabs);

        $arItemData = array();
        if ($ID && !$bVarsFromForm) {
            $arItemData = $entity_class::getById($ID)->fetch();
            foreach($arSettings['fields'] as $sField => $arFieldParams) {
                if (@$arFieldParams['type'] == 'M:N') {
                    /** @var \Bitrix\Main\Entity\DataManager $childEntityTable */
                    $childEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $arFieldParams['from']['referenceEntity'] . 'Table';
                    $rsValues = $childEntityTable::getList(array(
                        'select' => array($arFieldParams['from']['joined']),
                        'filter' => array($arFieldParams['from']['reference'] => $ID)
                    ));
                    $arValues = array();
                    while($arValue = $rsValues->fetch()) {
                        $arValues[] = (int)$arValue[$arFieldParams['from']['joined']];
                    }

                    $arItemData[$sField] = $arValues;
                }

                if (@$arFieldParams['type'] == '1:M' && $ID) {
                    /** @var \Bitrix\Main\Entity\DataManager $childEntityTable */
                    $childEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $arFieldParams['from']['entity'] . 'Table';
                    $rsValues = $childEntityTable::getList(array(
                        'select' => array('*'),
                        'filter' => array($arFieldParams['from']['reference'] => $ID),
                        'order' => array(@$arFieldParams['from']['sort'] ? $arFieldParams['from']['sort'] : $arFieldParams['from']['field'] => 'ASC'),
                    ));
                    $arValues = array();
                    while($arValue = $rsValues->fetch()) {
                        $arValues[$arValue['ID']] = $arValue;
                    }

                    $arItemData[$sField] = $arValues;
                }
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && self::$module_right == "W" && strlen($_REQUEST['Update']) > 0 && check_bitrix_sessid()) {
            $arFields = array();
            $arFieldsMultiple = array();

            foreach ($arSettings['fields'] as $sFieldCode => $arFieldParams) {
                if (is_numeric($sFieldCode)) {
                    $sFieldCode = $arFieldParams;
                    $arFieldParams = array();
                }

                if ($sFieldCode === 'ID' || @$arFieldParams['edit_skip'])
                    continue;

                if (in_array($arFieldParams['type'], array('file', 'image')) && is_array($_FILES[$sFieldCode]) && !$_FILES[$sFieldCode]['error'] && $_FILES[$sFieldCode]['tmp_name'] != '') {
                    $_FILES[$sFieldCode]['MODULE_ID'] = $module_name;
                    $_FILES[$sFieldCode]['old_file'] = @$arItemData[$sFieldCode];
                    $_FILES[$sFieldCode]['del'] = 'Y';
                    $oFile = new \CFile();
                    $_POST[$sFieldCode] = $oFile->SaveFile($_FILES[$sFieldCode], '/' . $module_name . '/' . $entity_name);
                }

                if ($_POST[$sFieldCode . '_del'] == 'Y' && (int)@$arItemData[$sFieldCode] > 0) {
                    $oFile = new \CFile();
                    $oFile->Delete(@$arItemData[$sFieldCode]);
                }

                if (!(in_array($arFieldParams['type'], array('file', 'image')) && !$_POST[$sFieldCode] && !$_POST[$sFieldCode . '_del'])) {
                    $data_type = '';
                    $entityField = self::getFieldFromMap($sFieldCode, $fieldsMap);
                    if ($entityField) {
                        $data_type = self::getFieldType($entityField);
                    }

                    $value = self::PrepareFieldValue($_POST[$sFieldCode], $arFieldParams['type'], $data_type);

                    if (is_array($value)) {
                        $arFieldsMultiple[$sFieldCode] = $value;
                    } else {
                        $arFields[$sFieldCode] = $value;
                    }
                }
            }


            $arMsg = Array();
            if ($ID) {
                $entity_class::update($ID, $arFields);
            } else {
                $res = $entity_class::add($arFields);
                $ID = $res->getId();
                if (!$ID) { var_dump($res->getErrorMessages());die(''); }
            }

            $arFields['ID'] = $ID;

            foreach ($arFieldsMultiple as $sField => $arValue) {
                if (@$arSettings['fields'][$sField]['type'] == 'M:N') {
                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                    $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $arSettings['fields'][$sField]['from']['referenceEntity'] . 'Table';
                    $sMultipleTable = $referenceEntityTable::getTableName();
                    $sLeftField = $arSettings['fields'][$sField]['from']['reference'];
                    $sRightField = $arSettings['fields'][$sField]['from']['joined'];
                    Application::getConnection()->query("
                        DELETE FROM
                            `{$sMultipleTable}`
                        WHERE
                            `{$sLeftField}` = {$ID}
                    ");
                    foreach($arValue as $val) {
                        Application::getConnection()->query("
                            INSERT INTO
                                `{$sMultipleTable}`
                            SET
                                `{$sLeftField}` = {$ID},
                                `{$sRightField}` = {$val}
                        ");
                    }
                }
                if (@$arSettings['fields'][$sField]['type'] == '1:M') {
                    $arFieldParams = &$arSettings['fields'][$sField];
                    $entity_o2m_code = $arFieldParams['from']['entity'];
                    $entity_o2m_cfg = &self::$module_cfg[$entity_o2m_code];
                    /** @var \Bitrix\Main\Entity\DataManager $entity_o2m_class */
                    $entity_o2m_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $entity_o2m_code . 'Table';
                    $entity_o2m_fieldsMap = $entity_o2m_class::getMap();
                    $sLeft = 'ID';
                    $sRight = $arFieldParams['from']['reference'];

                    foreach ($arValue as $vID => $arValueFields) {
                        if (!$arValueFields['mladminpanelbuilder_system_delete']) {
                            $vNewValue = array();
                            foreach ($entity_o2m_cfg['fields'] as $childField => $sValueFieldParams) {
                                if (is_numeric($childField)) {
                                    $childField = $sValueFieldParams;
                                    $sValueFieldParams = array();
                                }

                                if ($childField === 'ID' || $childField === $sRight || (!isset($arValueFields[$childField]) && $sValueFieldParams['type'] != 'checkbox') && !isset($_FILES[$sField]['error'][$vID][$childField]))
                                    continue;

                                if (in_array($sValueFieldParams['type'], array('file', 'image')) && !$_FILES[$childField]['error'][$vID] && $_FILES[$sField]['name'][$vID][$childField]) {
                                    $arFile = array(
                                        'MODULE_ID' => $module_name,
                                        'name' => $_FILES[$sField]['name'][$vID][$childField],
                                        'type' => $_FILES[$sField]['type'][$vID][$childField],
                                        'tmp_name' => $_FILES[$sField]['tmp_name'][$vID][$childField],
                                        'error' => $_FILES[$sField]['error'][$vID][$childField],
                                        'size' => $_FILES[$sField]['size'][$vID][$childField],
                                        'old_file' => @$arItemData[$sField][$vID][$childField],
                                        'del' => 'Y',
                                    );

                                    $oFile = new \CFile();
                                    $arValueFields[$childField] = $oFile->SaveFile($arFile, '/' . $module_name . '/' . $entity_o2m_code);
                                }

                                if ($_POST[$sField . '_del'][$vID][$childField] == 'Y' && (int)@$arItemData[$sField][$vID][$childField] > 0) {
                                    $oFile = new \CFile();
                                    $oFile->Delete(@$arItemData[$sField][$vID][$childField]);
                                }

                                if (!(in_array($sValueFieldParams['type'], array('file', 'image')) && !$arValueFields[$childField] && !$_POST[$sField . '_del'][$vID][$childField]) || $sValueFieldParams['type'] == 'checkbox') {
                                    $data_type = '';
                                    $childEntityField = self::getFieldFromMap($childField, $entity_o2m_fieldsMap);
                                    if ($childEntityField) {
                                        $data_type = self::getFieldType($childEntityField);
                                    }
                                    $vNewValue[$childField] = self::PrepareFieldValue($arValueFields[$childField], $sValueFieldParams['type'], $data_type);
                                }
                            }

                            if ($vID === 'new' && $arValueFields['mladminpanelbuilder_system_add']) {
                                $vNewValue[$sRight] = $arFields[$sLeft];
                                $entity_o2m_class::add($vNewValue);
                            } else {
                                $entity_o2m_class::update($vID, $vNewValue);
                            }
                        } else {
                            $entity_o2m_class::delete((int)$vID);
                        }
                    }
                }
            }

            if (strlen($_REQUEST['apply']) <= 0)
                LocalRedirect($APPLICATION->GetCurPage() . "?sect=list&entity={$entity_name}&lang=" . LANG );

            LocalRedirect($APPLICATION->GetCurPage() . "?sect=edit&entity={$entity_name}&ID={$ID}&tabControl_active_tab=" . $_REQUEST['tabControl_active_tab'] . "&lang=" . LANG);
        }

        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

        $aContext = array(
            array(
                "ICON" => "btn_list",
                "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_LIST"),
                "LINK" => $APPLICATION->GetCurPage() . "?sect=list&entity={$entity_name}&lang=".LANG,
                "TITLE" => Loc::getMessage("MAIN_ADMIN_MENU_LIST")
            ),
        );

        if ($ID) {
            $aContext[] = array(
                "ICON" => "btn_new",
                "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_CREATE"),
                "LINK" => $APPLICATION->GetCurPage() . "?sect=edit&entity={$entity_name}&lang=".LANG,
                "TITLE" => Loc::getMessage("MAIN_ADMIN_MENU_CREATE")
            );

            if ( self::$module_right == "W" ) {
                $aContext[] = array(
                    "ICON" => "btn_delete",
                    "TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
                    "LINK" => "javascript:if(confirm('" . Loc::getMessage("CONFIRM_DEL_MESSAGE") . "'))window.location='" . $APPLICATION->GetCurPage() . "?sect=edit&entity={$entity_name}&action=delete&ID[]={$ID}&lang=" . LANG . "&" . bitrix_sessid_get() . "';",
                );
            }
        }
        $context = new \CAdminContextMenu($aContext);
        $context->Show();

        if ($message)
            echo $message->Show();
        ?>
        <?/*<style type="text/css">
            .adm-input-file-new .adm-input {
                width: 110px;
            }
            .adm-input-file-new .adm-input-file {
                width: 78px;
            }
        </style>*/?>
        <form method="post" action="<?=$APPLICATION->GetCurUri()?>" name="form1_<?=$entity_name?>" id="form1_<?=$entity_name?>" enctype="multipart/form-data">
            <?=bitrix_sessid_post()?>
            <?echo GetFilterHiddens("filter_");?>
            <input type="hidden" name="id" value="<?echo $ID?>">
            <input type="hidden" name="Update" value="Y">
            <input type="hidden" name="from" value="<?echo htmlspecialchars($_REQUEST['from'])?>">
            <?if (strlen($_REQUEST['return_url']) > 0):?><input type="hidden" name="return_url" value="<?=htmlspecialchars($_REQUEST['return_url'])?>"><?endif?>
            <?
            $tabControl->Begin();

            foreach ($arSettings['tabs'] as &$arTabFields) {
                $tabControl->BeginNextTab();

                foreach ($arTabFields as &$sField) {
                    if (isset($arSettings['fields'][$sField]['edit_skip']))
                        continue;

                    if ($sField == 'ID') {
                        if ($ID) {
                            ?>
                            <tr>
                                <td width="50%"><?=Loc::getMessage("{$module_name}_{$entity_code}_" . strtolower($sField))?>:</td>
                                <td width="50%">
                                    <?=$ID?>
                                </td>
                            </tr>
                        <?
                        }
                    } else {
                        echo self::PrepareFieldHtml(
                            Loc::getMessage("{$module_name}_{$entity_code}_" . strtolower($sField)),
                            $sField,
                            $arItemData[$sField],
                            @$arSettings['fields'][$sField]['type'],
                            @$arSettings['fields'][$sField]['from'],
                            @$arSettings['fields'][$sField]['list'],
                            @$arSettings['fields'][$sField]['size'],
                            @$arSettings['fields'][$sField]['required'],
                            isset($arSettings['fields'][$sField]['readonly']) ? $arSettings['fields'][$sField]['readonly'] : false
                        );
                    }
                }

                $tabControl->EndTab();
            }

            $tabControl->Buttons(
                array(
                    "disabled" => self::$module_right < "W",
                    "back_url" => $APPLICATION->GetCurPage() . "?sect=list&entity={$entity_name}&lang=".LANG
                )
            );

            $tabControl->End();
            ?>
        </form>
        <?
        $tabControl->ShowWarnings("form1_{$entity_name}", $message);

        if ($note = Loc::getMessage("{$module_name}_{$entity_code}_edit_note")) {
            echo BeginNote();
            echo $note;
            echo EndNote();
        }
    }

    public static function getFieldFromMap($field, &$fieldsMap) {
        /** @var Entity\Field $fieldObj */
        foreach ($fieldsMap as &$fieldObj) {
            if ($fieldObj->getName() == $field) {
                return $fieldObj;
            }
        }

        return false;
    }

    public static function getFieldType(Entity\Field $field) {
        if ($field instanceof Entity\IntegerField)
            return 'integer';
        if ($field instanceof Entity\DatetimeField)
            return 'datetime';
        if ($field instanceof Entity\DateField)
            return 'date';
        if ($field instanceof Entity\BooleanField)
            return 'boolean';
        if ($field instanceof Entity\FloatField)
            return 'float';
        if ($field instanceof Entity\EnumField)
            return 'enum';
        if ($field instanceof Entity\TextField)
            return 'text';
        if ($field instanceof Entity\StringField)
            return 'string';
        if ($field instanceof Entity\ExpressionField)
            return 'expression';
        if ($field instanceof Entity\ReferenceField)
            return 'reference';
    }

    public static function buildAdminImportPage($module_name) {
        // TODO: change to new core without $GLOBALS when it will be able
        global $APPLICATION, $adminPage, $adminMenu, $USER, $adminChain, $SiteExpireDate, $message, $bVarsFromForm;
        if (isset($_REQUEST['go_import'])) {
            /** @var \Ml2WebForms\MlAdminPanelBuilderImport $xml_parser */
            $xml_parser = null;
            eval('$xml_parser = new ' . MODULE_IMPORT_CLASS . '(self::$module_cfg);');
            $xml_parser->_setLogParams(array(
                'enable_log' => true,
                'save_log' => true,
                'save_html' => false,
                'log_dir' => $_SERVER['DOCUMENT_ROOT'] . '/upload/logs',
                'send_log' => false,
                'send_html' => false,
                'log_recipient' => LOGS_RECIPIENT,
                'log_from' => LOGS_FROM,
            ));
            if ($_REQUEST['show_progress'] == 'Y') {
                $xml_parser->_showProgress(true, $_REQUEST['show_progress_script'] == 'Y');
            }
            $xml_parser->_import();
            exit();
        } else {
            $aTabs = array(
                array("DIV" => "edit1", "TAB" => Loc::getMessage("{$module_name}_import_tab"), "ICON"=>"main_user_edit", "TITLE"=> Loc::getMessage("{$module_name}_import_tab_settings")),
            );
            $tabControl = new \CAdminTabControl("tabControl", $aTabs);

            $APPLICATION->SetTitle(Loc::getMessage("{$module_name}_import_title"));
            $APPLICATION->AddHeadScript('/bitrix/js/main/jquery/jquery-1.8.3.min.js');
            require( $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php" );
            ?>

        <script type="text/javascript">
            var MLFactoryImport = {
                preloaderTO: null,
                updateProgress: function(progress) {
                    clearInterval(this.preloaderTO);
                    var percent = Math.round(progress.percent * 100) / 100;
                    $('#mlfactory_import_progress .adm-info-message > p > b').html(progress.message);
                    var inner = $('#mlfactory_import_progress .adm-progress-bar-inner');
                    inner.find('.adm-progress-bar-inner-text').text(percent + '%');
                    inner.width(Math.round(496 * percent / 100));
                    $('.adm-progress-bar-outer').html('').append(inner).append(percent + '%');
                    if (!progress.complete) {
                        //$('#mlfactory_import_form input[name="progress"]').val(JSON.stringify(progress));
                        //$('#mlfactory_import_form').submit();
                        this.initPreloader();
                    } else {
                        $('#mlfactory_import_start_btn').attr('disabled', false);
                    }
                },
                initPreloader: function() {
                    this.preloaderTO = setInterval(function(){
                        $('#mlfactory_import_status_frame').attr('src', '/upload/tmp/mlfactory_import_' + $('#import_id').val() + '.html?r=' + Math.random());
                    }, 3000);
                }
            };

            $(function(){
                $('#mlfactory_import_start_btn').on('click', function(){
                    $('#mlfactory_import_prompt').hide();
                    $('#mlfactory_import_progress').show();
                    $(this).attr('disabled', 'disabled');
                    $('#mlfactory_import_form').submit();
                    MLFactoryImport.initPreloader();
                });
            });
        </script>
        <style type="text/css">
            #mlfactory_import_progress {
                display: none;
            }
            #mlfactory_import_frame, #mlfactory_import_status_frame {
                width: 0;
                height: 0;
                border: 0;
            }
        </style>
        <?
        $formAction = $APPLICATION->GetCurUri();
        ?>
        <form method="post" action="<?=$formAction?>" name="form1" target="mlfactory_import_frame" id="mlfactory_import_form">
            <?=bitrix_sessid_post()?>
            <?echo GetFilterHiddens("filter_");?>
            <?
            foreach ($_REQUEST as $key => $val) {
                ?><input type="hidden" name="<?=$key?>" value="<?=$val?>"><?
            }
            ?>
            <input type="hidden" id="import_id" name="import_id" value="<?=md5(date('YmdHis') . rand(0, 1000))?>">
            <input type="hidden" name="go_import" value="Y">
            <input type="hidden" name="show_progress" value="Y">
            <input type="hidden" name="progress" value="">
            <input type="hidden" name="show_progress_script" value="Y">
            <input type="hidden" name="from" value="<?echo htmlspecialchars($_REQUEST['from'])?>">
            <?if(strlen($_REQUEST['return_url'])>0):?><input type="hidden" name="return_url" value="<?=htmlspecialchars($_REQUEST['return_url'])?>"><?endif?>

            <?$tabControl->Begin();?>
            <?$tabControl->BeginNextTab();?>
                <div id="mlfactory_import_prompt"><?=Loc::getMessage("{$module_name}_import_prompt")?></div>
                <div id="mlfactory_import_progress"><?$oAdminMessage = new \CAdminMessage('');
                    $oAdminMessage->ShowMessage(array(
                        "TYPE" => "PROGRESS",
                        "DETAILS" => '<p><b>' . Loc::getMessage("{$module_name}_import_progress") . '</b></p>#PROGRESS_BAR#',
                        "HTML" => true,
                        "PROGRESS_TOTAL" => 100,
                        "PROGRESS_VALUE" => 0,
                    ));?></div>
            <?$tabControl->EndTab();?>
            <?$tabControl->Buttons();?>
            <input id="mlfactory_import_start_btn" class="adm-btn-save" type="submit" name="do_import" value="<?=Loc::getMessage("{$module_name}_go_import")?>" />
            <?$tabControl->End();?>
        </form>
        <iframe id="mlfactory_import_frame" name="mlfactory_import_frame"></iframe>
        <iframe id="mlfactory_import_status_frame" name="mlfactory_import_status_frame"></iframe>

            <?
            if ($note = Loc::getMessage("{$module_name}_import_note")) {
                echo BeginNote();
                echo $note;
                echo EndNote();
            }
        }
    }

    public static function getAdminEditJS() {
        $result = "
        <script type=\"text/javascript\">

            // общий обработчик для всех элементов
            function formRequiredGetMessageDefault( element, title ) {
                switch( element.tagName.toLowerCase() ) {
                    case 'input':
                        switch( $( element ).attr( 'type' ) ) {
                            case 'text':
                                if ( $( element ).val() == '' ) {
                                    return '" . Loc::getMessage('mladminpanelbuilder_field_required') . " - ' + title + '\\r\\n';
                                }
                                break;
                            case 'checkbox':
                                if ( !$( element ).is( ':checked' ) ) {
                                    return '" . Loc::getMessage('mladminpanelbuilder_field_required') . " - ' + title + '\\r\\n';
                                }
                                break;
                        }
                        break;
                    case 'M:1':
                        var empty = true;
                        var options = $( element ).find( 'option:selected' );
                        for ( var i = 0; i < options.length; i++ ) {
                            if ( $( options[ i ] ).text() != '' && $( options[ i ] ).val() != '' )
                                empty = false;
                        }

                        if ( empty ) {
                            return '" . Loc::getMessage('mladminpanelbuilder_field_required') . " - ' + title + '\\r\\n';
                        }
                        break;
                    case 'textarea':
                        if ( $( element ).val() == '' ) {
                            return '" . Loc::getMessage('mladminpanelbuilder_field_required') . " - ' + title + '\\r\\n';
                        }
                        break;
                }

                return false;
            }

            // пользовательский обработчик для элемента формы с атрибутом name=\"form1_cities\" для input с name=\"name\"
            // для элемента с name=\"items[]\" название обработчика будет выглядеть так formRequiredGetMessage_form1_cities_input_items__( element, title )
            function formRequiredGetMessage_form1_cities_input_name( element, title ) {
                if ( $( element ).val() == '' ) {
                    return '' + title + ': " . Loc::getMessage('mladminpanelbuilder_field_required') . "\\r\\n';
                }

                return false
            }

            $( function() {
                $( 'form' ).each( function( index, frm ) {
                    $( this ).submit( function( e ) {
                        var errorMessage = '';
                        $( this ).find( 'input[req=\"1\"], select[req=\"1\"], textarea[req=\"1\"]' ).each( function( index, element ) {
                            var msg = false;

                            var title = '';
                            if ( $( element ).parents( 'table.one_to_many' ).length > 0 ) {
                                var num = $( element ).parent( 'td' ).index();
                                title = $( element ).parents( 'table.one_to_many > thead > tr > td' ).eq( num ).find( 'span' ).text();
                            } else {
                                title = $( $( element ).parents( 'td' ).get( 0 ) ).prev( 'td' ).find( 'span' ).text();
                            }

                            var customFunctionName = 'formRequiredGetMessage_' + $( frm ).attr( 'name' ).split( '[' ).join( '_' ).split( '[' ).join( '_' ) + '_' + element.tagName.toLowerCase() + '_' + $( element ).attr( 'name' ).split( '[' ).join( '_' ).split( '[' ).join( '_' );
                            if ( window[ customFunctionName ] ) {
                                eval( 'msg = ' + customFunctionName + '( element, title );' );
                            } else {
                                msg = formRequiredGetMessageDefault( element, title );
                            }

                            if ( msg ) {
                                errorMessage += msg;
                            }
                        } );

                        if ( errorMessage.length > 0 ) {
                            alert( errorMessage );
                            $( '.adm-detail-content-btns .adm-btn-load-img, .adm-detail-content-btns .adm-btn-load-img-green' ).remove();
                            $( '.adm-detail-content-btns input' ).removeClass( 'adm-btn-load' );
                            setTimeout(
                                function() {
                                    $( '.adm-detail-content-btns input' ).each( function( index, element ) {
                                        $( element ).attr( 'disabled', false );
                                    } );
                                },
                                100
                            );
                            e.preventDefault();
                        }
                    } );
                } );
            } );
        </script>";

        return $result;
    }

    /*
     * Prepares value from $_POST for DB
     *
     * @param mixed $value field value
     * @param string $type field type
     * @param string $data_type data type
     * @return mixed
     */
    public static function PrepareFieldValue($value, $type = 'input_text', $data_type = '') {
        switch ($type) {
            case 'M:N':
                if (!is_array($value))
                    $value = array();

                foreach ($value as $vnum => $vitem) {
                    $value[$vnum] = (int)$vitem;
                }
                break;
            case 'datetime':
                $value = FieldType\DateTime::createFromUserTime($value);
                break;
            case 'checkbox':
            case 'M:1':
                $value = (int)$value;
                break;
            case 'input_text':
                switch ($data_type) {
                    case 'integer':
                        $value = (int)$value;
                        break;
                    case 'float':
                        $value = (double)$value;
                        break;
                }
                break;

            default:
                break;
        }

        return $value;
    }

    /*
     * Prepares field html code for edit page
     *
     * @param string $title param title
     * @param string $name field name
     * @param mixed $value field value
     * @param string $type field type
     * @param array $from param 'from' from config
     * @param array $list list of options for M:1
     * @param string $size attribute size for input[type="text"]
     * @param bool $required is field required
     * @param bool $readonly is field for read only
     * @param string $className class name for field
     * @return string
     */
    public static function PrepareFieldHtml($title, $name, $value, $type, $from = array(), $list = array(), $size="", $required = true, $readonly = false, $className = '') {
        $arFullWidthTypes = array(
            'html',
            '1:M',
        );

        $fieldHtml = '
	<tr id="tr_' . $name . (in_array($type, $arFullWidthTypes) ? '_label" class="heading"' : '"') . '>
		<td' . (in_array($type, $arFullWidthTypes) ? ' colspan="2"' : '') . '>' . ($required ? '<b><span>' . $title . '</span>*</b>' : '<span>' . $title . '</span>') . ':</td>
    ' . (in_array($type, $arFullWidthTypes) ? '</tr><tr id="tr_' . $name . '_editor"><td align="center" colspan="2">' : '<td>');
        switch ($type) {
            case '1:M':
                $sFromEntity = $from['entity'];
                $sRight = $from['reference'];
                $arFromEntity = &self::$module_cfg[$sFromEntity];

                $fieldHtml .= '<table class="adm-list-table one_to_many"><thead><tr class="adm-list-table-header">';
                foreach ($arFromEntity['fields'] as $sField => $arFieldParams) {
                    if (is_numeric($sField)) {
                        $sField = $arFieldParams;
                        $arFieldParams = array();
                    }

                    if ($sRight == $sField)
                        continue;
                    $fieldHtml .= '<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><span>' . Loc::getMessage(ADMIN_MODULE_NAME . '_' . strtolower($sFromEntity) . '_' . strtolower($sField)) . '</span>' . ($required ? '*' : '') . '</div></td>';
                }
                $fieldHtml .= '<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner">&nbsp;</div></td>';
                $fieldHtml .= '</tr></thead><tbody>';
                foreach ($value as $ID => $arValueParams) {
                    $fieldHtml .= '<tr class="adm-list-table-row">';
                    foreach ($arFromEntity['fields'] as $sField => $arFieldParams) {
                        if (is_numeric($sField)) {
                            $sField = $arFieldParams;
                            $arFieldParams = array();
                        }

                        if ($sRight == $sField)
                            continue;

                        if ($sField == 'ID') {
                            $sMassFieldHtml = $arValueParams[$sField];
                        } else {
                            $sMassFieldHtml = self::PrepareMassFieldHtml($name . '[' . $ID . '][' . $sField . ']', $arValueParams[$sField], @$arFieldParams['type'], @$arFieldParams['from'], @$arFieldParams['size']);
                        }
                        $fieldHtml .= '<td class="adm-list-table-cell">' . ($sMassFieldHtml ? $sMassFieldHtml : '&nbsp;') . '</td>';
                    }

                    $sMassFieldHtml = self::PrepareMassFieldHtml($name . '[' . $ID . '][mladminpanelbuilder_system_delete]', 0, 'checkbox');
                    $fieldHtml .= '<td class="adm-list-table-cell" align="center">' . Loc::getMessage('mladminpanelbuilder_one_to_many_delete') . '&nbsp;<br />&nbsp;&nbsp;&nbsp;' . ($sMassFieldHtml ? $sMassFieldHtml : '&nbsp;') . '</td>';
                    $fieldHtml .= '</tr>';
                }
                $fieldHtml .= '<tr class="adm-list-table-row">';
                foreach ($arFromEntity['fields'] as $sField => $arFieldParams) {
                    if (is_numeric($sField)) {
                        $sField = $arFieldParams;
                        $arFieldParams = array();
                    }

                    if ($sRight == $sField)
                        continue;

                    if ($sField == 'ID') {
                        $sMassFieldHtml = '&nbsp;';
                    } else {
                        $sMassFieldHtml = self::PrepareMassFieldHtml($name . '[new][' . $sField . ']', '', @$arFieldParams['type'], @$arFieldParams['from'], @$arFieldParams['size']);
                    }
                    $fieldHtml .= '<td class="adm-list-table-cell">' . ($sMassFieldHtml ? $sMassFieldHtml : '&nbsp;') . '</td>';
                }
                $sMassFieldHtml = self::PrepareMassFieldHtml($name . '[new][mladminpanelbuilder_system_add]', 0, 'checkbox');
                $fieldHtml .= '<td class="adm-list-table-cell" align="center">' . Loc::getMessage('mladminpanelbuilder_one_to_many_add') . '&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;' . ($sMassFieldHtml ? $sMassFieldHtml : '&nbsp;') . '</td>';
                $fieldHtml .= '</tr>';
                $fieldHtml .= '</tbody></table>';
                break;

            case 'text':
                $fieldHtml .= '
			<textarea class="typeinput ' . $className . '" cols="40" rows="15" name="' . $name . '"' . ($required ? ' req="1"' : '') . ($readonly ? ' readonly="readonly"' : '') . '>' . $value . '</textarea>';
                break;

            case 'file':
                $fieldHtml .= \CFileInput::Show(
                    $name,
                    (int)$value,
                    array(
                        "IMAGE" => "Y",
                        "PATH" => "Y",
                        "FILE_SIZE" => "Y",
                        "DIMENSIONS" => "Y",
                        "IMAGE_POPUP" => "Y",
                        "MAX_SIZE" => array(
                            "W" => \COption::GetOptionString("iblock", "detail_image_size"),
                            "H" => \COption::GetOptionString("iblock", "detail_image_size"),
                        ),
                    ),
                    array(
                        'upload' => true,
                        'medialib' => true,
                        'file_dialog' => true,
                        'cloud' => false,
                        'del' => true,
                        'description' => true,
                    )
                );
                /*if ( @getimagesize( $_SERVER[ 'DOCUMENT_ROOT' ] . $value ) ) {
                    $fieldHtml .= '<img id="img_' . md5( $value ) . '" src="' . $value . '" style="max-width: 118px; max-height: 100px;" /><script type="text/javascript">$( function () { var fileimg_' . md5( $value ) . ' = $( \'#img_' . md5( $value ) . '\' ).remove(); $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-preview\' ).prepend( fileimg_' . md5( $value ) . ' ); } );</script>';
                } else {
                    $fieldHtml .= '<script type="text/javascript">$( function () { if ( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).length > 0 ) $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html().substring( 0, 10 ) + \'...\' ); } );</script>';
                }*/
                break;

            case 'image':
                $fieldHtml .= \CFileInput::Show(
                    $name,
                    (int)$value,
                    array(
                        "IMAGE" => "Y",
                        "PATH" => "Y",
                        "FILE_SIZE" => "Y",
                        "DIMENSIONS" => "Y",
                        "IMAGE_POPUP" => "Y",
                        "MAX_SIZE" => array(
                            "W" => \COption::GetOptionString("iblock", "detail_image_size"),
                            "H" => \COption::GetOptionString("iblock", "detail_image_size"),
                        ),
                    ),
                    array(
                        'upload' => true,
                        'medialib' => true,
                        'file_dialog' => true,
                        'cloud' => false,
                        'del' => true,
                        'description' => false,
                    )
                );
                /*if ( @getimagesize( $_SERVER[ 'DOCUMENT_ROOT' ] . $value ) ) {
                    $fieldHtml .= '<img id="img_' . md5( $value ) . '" src="' . $value . '" style="max-width: 118px; max-height: 100px;" /><script type="text/javascript">$( function () { var fileimg_' . md5( $value ) . ' = $( \'#img_' . md5( $value ) . '\' ).remove(); $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-preview\' ).prepend( fileimg_' . md5( $value ) . ' ); } );</script>';
                } else {
                    $fieldHtml .= '<script type="text/javascript">$( function () { if ( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).length > 0 ) $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html().substring( 0, 10 ) + \'...\' ); } );</script>';
                }*/
                break;

            case 'checkbox':
                if (!$readonly)
                    $fieldHtml .= '
			<input type="checkbox" class="typeinput ' . $className . '" size="30" name="' . $name . '" value="1"' . ( $value ? ' checked="checked"' : '' ) . '' . ( $required ? ' req="1"' : '' ) . ' />';
                else
                    $fieldHtml .= '
            <input type="checkbox" class="typeinput ' . $className . '" size="30" name="_' . $name . '" value="1"' . ( $value ? ' checked="checked"' : '' ) . '' . ( $required ? ' req="1"' : '' ) . ' disabled' . ' />'.( $value ? '<input type="hidden" name="' . $name . '" value="1">':'');
                break;

            case 'M:1':
                if (@count($from) > 0) {
                    $sFromEntity = $from['entity'];
                    $sFromField = $from['field'];
					$sFilter = array();
					if (isset($from['filter'])) {
						$sFilter = $from['filter'];
					}

                    if (@count(self::$variants_cache[$sFromEntity . '_' . $sFromField]) == 0) {
                        /** @var \Bitrix\Main\Entity\DataManager $from_entity_class */
                        $from_entity_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $sFromEntity . 'Table';
                        $rsVariants = $from_entity_class::getList(array(
                            'order' => array(
                                $sFromField => 'asc',
                            ),
							'filter' => $sFilter,
                        ));
                        self::$variants_cache[$sFromEntity . '_' . $sFromField] = array();
                        while($arVariant = $rsVariants->fetch()) {
                            self::$variants_cache[$sFromEntity . '_' . $sFromField][] = array(
                                'id' => $arVariant['ID'],
                                'text' => $arVariant[$sFromField],
                            );
                        }
                    }

                    $fieldHtml .= '
            <select class="typeinput ' . $className . '" name="' . $name . '"' . ($required ? ' req="1"' : '') . ($readonly ? ' readonly="readonly"' : '') . '>
                <option></option>';
                    foreach (self::$variants_cache[$sFromEntity . '_' . $sFromField] as $arVariant) {
                        $fieldHtml .= '
                <option value="' . $arVariant['id'] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                    }
                    $fieldHtml .= '
            </select>';

                } else if (@count($list) > 0) {

                    $fieldHtml .= '
            <select class="typeinput ' . $className . '" name="' . $name . '"' . ($required ? ' req="1"' : '') . ($readonly ? ' readonly="readonly"' : '') . '>';
                    foreach ($list as $row) {
                        $fieldHtml .= '
                <option value="' . $row['id'] . '"' . ($value == $row['id'] ? ' selected="selected"' : '' ) . '>' . $row['text'] . '</option>';
                    }
                    $fieldHtml .= '
            </select>';
                }
                break;

            case 'M:N':
                if (@count($from) > 0) {
                    $sFromEntity = $from['entity'];
                    $sFromField = $from['field'];

                    if (@count(self::$variants_cache[$sFromEntity . '_' . $sFromField]) == 0) {
                        /** @var \Bitrix\Main\Entity\DataManager $from_entity_class */
                        $from_entity_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $sFromEntity . 'Table';
                        $rsVariants = $from_entity_class::getList(array(
                            'order' => array(
                                $sFromField => 'asc',
                            )
                        ));
                        self::$variants_cache[$sFromEntity . '_' . $sFromField] = array();
                        while($arVariant = $rsVariants->fetch()) {
                            self::$variants_cache[$sFromEntity . '_' . $sFromField][] = array(
                                'id' => $arVariant['ID'],
                                'text' => $arVariant[$sFromField],
                            );
                        }
                    }

                    $fieldHtml .= '
			<select class="typeinput ' . $className . '" name="' . $name . '[]" multiple="multiple"' . ($required ? ' req="1"' : '') . ($readonly ? ' readonly="readonly"' : '') . '>';
                    foreach (self::$variants_cache[$sFromEntity . '_' . $sFromField] as $arVariant) {
                        $fieldHtml .= '
                <option value="' . $arVariant['id'] . '"' . (in_array((int)$arVariant['id'], $value) ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                    }
                    $fieldHtml .= '
            </select>';
                }
                break;

            case 'datetime':
                $value = ($value && $value != "0000-00-00 00:00:00") ? strtotime($value) : "";
                $oAdminCalendar = new \CAdminCalendar();
                $oSite = new \CSite();
                $fieldHtml .= '
			' . ($required ? '<script type="text/javascript">
				$( function() {
					$( \'.adm-input-calendar[name="' . $name . '"]\' ).attr( \'req\', \'1\' );
				} );
			</script>' : '') . '
            ' . $oAdminCalendar->CalendarDate($name, date($GLOBALS['DB']->DateFormatToPHP($oSite->GetDateFormat('FULL')), $value), 19, true);
                break;

            case 'html':
                $fieldHtml .= '
            ';
                $oFileMan = new \CFileMan();
                ob_start();
                $oFileMan->AddHTMLEditorFrame(
                    $name,
                    $value,
                    $name . '_type',
                    'html',
                    array(
                        'width' => '100%',
                        'height' => '350',
                    )
                );
                $fieldHtml .= ob_get_clean();
                break;

            case 'input_text':
            default:
                $size = (int)$size;
                $size = $size >= 0 ? $size : "30";
                $fieldHtml .= '
			<input type="text" class="typeinput ' . $className . '" size="' . $size . '" name="' . $name . '" value="' . str_replace('"', '&quot;', $value) . '"' . ($required ? ' req="1"' : '') . ($readonly ? ' readonly="readonly"' : '') . ' />';
                break;
        }
        $fieldHtml .= '
		</td>
	</tr>';

        return $fieldHtml;
    }
    /*
     * Prepares field for mass editing in 1:M field
     *
     * @param string $name field name
     * @param mixed $value field value
     * @param string $type field type
     * @param array $from field from
     * @return string
     */
    public static function PrepareMassFieldHtml($name, $value, $type, $from = array(), $size="") {
        $required = false;

        $fieldHtml = '';
        switch ($type) {
            case '1:M':
                return false;
                break;

            case 'text':
            case 'html':
                $fieldHtml .= '
			<textarea class="typeinput" cols="20" rows="5" name="' . $name . '"' . ($required ? ' req="1"' : '') . '>' . $value . '</textarea>';
                break;

            case 'file':
                $fieldHtml .= /*'<div style="width:182px">' .*/ \CFileInput::Show(
                        $name,
                        array(
                            (int)$value,
                        ),
                        array(
                            "IMAGE" => "Y",
                            "PATH" => "Y",
                            "FILE_SIZE" => "Y",
                            "DIMENSIONS" => "Y",
                            "IMAGE_POPUP" => "Y",
                            "MAX_SIZE" => array(
                                "W" => \COption::GetOptionString("iblock", "detail_image_size"),
                                "H" => \COption::GetOptionString("iblock", "detail_image_size"),
                            ),
                        ),
                        array(
                            'upload' => true,
                            'medialib' => true,
                            'file_dialog' => true,
                            'cloud' => false,
                            'del' => true,
                            'description' => true,
                        )
                    );
                /*if ( @getimagesize( $_SERVER[ 'DOCUMENT_ROOT' ] . $value ) ) {
                    $fieldHtml .= '<img id="img_' . md5( $value ) . '_' . md5( $name ) . '" src="' . $value . '" style="max-width: 118px; max-height: 100px;" /><script type="text/javascript">$( function () { var fileimg_' . md5( $value ) . '_' . md5( $name ) . ' = $( \'#img_' . md5( $value ) . '_' . md5( $name ) . '\' ).remove(); $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-preview\' ).prepend( fileimg_' . md5( $value ) . '_' . md5( $name ) . ' ); } );</script>';
                } else {
                    $fieldHtml .= '<script type="text/javascript">$( function () { if ( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).length > 0 ) $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html().substring( 0, 10 ) + \'...\' ); } );</script>';
                }*/
                //$fieldHtml .= '</div>';
                break;

            case 'image':
                $fieldHtml .= /*'<div style="width:182px">' .*/ \CFileInput::Show(
                        $name,
                        array(
                            $value,
                        ),
                        array(
                            "IMAGE" => "Y",
                            "PATH" => "Y",
                            "FILE_SIZE" => "Y",
                            "DIMENSIONS" => "Y",
                            "IMAGE_POPUP" => "Y",
                            "MAX_SIZE" => array(
                                "W" => \COption::GetOptionString("iblock", "detail_image_size"),
                                "H" => \COption::GetOptionString("iblock", "detail_image_size"),
                            ),
                        ),
                        array(
                            'upload' => true,
                            'medialib' => true,
                            'file_dialog' => true,
                            'cloud' => false,
                            'del' => true,
                            'description' => false,
                        )
                    );
                /*if ( @getimagesize( $_SERVER[ 'DOCUMENT_ROOT' ] . $value ) ) {
                    $fieldHtml .= '<img id="img_' . md5( $value ) . '_' . md5( $name ) . '" src="' . $value . '" style="max-width: 118px; max-height: 100px;" /><script type="text/javascript">$( function () { var fileimg_' . md5( $value ) . '_' . md5( $name ) . ' = $( \'#img_' . md5( $value ) . '_' . md5( $name ) . '\' ).remove(); $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-preview\' ).prepend( fileimg_' . md5( $value ) . '_' . md5( $name ) . ' ); } );</script>';
                } else {
                    $fieldHtml .= '<script type="text/javascript">$( function () { if ( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).length > 0 ) $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html( $( \'#bx_file_' . str_replace( array( '[', ']', ), '_', $name ) . '_cont .adm-input-file-name\' ).html().substring( 0, 10 ) + \'...\' ); } );</script>';
                }*/
                //$fieldHtml .= '</div>';
                break;

            case 'checkbox':
                $fieldHtml .= '
			<input type="checkbox" class="typeinput" size="30" name="' . $name . '" value="1"' . ($value ? ' checked="checked"' : '') . '' . ($required ? ' req="1"' : '') . ' />';
                break;

            case 'M:1':
                if (@count($from) > 0) {
                    $sFromEntity = $from['entity'];
                    $sFromField = $from['field'];
					$sFilter = isset($from['filter']) ? $from['filter'] : array();

                    if (@count(self::$variants_cache[$sFromEntity . '_' . $sFromField]) == 0) {
                        /** @var \Bitrix\Main\Entity\DataManager $from_entity_class */
                        $from_entity_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $sFromEntity . 'Table';
                        $rsVariants = $from_entity_class::getList(array(
                            'order' => array(
                                $sFromField => 'asc',
                            ),
							'filter' => $sFilter,
                        ));
                        self::$variants_cache[$sFromEntity . '_' . $sFromField] = array();
                        while($arVariant = $rsVariants->fetch()) {
                            self::$variants_cache[$sFromEntity . '_' . $sFromField][] = array(
                                'id' => $arVariant['ID'],
                                'text' => $arVariant[$sFromField],
                            );
                        }
                    }

                    $fieldHtml .= '
			<select class="typeinput" name="' . $name . '"' . ($required ? ' req="1"' : '') . '>
                <option></option>';
                    foreach (self::$variants_cache[$sFromEntity . '_' . $sFromField] as $arVariant) {
                        $fieldHtml .= '
                <option value="' . $arVariant[ 'id' ] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                    }
                    $fieldHtml .= '
            </select>';
                }
                break;

            case 'M:N':
                if ( @count( $from ) > 0 ) {
                    $sFromEntity = $from['entity'];
                    $sFromField = $from['field'];

                    if (@count(self::$variants_cache[$sFromEntity . '_' . $sFromField]) == 0) {
                        /** @var \Bitrix\Main\Entity\DataManager $from_entity_class */
                        $from_entity_class = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $sFromEntity . 'Table';
                        $rsVariants = $from_entity_class::getList(array(
                            'order' => array(
                                $sFromField => 'asc',
                            )
                        ));
                        self::$variants_cache[$sFromEntity . '_' . $sFromField] = array();
                        while($arVariant = $rsVariants->fetch()) {
                            self::$variants_cache[$sFromEntity . '_' . $sFromField][] = array(
                                'id' => $arVariant['ID'],
                                'text' => $arVariant[$sFromField],
                            );
                        }
                    }

                    $fieldHtml .= '
			<select class="typeinput" name="' . $name . '[]" multiple="multiple">';
                    foreach (self::$variants_cache[$sFromEntity . '_' . $sFromField] as $arVariant) {
                        $fieldHtml .= '
                <option value="' . $arVariant['id'] . '"' . (in_array((int)$arVariant['id'], $value) ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                    }
                    $fieldHtml .= '
            </select>';
                }
                break;

            case 'datetime':
                $value = ($value && $value != "0000-00-00 00:00:00") ? strtotime($value) : "";
                $oAdminCalendar = new \CAdminCalendar();
                $oSite = new \CSite();
                $fieldHtml .= '
            ' . ($required ? '<script type="text/javascript">
				$( function() {
					$( \'.adm-input-calendar[name="' . $name . '"]\' ).attr( \'req\', \'1\' );
				} );
			</script>' : '') . $oAdminCalendar->CalendarDate($name, date($GLOBALS['DB']->DateFormatToPHP($oSite->GetDateFormat('FULL')), $value), 19, true);
                break;

            case 'input_text':
            default:
                $size = (int)$size;
                $size = $size >= 0 ? $size : "30";
                $fieldHtml .= '
			<input type="text" class="typeinput" size="' . $size . '" name="' . $name . '" value="' . str_replace('"', '&quot;', $value) . '"' . ($required ? ' req="1"' : '') . ' />';
                break;

                //return false;
                //break;
        }
        $fieldHtml .= '';

        return $fieldHtml;
    }

    /*
     * Prepares field html code for filter in entity elements list
     *
     * @param string $title param title
     * @param string $name field name
     * @param mixed $value field value
     * @param string $type type from config
     * @param array $from param 'from' from config
     * @return string
     */
    public static function PrepareFilterFieldHtml( $title, $name, $value, $data_type, $type, $from = array() ) {
        $fieldHtml = '
	<tr>
		<td>' . $title . ':</td>
		<td nowrap>';
        switch( $data_type ) {
            case 'text':
                $fieldHtml .= '
			<select name="f_' . $name . '">
                <option value="0"></option>
                <option value="1"' . ( $value == 1 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_not_empty') . '</option>
                <option value="2"' . ( $value == 2 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_empty') . '</option>
            </select>';
                break;

            case 'string':
                $fieldHtml .= '
			<input type="text" name="f_' . $name . '" value="' . $value . '" />';
                break;

            case 'integer':
            case 'boolean':
                switch ($type) {
                    case 'file':
                    case 'image':
                        $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value="0"></option>
                        <option value="1"' . ( $value == 1 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_not_empty') . '</option>
                        <option value="2"' . ( $value == 2 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_empty') . '</option>
                    </select>';
                        break;
                    case 'checkbox':
                        $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value="0"></option>
                        <option value="1"' . ( $value == 1 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_chechbox_yes') . '</option>
                        <option value="2"' . ( $value == 2 ? ' selected="selected"' : '' ) . '>' . Loc::getMessage('mladminpanelbuilder_chechbox_no') . '</option>
                    </select>';
                        break;
                    case 'M:1':
                        if (@count($from) > 0) {
                            if (isset($from['reference'])) {
                                $entityReferenceField = $from['reference'];
                                $entityShowField = $from['field'];
                                $referenceEntity = $from['entity'];
								$sFilter = isset($from['filter']) ? $from['filter'] : array();

                                if (@count(self::$variants_cache[$entityReferenceField . '_' . $entityShowField]) == 0) {
                                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                                    $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $referenceEntity . 'Table';

                                    $rsVariants = $referenceEntityTable::getList(array(
                                        'order' => array(
                                            $entityShowField => 'ASC',
                                        ),
										'filter' => $sFilter,
                                    ));
                                    self::$variants_cache[$entityReferenceField . '_' . $entityShowField] = array();
                                    while($arVariant = $rsVariants->fetch()) {
                                        self::$variants_cache[$entityReferenceField . '_' . $entityShowField][] = array(
                                            'id' => $arVariant['ID'],
                                            'text' => $arVariant[$entityShowField],
                                        );
                                    }
                                }

                                $variants = &self::$variants_cache[$entityReferenceField . '_' . $entityShowField];
                            } else {
                                $variants = &$from;
                            }

                            $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value=""></option>';
                            foreach ($variants as $arVariant) {
                                $fieldHtml .= '
                        <option value="' . $arVariant['id'] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                            }
                            $fieldHtml .= '
                    </select>';
                        }
                        break;
                    case 'M:N':
                        if (@count($from) > 0) {
                            if (isset($from['reference'])) {
                                $entityReferenceField = $from['joined'];
                                $entityShowField = $from['field'];
                                $referenceEntity = $from['entity'];

                                if (@count(self::$variants_cache[$entityReferenceField . '_' . $entityShowField]) == 0) {
                                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                                    $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $referenceEntity . 'Table';

                                    $rsVariants = $referenceEntityTable::getList(array(
                                        'order' => array(
                                            $entityShowField => 'ASC',
                                        )
                                    ));
                                    self::$variants_cache[$entityReferenceField . '_' . $entityShowField] = array();
                                    while($arVariant = $rsVariants->fetch()) {
                                        self::$variants_cache[$entityReferenceField . '_' . $entityShowField][] = array(
                                            'id' => $arVariant['ID'],
                                            'text' => $arVariant[$entityShowField],
                                        );
                                    }
                                }

                                $variants = &self::$variants_cache[$entityReferenceField . '_' . $entityShowField];
                            } else {
                                $variants = &$from;
                            }

                            $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value=""></option>';
                            foreach ($variants as $arVariant) {
                                $fieldHtml .= '
                        <option value="' . $arVariant['id'] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                            }
                            $fieldHtml .= '
                    </select>';
                        }
                        break;
                    case 'input_text':
                    default:
                        $fieldHtml .= '
                        ' . Loc::getMessage('mladminpanelbuilder_from') . ' <input type="text" name="f_' . $name . '[]" value="' . $value[ 0 ] . '" />
                        ' . Loc::getMessage('mladminpanelbuilder_to') . '<input type="text" name="f_' . $name . '[]" value="' . $value[ 1 ] . '" />';
                        break;
                }
                break;

            case 'float':
                $fieldHtml .= '
                        ' . Loc::getMessage('mladminpanelbuilder_from') . ' <input type="text" name="f_' . $name . '[]" value="' . $value[ 0 ] . '" />
                        ' . Loc::getMessage('mladminpanelbuilder_to') . '<input type="text" name="f_' . $name . '[]" value="' . $value[ 1 ] . '" />';
                break;


            case 'datetime':
                $fieldHtml .= '
            			' . CalendarPeriod('f_' . $name . '[0]', htmlspecialcharsex($value[0]), 'f_' . $name . '[1]', htmlspecialcharsex($value[1]), "find_form");
                break;

            default:
                switch ($type) {
                    case 'M:1':
                        if (@count($from) > 0) {
                            if (isset($from['reference'])) {
                                $entityReferenceField = $from['reference'];
                                $entityShowField = $from['field'];
                                $referenceEntity = $from['entity'];
								$sFilter = isset($from['filter']) ? $from['filter'] : array();

                                if (@count(self::$variants_cache[$entityReferenceField . '_' . $entityShowField]) == 0) {
                                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                                    $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $referenceEntity . 'Table';

                                    $rsVariants = $referenceEntityTable::getList(array(
                                        'order' => array(
                                            $entityShowField => 'ASC',
                                        ),
										'filter'=>$sFilter,
                                    ));
                                    self::$variants_cache[$entityReferenceField . '_' . $entityShowField] = array();
                                    while($arVariant = $rsVariants->fetch()) {
                                        self::$variants_cache[$entityReferenceField . '_' . $entityShowField][] = array(
                                            'id' => $arVariant['ID'],
                                            'text' => $arVariant[$entityShowField],
                                        );
                                    }
                                }

                                $variants = &self::$variants_cache[$entityReferenceField . '_' . $entityShowField];
                            } else {
                                $variants = &$from;
                            }

                            $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value=""></option>';
                            foreach ($variants as $arVariant) {
                                $fieldHtml .= '
                        <option value="' . $arVariant['id'] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                            }
                            $fieldHtml .= '
                    </select>';
                        }
                    case 'M:N':
                        if (@count($from) > 0) {
                            if (isset($from['reference'])) {
                                $entityReferenceField = $from['joined'];
                                $entityShowField = $from['field'];
                                $referenceEntity = $from['entity'];

                                if (@count(self::$variants_cache[$entityReferenceField . '_' . $entityShowField]) == 0) {
                                    /** @var \Bitrix\Main\Entity\DataManager $referenceEntityTable */
                                    $referenceEntityTable = '\\' . ADMIN_MODULE_NAMESPACE . '\\' . $referenceEntity . 'Table';

                                    $rsVariants = $referenceEntityTable::getList(array(
                                        'order' => array(
                                            $entityShowField => 'ASC',
                                        )
                                    ));
                                    self::$variants_cache[$entityReferenceField . '_' . $entityShowField] = array();
                                    while($arVariant = $rsVariants->fetch()) {
                                        self::$variants_cache[$entityReferenceField . '_' . $entityShowField][] = array(
                                            'id' => $arVariant['ID'],
                                            'text' => $arVariant[$entityShowField],
                                        );
                                    }
                                }

                                $variants = &self::$variants_cache[$entityReferenceField . '_' . $entityShowField];
                            } else {
                                $variants = &$from;
                            }

                            $fieldHtml .= '
                    <select name="f_' . $name . '">
                        <option value=""></option>';
                            foreach ($variants as $arVariant) {
                                $fieldHtml .= '
                        <option value="' . $arVariant['id'] . '"' . ((int)$value == (int)$arVariant['id'] ? ' selected="selected"' : '') . '>' . $arVariant['text'] . '</option>';
                            }
                            $fieldHtml .= '
                    </select>';
                        }
                        break;
                    default:
                        $fieldHtml .= '
                        ' . Loc::getMessage('mladminpanelbuilder_from') . ' <input type="text" name="f_' . $name . '[]" value="' . $value[ 0 ] . '" />
                        ' . Loc::getMessage('mladminpanelbuilder_to') . '<input type="text" name="f_' . $name . '[]" value="' . $value[ 1 ] . '" />';
                        break;
                }
                break;
        }

        return $fieldHtml;
    }

    /**
     * Prepares param for filter
     *
     * @param string $name field name
     * @param mixed $value field value
     * @param string $type field type
     * @param array $from param 'from' from config
     * @return string
     **/
    public static function PrepareFilterFieldValue($name, $value, $type, $from = array()) {
        $filterValue = array();
        switch($type) {
            case 'html':
            case 'text':
                if ((int)$value == 1) {
                    $filterValue['!' . $name] = '';
                }
                if ((int)$value == 2) {
                    $filterValue[$name] = '';
                }
                break;
            case 'file':
            case 'image':
                if ((int)$value == 1) {
                    $filterValue['!' . $name] = 0;
                }
                if ((int)$value == 2) {
                    $filterValue[$name] = 0;
                }
                break;
            case 'checkbox':
            case 'boolean':
                if ( (int)$value == 1 ) {
                    $filterValue[$name] = 1;
                }
                if ( (int)$value == 2 ) {
                    $filterValue[$name] = 0;
                }
                break;
            case 'M:1':
            case 'M:N':
                if (isset($from['reference'])) {
                    if ((int)$value > 0) {
                        $filterValue[$from['reference'] . '.ID'] = (int)$value;
                    }
                } else {
                    if (!empty($value)) {
                        $filterValue[$name] = $value;
                    }
                }
                break;

            case 'varchar':
            case 'string':
                if ( strlen( $value ) > 0 )
                    $filterValue[ '%=' . $name ] = '%' . $value . '%';
                break;

            case 'int':
            case 'integer':
                if ( $value[ 0 ] != '' ) {
                    $filterValue[ '>=' . $name ] = (int)$value[ 0 ];
                }
                if ( $value[ 1 ] != '' ) {
                    $filterValue[ '<=' . $name ] = (int)$value[ 1 ];
                }
                break;

            case 'float':
                if ( $value[ 0 ] != '' ) {
                    $filterValue[ '>=' . $name ] = (float)$value[ 0 ];
                }
                if ( $value[ 1 ] != '' ) {
                    $filterValue[ '<=' . $name ] = (float)$value[ 1 ];
                }
                break;

            case 'double':
                if ( $value[ 0 ] != '' ) {
                    $filterValue[ '>=' . $name ] = (double)$value[ 0 ];
                }
                if ( $value[ 1 ] != '' ) {
                    $filterValue[ '<=' . $name ] = (double)$value[ 1 ];
                }
                break;

            case 'datetime':
            case 'date':
                if ( $value[ 0 ] != '' ) {
                    $filterValue[ '>=' . $name ] = str_replace( '\'', '', $GLOBALS[ 'DB' ]->CharToDateFunction( $value[ 0 ], 'FULL' ) );
                }
                if ( $value[ 1 ] != '' ) {
                    $filterValue[ '<=' . $name ] = str_replace( '\'', '', $GLOBALS[ 'DB' ]->CharToDateFunction( $value[ 1 ], 'FULL' ) );
                }
                break;
        }

        return $filterValue;
    }

    /**
     * Prepares field value for output in entity list
     *
     * @param mixed $value field value
     * @param string $type field type from config array $CFG[entity][field]['__advanced']['type']
     * @return string
     **/
    public static function PrepareListHtml($value, $type, $url = '') {
        // TODO: change globals to other way when it will be able
        global $DB;
        $fieldHtml = '';
        switch($type) {
            case 'text':
                $fieldHtml .= $value;
                break;

            case 'file':
                $value = explode(',', $value);
                foreach ($value as $val) {
                    $val = (int)trim($val);
                    if ($val > 0) {
                        $oFile = new \CFile();
                        $file_src = $oFile->GetPath($val);
                        $fieldHtml .= '<a href="' . $file_src . '" target="_blank">' . basename($file_src) . '</a>';
                    }
                }
                break;

            case 'image':
                $value = explode(',', $value);
                foreach ($value as $val) {
                    $val = (int)trim($val);
                    if ($val > 0) {
                        $oFile = new \CFile();
                        $file_src = $oFile->GetPath($val);
                        $fieldHtml .= '<a href="' . $file_src . '" target="_blank" class="admin_image_preview"><img src="' . $file_src . '" style="max-width: 40px; max-height: 30px;" alt="" /></a> ';
                    }
                }
                break;

            case 'checkbox':
                $fieldHtml .= Loc::getMessage('mladminpanelbuilder_chechbox_' . ( $value ? 'yes' : 'no' ));
                break;

            case 'datetime':
                $oSite = new \CSite();
                $value = ($value && $value != "0000-00-00 00:00:00") ? strtotime($value) : "";
                $fieldHtml .= date($DB->DateFormatToPHP($oSite->GetDateFormat('FULL')), $value);
                break;

            case 'html':
            case 'M:1':
            case 'M:N':
            case 'input_text':

            default:
                if ($url) {
                    $fieldHtml .= '<a href="'.$url.'">'.$value.'</a>';
                } else {
                    $fieldHtml .= $value;
                }
                
                break;
        }

        return $fieldHtml;
    }
}