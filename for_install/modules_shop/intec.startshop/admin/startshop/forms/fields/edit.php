<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER, $APPLICATION;
    IncludeModuleLangFile(__FILE__);

    if (!CModule::IncludeModule("iblock"))
        return;

    if (!CModule::IncludeModule("intec.startshop"))
        return;

    $bRightsView = CStartShopUtilsRights::AllowedForGroups(
            $USER->GetUserGroupArray(),
            'STARTSHOP_FORMS',
            'V'
        ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
            $USER->GetUserGroupArray(),
            'STARTSHOP_FORMS',
            'E'
        ) || $USER->IsAdmin();

    if (!$bRightsEdit || !$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_forms_fields_edit.php?lang=".LANG."&action=add&FORM_ID=".$_REQUEST['FORM_ID'],
        'EDIT' => "/bitrix/admin/startshop_forms_fields_edit.php?lang=".LANG."&action=edit&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'BACK' => "/bitrix/admin/startshop_forms_fields.php?lang=".LANG."&FORM_ID=".$_REQUEST['FORM_ID'],
        'FORMS' => "/bitrix/admin/startshop_forms.php?lang=".LANG
    );

    $arItem = CStartShopForm::GetByID($_REQUEST['FORM_ID'])->Fetch();

    if (empty($arItem)) {
        LocalRedirect($arLinks['FORMS']);
        die();
    }

    $arField = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit');
    $sAction = $_REQUEST['action'];

    $sSectionCurrent = 'FIELDS';
    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    $arTypes = array(
        STARTSHOP_FORM_PROPERTY_TYPE_TEXT => GetMessage('types.text'),
        STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA => GetMessage('types.textarea'),
        STARTSHOP_FORM_PROPERTY_TYPE_RADIO => GetMessage('types.radio'),
        STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX => GetMessage('types.checkbox'),
        STARTSHOP_FORM_PROPERTY_TYPE_SELECT => GetMessage('types.select'),
        STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT => GetMessage('types.multiselect'),
        STARTSHOP_FORM_PROPERTY_TYPE_PASSWORD => GetMessage('types.password'),
        STARTSHOP_FORM_PROPERTY_TYPE_HIDDEN => GetMessage('types.hidden')
    );

    if (!is_numeric($_REQUEST['SORT']) || !isset($_REQUEST['SORT']))
        $_REQUEST['SORT'] = 500;

    $arValues = array();
    $arValues['CODE'] = strval($_REQUEST['CODE']);
    $arValues['FORM'] = $arItem['ID'];
    $arValues['SORT'] = intval($_REQUEST['SORT']);
    $arValues['ACTIVE'] = $_REQUEST['ACTIVE'] == "Y" ? "Y" : "N";
    $arValues['REQUIRED'] = $_REQUEST['REQUIRED'] == "Y" ? "Y" : "N";
    $arValues['READONLY'] = $_REQUEST['READONLY'] == "Y" ? "Y" : "N";
    $arValues['TYPE'] = intval($_REQUEST['TYPE']);
    $arValues['LANG'] = array();
    $arValues['DATA'] = array();

    if (in_array($arValues['TYPE'], array(STARTSHOP_FORM_PROPERTY_TYPE_TEXT, STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA))) {
        $arValues['DATA']['EXPRESSION'] = $_REQUEST['EXPRESSION'];
        $arValues['DATA']['EXPRESSION_FLAGS'] = $_REQUEST['EXPRESSION_FLAGS'];

        if ($arValues['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT) {
            $arValues['DATA']['MASK'] = $_REQUEST['MASK'];
            $arValues['DATA']['MASK_PLACEHOLDER'] = $_REQUEST['MASK_PLACEHOLDER'];
        }

    }

    if (in_array($arValues['TYPE'], array(STARTSHOP_FORM_PROPERTY_TYPE_RADIO, STARTSHOP_FORM_PROPERTY_TYPE_SELECT, STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT))) {
        $arValues['DATA']['VALUES'] = array();

        if (is_array($_REQUEST['MULTILINE_VALUE']))
            foreach($_REQUEST['MULTILINE_VALUE'] as $iValueID => $sValue) {
                $iSort = intval($_REQUEST['MULTILINE_SORT'][$iValueID]);

                if (!empty($sValue))
                    $arValues['DATA']['VALUES'][] = array(
                        "VALUE" => $sValue,
                        "SORT" => $iSort
                    );
            }
    }

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList($by = "lid", $order = "asc");

    while ($arLanguage = $dbLanguages->Fetch()) {
        $arLanguages[] = $arLanguage;
        $arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_NAME'];
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add', array('#NAME#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['CODE'])) {
                $iItemID = CStartShopFormProperty::Add($arValues);

                if ($iItemID) {
                    if ($bActionSave) LocalRedirect($arLinks['BACK']);
                    if ($bActionApply) LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array("ID" => $iItemID)).'&ADDED=Y');
                    die();
                }

                $sError = GetMessage('messages.warning.exists');
            } else {
                $arFields = array();

                if (empty($arValues['CODE'])) $arFields[] = GetMessage('fields.code');

                $sError = GetMessage('messages.warning.empty_fields', array(
                    '#FIELDS#' => '\''.implode('\', \'', $arFields).'\''
                ));

                unset($arFields);
            }
    }

    if ($sAction == 'edit') {
        $arField = CStartShopFormProperty::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arField) || $arField['FORM'] != $arItem['ID']) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if ($bActionSave || $bActionApply) {
            $bUpdated = CStartShopFormProperty::Update($arField['ID'], $arValues);

            if ($bUpdated) {
                if ($bActionSave) {
                    LocalRedirect($arLinks['BACK']);
                    die();
                }

                $sNotify = GetMessage('messages.notify.saved');
            } else {
                $sError = GetMessage('messages.warning.exists');
            }

            $arField = CStartShopFormProperty::GetByID($_REQUEST['ID'])->GetNext();
        }

        $arValues = array();
        $arValues['CODE'] = strval($arField['CODE']);
        $arValues['SORT'] = intval($arField['SORT']);
        $arValues['ACTIVE'] = strval($arField['ACTIVE']);
        $arValues['REQUIRED'] = strval($arField['REQUIRED']);
        $arValues['READONLY'] = strval($arField['READONLY']);
        $arValues['TYPE'] = intval($arField['TYPE']);
        $arValues['LANG'] = $arField['LANG'];
        $arValues['DATA'] = $arField['DATA'];

        $APPLICATION->SetTitle(GetMessage('title.edit', array('#NAME#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));
    }

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => $arLinks['BACK']
        ),
        array(
            "TEXT" => GetMessage("title.buttons.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
        )
    );

    $arTabs = array(
        array(
            "DIV" => "common",
            "TAB" => GetMessage("tabs.common"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.common")
        )
    );

    $oContextMenu = new CAdminContextMenu($arContextMenu);
    $oTabControl = new CAdminTabControl("tabs", $arTabs);
?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
    require_once(dirname(__FILE__).'/../sections.php');

    $oContextMenu->Show();

    if (!empty($sError))
        CAdminMessage::ShowMessage($sError);

    if (!empty($sNotify) && empty($sError))
        CAdminMessage::ShowNote($sNotify);
?>
<form method="POST" id="StartshopFormFieldEdit">
    <?
        $oTabControl->Begin();
        $oTabControl->BeginNextTab();
    ?>
    <?if ($sAction == 'add'):?>
        <input type="hidden" name="ADDED" value="Y" />
    <?endif;?>
    <?if ($sAction == 'edit'):?>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.id")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx($arField['ID'])?></td>
        </tr>
    <?endif;?>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.code")?>:</b></td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['CODE'])?>" name="CODE"/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.sort")?>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['SORT'])?>" name="SORT"/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.active")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="ACTIVE"<?=$arValues['ACTIVE'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.required")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="REQUIRED"<?=$arValues['REQUIRED'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.readonly")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="READONLY"<?=$arValues['READONLY'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.type")?>:</td>
        <td width="60%">
            <select name="TYPE">
                <?foreach ($arTypes as $iTypeID => $sTypeName):?>
                    <option value="<?=$iTypeID?>" <?=$iTypeID == $arValues['TYPE'] ? ' selected="selected"' : ''?>><?=$sTypeName?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_TEXT?> StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA?>">
        <td width="40%"><?=GetMessage("fields.expression")?>:</td>
        <td width="60%">
            <table>
                <tr>
                    <td><?=GetMessage('fields.expression.value')?>:</td>
                    <td><input type="text" value="<?=htmlspecialcharsbx($arValues['DATA']['EXPRESSION'])?>" name="EXPRESSION"/></td>
                </tr>
                <tr>
                    <td><?=GetMessage('fields.expression.flags')?>:</td>
                    <td><input type="text" value="<?=htmlspecialcharsbx($arValues['DATA']['EXPRESSION_FLAGS'])?>" name="EXPRESSION_FLAGS"/></td>
                </tr>
                <tr>
                    <td><?=GetMessage('fields.expression.test')?>:</td>
                    <td><input type="text" class="StartShopEditorExpression" /><span class="StartShopEditorExpressionValid" style="display: none; margin-left: 10px;"><?=GetMessage('fields.expression.test.valid')?></span><span class="StartShopEditorExpressionInvalid" style="display: none; margin-left: 10px;"><?=GetMessage('fields.expression.test.invalid')?></span></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_TEXT?>">
        <td width="40%"><?=GetMessage("fields.mask")?>:</td>
        <td width="60%">
            <table>
                <tr>
                    <td><?=GetMessage('fields.mask.value')?>:</td>
                    <td><input type="text" value="<?=htmlspecialcharsbx($arValues['DATA']['MASK'])?>" name="MASK" /></td>
                </tr>
                <tr>
                    <td><?=GetMessage('fields.mask.placeholder')?>:</td>
                    <td><input type="text" value="<?=htmlspecialcharsbx($arValues['DATA']['MASK_PLACEHOLDER'])?>" name="MASK_PLACEHOLDER" /></td>
                </tr>
                <tr>
                    <td><?=GetMessage('fields.mask.test')?>:</td>
                    <td><input type="text" class="StartShopEditorMask" /></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_RADIO?> StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_SELECT?> StartShopTypeField_<?=STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT?>">
        <?$arMultilineValues = array();?>
        <?if (is_array($arValues['DATA']['VALUES'])) $arMultilineValues = $arValues['DATA']['VALUES'];?>
        <td colspan="2" align="center">
            <table class="internal" id="StartShopEditorMultiline">
                <tr class="heading">
                    <td><?=GetMessage('fields.multiline.header.value')?></td>
                    <td><?=GetMessage('fields.multiline.header.sort')?></td>
                    <td></td>
                </tr>
                <?foreach ($arMultilineValues as $arMultilineValue):?>
                    <tr class="StartShopEditorMultilineLine">
                        <td><input type="text" name="MULTILINE_VALUE[]" value="<?=htmlspecialcharsbx($arMultilineValue['VALUE'])?>" /></td>
                        <td><input type="text" name="MULTILINE_SORT[]" style="width: 70px;" value="<?=htmlspecialcharsbx($arMultilineValue['SORT'])?>" /></td>
                        <td><a class="StartShopEditorMultilineLineDelete" style="cursor: pointer"><?=GetMessage('fields.multiline.actions.delete')?></a></td>
                    </tr>
                <?endforeach;?>
                <tr class="StartShopEditorMultilineLine">
                    <td><input type="text" name="MULTILINE_VALUE[]" /></td>
                    <td><input type="text" name="MULTILINE_SORT[]" style="width: 70px;" /></td>
                    <td><a class="StartShopEditorMultilineLineDelete" style="cursor: pointer"><?=GetMessage('fields.multiline.actions.delete')?></a></td>
                </tr>
                <tr class="actions">
                    <td colspan="3">
                        <a id="StartShopEditorMultilineLineAdd" style="cursor: pointer"><?=GetMessage('fields.multiline.actions.add')?></a>
                    </td>
                </tr>
            </table>
        </td>
        <?unset($arMultilineValues);?>
    </tr>
    <tr class="heading">
        <td colspan="2"><?=GetMessage("fields.language.caption")?></td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <table border="0" cellspacing="6" class="internal">
                <tr class="heading">
                    <td><?=GetMessage("fields.language.language")?></td>
                    <td><?=GetMessage("fields.language.name")?></td>
                </tr>
                <?foreach ($arLanguages as $arLanguage):?>
                    <tr>
                        <td><?=$arLanguage['NAME']?></td>
                        <td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="LANG_<?=$arLanguage['LID']?>_NAME"/></td>
                    </tr>
                <?endforeach;?>
            </table>
        </td>
    </tr>
    <?
    $oTabControl->Buttons(
        array(
            "back_url" => $arLinks['BACK']
        )
    );
    ?>
    <?
    $oTabControl->End();
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var $oRoot = $('#StartshopFormFieldEdit');
            var $oTypeFieldSelect = $oRoot.find('select[name="TYPE"]');
            var $oTypesFields = $oRoot.find('.StartShopTypeField');

            function UpdateTypesFields () {
                $oTypesFields.css('display', 'none');
                $oRoot.find('.StartShopTypeField.StartShopTypeField_' + $oTypeFieldSelect.val()).css('display', '');
            }

            $oTypeFieldSelect.change(function () {
                UpdateTypesFields();
            });

            UpdateTypesFields();

            var $oMultilineEditor = {};

            $oMultilineEditor.Root = $oRoot.find('#StartShopEditorMultiline');
            $oMultilineEditor.GetButtonAdd = function () {
                return this.Root.find('#StartShopEditorMultilineLineAdd');
            };
            $oMultilineEditor.GetButtonsDelete = function () {
                return this.Root.find('.StartShopEditorMultilineLineDelete');
            };
            $oMultilineEditor.GetLines = function () {
                return this.Root.find('.StartShopEditorMultilineLine');
            };
            $oMultilineEditor.AddLine = function () {
                this.GetLines().last().after(
                    $('<tr></tr>').addClass('StartShopEditorMultilineLine').append(
                        $('<td></td>').append(
                            $('<input />')
                                .attr('type', 'text')
                                .attr('name', 'MULTILINE_VALUE[]')
                        )
                    ).append(
                        $('<td></td>').append(
                            $('<input />')
                                .attr('type', 'text')
                                .attr('name', 'MULTILINE_SORT[]')
                                .css('width', '70px')
                        )
                    ).append(
                        $('<td></td>').append(
                            $('<a></a>')
                                .css('cursor', 'pointer')
                                .addClass('StartShopEditorMultilineLineDelete')
                                .html('<?=GetMessage('fields.multiline.actions.delete')?>')
                        )
                    )
                );

                this.UpdateButtonsDelete();
            };
            $oMultilineEditor.UpdateButtonsDelete = function () {
                var $oButtonsDelete = $oMultilineEditor.GetButtonsDelete();

                $oButtonsDelete.off('click');
                $oButtonsDelete.on('click', function () {
                    $(this).parent().parent().remove();
                    $oMultilineEditor.UpdateButtonsDelete();
                });

                if ($oButtonsDelete.size() <= 1) {
                    $oButtonsDelete.css('display', 'none');
                } else {
                    $oButtonsDelete.css('display', '');
                }
            };

            $oMultilineEditor.GetButtonAdd().on('click', function () {
                $oMultilineEditor.AddLine();
            });

            $oMultilineEditor.UpdateButtonsDelete();

            var $oMaskField = $oRoot.find('input[name="MASK"]');
            var $oMaskPlaceholder = $oRoot.find('input[name="MASK_PLACEHOLDER"]');
            var $oMaskViewer = $oRoot.find('.StartShopEditorMask');

            function UpdateMask() {
                if ($oMaskField.val().length > 0) {
                    $oMaskViewer.val(null);

                    $oMaskViewer.mask($oMaskField.val(), {
                        placeholder: $oMaskPlaceholder.val()
                    });
                } else {
                    $oMaskViewer.unmask();
                }

            }

            $oMaskField.add($oMaskPlaceholder).keyup(function () {
                UpdateMask();
            });

            UpdateMask();

            var $oExpressionField = $oRoot.find('input[name="EXPRESSION"]');
            var $oExpressionFlags = $oRoot.find('input[name="EXPRESSION_FLAGS"]');
            var $oExpressionViewer = $oRoot.find('.StartShopEditorExpression');
            var $oExpressionViewerValid = $oRoot.find('.StartShopEditorExpressionValid');
            var $oExpressionViewerInvalid = $oRoot.find('.StartShopEditorExpressionInvalid');

            function UpdateExpression() {
                try {
                    var $oRegExp = new RegExp($oExpressionField.val(), $oExpressionFlags.val());
                    if ($oRegExp.test($oExpressionViewer.val())) {
                        $oExpressionViewerValid.css('display', '');
                        $oExpressionViewerInvalid.css('display', 'none');
                    } else {
                        $oExpressionViewerValid.css('display', 'none');
                        $oExpressionViewerInvalid.css('display', '');
                    }
                } catch (e) {
                    $oExpressionViewerValid.css('display', 'none');
                    $oExpressionViewerInvalid.css('display', '');
                }
            }

            $oExpressionField.add($oExpressionFlags).add($oExpressionViewer).keyup(function () {
                UpdateExpression();
            });

            UpdateExpression();
        });
    </script>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
