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
            'STARTSHOP_SETTINGS_ORDER_PROPERTY',
            'V'
        ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
            $USER->GetUserGroupArray(),
            'STARTSHOP_SETTINGS_ORDER_PROPERTY',
            'E'
        ) || $USER->IsAdmin();

    if (!$bRightsView || !$bRightsEdit) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_settings_order_property_edit.php?lang=".LANG."&action=add",
        'EDIT' => "/bitrix/admin/startshop_settings_order_property_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_settings_order_property.php?lang=".LANG
    );

    $arTypes = array(
        "INPUT" => array(
            "NAME" => GetMessage("property.type.S"),
            "TYPE" => "S",
            "SUBTYPE" => ""
        ),
        "INPUT_TEXT" => array(
            "NAME" => GetMessage("property.type.S.TEXT"),
            "TYPE" => "S",
            "SUBTYPE" => "TEXT"
        ),
        "CHECKBOX" => array(
            "NAME" => GetMessage("property.type.B"),
            "TYPE" => "B",
            "SUBTYPE" => ""
        ),
        "LIST_IBLOCK_ELEMENT" => array(
            "NAME" => GetMessage("property.type.L.IBLOCK_ELEMENT"),
            "TYPE" => "L",
            "SUBTYPE" => "IBLOCK_ELEMENT"
        )
    );

    $sCurrentType = key($arTypes);

    $arItem = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit');
    $sAction = $_REQUEST['action'];

    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    if (!is_numeric($_REQUEST['SORT']))
        $_REQUEST['SORT'] = 500;

    $arValues = array();
    $arValues['CODE'] = strval($_REQUEST['CODE']);
    $arValues['ACTIVE'] = $_REQUEST['ACTIVE'] == "Y" ? "Y" : "N";
    $arValues['REQUIRED'] = $_REQUEST['REQUIRED'] == "Y" ? "Y" : "N";
    $arValues['SID'] = strval($_REQUEST['SID']);
    $arValues['TYPE'] = $arTypes[$sCurrentType]['TYPE'];
    $arValues['SUBTYPE'] = $arTypes[$sCurrentType]['SUBTYPE'];
    $arValues['SORT'] = intval($_REQUEST['SORT']);
    $arValues['USER_FIELD'] = strval($_REQUEST['USER_FIELD']);
    $arValues['LANG'] = array();

    foreach ($arTypes as $sKey => $arType)
        if ($sKey == $_REQUEST['TYPE_ID']) {
            $arValues['TYPE'] = $arType['TYPE'];
            $arValues['SUBTYPE'] = $arType['SUBTYPE'];
            $sCurrentType = $sKey;
            break;
        }

    if ($sCurrentType == "LIST_IBLOCK_ELEMENT") {
        $arValues['DATA'] = array(
            "IBLOCK_ID" => intval($_REQUEST['TYPE_LIST_IBLOCK_ELEMENT_IBLOCK_ID'])
        );
    } else if ($sCurrentType == "INPUT" || $sCurrentType == "INPUT_TEXT") {
        $arValues['DATA'] = array(
            "EXPRESSION" => strval($_REQUEST['TYPE_INPUT_EXPRESSION'])
        );

        if ($sCurrentType == "INPUT")
            $arValues['DATA']['LENGTH'] = intval($_REQUEST['TYPE_INPUT_LENGTH']);
    } else {
        $arValues['DATA'] = false;
    }

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList($by = "lid", $order = "asc");

    while ($arLanguage = $dbLanguages->Fetch()) {
        $arLanguages[] = $arLanguage;
        $arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_NAME'];
        $arValues['LANG'][$arLanguage['LID']]['DESCRIPTION'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_DESCRIPTION'];
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['CODE']) && !empty($arValues['SID'])) {
                $iItemID = CStartShopOrderProperty::Add($arValues);

                if ($iItemID) {
                    if ($bActionSave) LocalRedirect($arLinks['BACK']);
                    if ($bActionApply) LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array("ID" => $iItemID)).'&ADDED=Y');
                    die();
                }

                $sError = GetMessage('messages.warning.exists');
            } else {
                $arFields = array();

                if (empty($arValues['CODE'])) $arFields[] = GetMessage('fields.code');
                if (empty($arValues['SID'])) $arFields[] = GetMessage('fields.site');

                $sError = GetMessage('messages.warning.empty_fields', array(
                    '#FIELDS#' => '\''.implode('\', \'', $arFields).'\''
                ));

                unset($arFields);
            }
    }

    if ($sAction == 'edit') {
        $arItem = CStartShopOrderProperty::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if ($bActionSave || $bActionApply) {
            $bUpdated = CStartShopOrderProperty::Update($arItem['ID'], $arValues);

            if ($bUpdated) {
                if ($bActionSave) {
                    LocalRedirect($arLinks['BACK']);
                    die();
                }

                $sNotify = GetMessage('messages.notify.saved');
            } else {
                $sError = GetMessage('messages.warning.exists');
            }

            $arItem = CStartShopOrderProperty::GetByID($_REQUEST['ID'])->GetNext();
        }

        $arValues = array();

        $arValues['CODE'] = strval($arItem['CODE']);
        $arValues['ACTIVE'] = strval($arItem['ACTIVE']);
        $arValues['REQUIRED'] = strval($arItem['REQUIRED']);
        $arValues['SID'] = strval($arItem['SID']);
        $arValues['TYPE'] = strval($arItem['TYPE']);
        $arValues['SUBTYPE'] = strval($arItem['SUBTYPE']);
        $arValues['SORT'] = intval($arItem['SORT']);
        $arValues['USER_FIELD'] = strval($arItem['USER_FIELD']);
        $arValues['DATA'] = $arItem['DATA'];
        $arValues['LANG'] = $arItem['LANG'];

        foreach ($arTypes as $sKey => $arType)
            if ($arType['TYPE'] == $arValues['TYPE'] && $arType['SUBTYPE'] == $arValues['SUBTYPE']) {
                $sCurrentType = $sKey;
                break;
            }

        $APPLICATION->SetTitle(GetMessage('title.edit'));
    }

    $arSites = array();
    $dbSites = CSite::GetList($by = "sort", $order = "asc");

    while ($arSite = $dbSites->Fetch())
        $arSites[] = $arSite;

    $arIBlocks = array();
    $dbIBlocks = CIBlock::GetList(array('SORT' => 'ASC'));

    while ($arIBlock = $dbIBlocks->Fetch())
        $arIBlocks[] = $arIBlock;

    $arUserFields = array(
        'LOGIN' => GetMessage('user_fields.login'),
        'EMAIL' => GetMessage('user_fields.email'),
        'NAME' => GetMessage('user_fields.name'),
        'LAST_NAME' => GetMessage('user_fields.last_name'),
        'SECOND_NAME' => GetMessage('user_fields.second_name'),
        'PERSONAL_PROFESSION' => GetMessage('user_fields.personal_profession'),
        'PERSONAL_WWW' => GetMessage('user_fields.personal_www'),
        'PERSONAL_ICQ' => GetMessage('user_fields.personal_icq'),
        'PERSONAL_PHONE' => GetMessage('user_fields.personal_phone'),
        'PERSONAL_FAX' => GetMessage('user_fields.personal_fax'),
        'PERSONAL_MOBILE' => GetMessage('user_fields.personal_mobile'),
        'PERSONAL_PAGER' => GetMessage('user_fields.personal_pager'),
        'PERSONAL_STREET' => GetMessage('user_fields.personal_street'),
        'PERSONAL_MAILBOX' => GetMessage('user_fields.personal_mailbox'),
        'PERSONAL_CITY' => GetMessage('user_fields.personal_city'),
        'PERSONAL_STATE' => GetMessage('user_fields.personal_state'),
        'PERSONAL_ZIP' => GetMessage('user_fields.personal_zip'),
        'PERSONAL_COUNTRY' => GetMessage('user_fields.personal_country'),
        'WORK_COMPANY' => GetMessage('user_fields.work_company'),
        'WORK_DEPARTMENT' => GetMessage('user_fields.work_department'),
        'WORK_POSITION' => GetMessage('user_fields.work_position'),
        'WORK_WWW' => GetMessage('user_fields.work_www'),
        'WORK_PHONE' => GetMessage('user_fields.work_phone'),
        'WORK_FAX' => GetMessage('user_fields.work_fax'),
        'WORK_PAGER' => GetMessage('user_fields.work_pager'),
        'WORK_STREET' => GetMessage('user_fields.work_street'),
        'WORK_MAILBOX' => GetMessage('user_fields.work_mailbox'),
        'WORK_CITY' => GetMessage('user_fields.work_city'),
        'WORK_STATE' => GetMessage('user_fields.work_state'),
        'WORK_ZIP' => GetMessage('user_fields.work_zip'),
        'WORK_COUNTRY' => GetMessage('user_fields.work_country')
    );

    $dbUserTypeEntities = CUserTypeEntity::GetList(array(), array(
        'ENTITY_ID' => 'USER',
        'USER_TYPE_ID' => 'string',
        'LANG' => LANGUAGE_ID
    ));

    while ($arUserTypeEntity = $dbUserTypeEntities->Fetch())
        $arUserFields[$arUserTypeEntity['FIELD_NAME']] = $arUserTypeEntity['LIST_COLUMN_LABEL'];

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
<?require_once($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
    $oContextMenu->Show();

    if (!empty($sError))
        CAdminMessage::ShowMessage($sError);

    if (!empty($sNotify) && empty($sError))
        CAdminMessage::ShowNote($sNotify);
?>
<form method="POST" id="StartshopOrderPropertyEdit">
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
            <td width="60%"><?=htmlspecialcharsbx($arItem['ID'])?></td>
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
        <td width="40%"><?=GetMessage("fields.user_field")?>:</td>
        <td width="60%">
            <?$bUserFieldCustom = !empty($arValues['USER_FIELD']) && !array_key_exists($arValues['USER_FIELD'], $arUserFields);?>
            <select name="USER_FIELD" style="width: 200px;">
                <option value=""><?=GetMessage('select.empty')?></option>
                <option value="" <?=$bUserFieldCustom ? ' selected="selected"' : ''?>><?=GetMessage('select.custom')?></option>
                <?foreach ($arUserFields as $sKey => $sName):?>
                    <option value="<?=$sKey?>"<?=$sKey == $arValues['USER_FIELD'] ? ' selected="selected"' : ''?>><?='['.$sKey.'] '.$sName?></option>
                <?endforeach?>
            </select><br />
            <input type="text" name="USER_FIELD" value="<?=htmlspecialcharsbx($arValues['USER_FIELD'])?>"<?=!$bUserFieldCustom ? ' disabled="disabled"' : ''?> style="margin-top: 5px; width: 200px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" />
            <?unset($bUserFieldCustom);?>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.required")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="REQUIRED"<?=$arValues['REQUIRED'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.site")?>:</b></td>
        <td width="60%">
            <?foreach ($arSites as $arSite):?>
                <label><input type="radio" value="<?=$arSite['ID']?>" name="SID"<?=$arValues['SID'] == $arSite['ID'] ? ' checked="checked"' : ''?>/><?=$arSite['NAME']?> (<?=$arSite['ID']?>)</label><br />
            <?endforeach?>
        </td>
    </tr>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.type")?>:</b></td>
        <td width="60%">
            <select name="TYPE_ID">
                <?foreach ($arTypes as $sKey => $arType):?>
                    <option value="<?=$sKey?>"<?=$sKey == $sCurrentType ? ' selected="selected"' : ''?>><?=$arType['NAME']?></option>
                <?endforeach?>
            </select>
        </td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_INPUT StartShopTypeField_INPUT_TEXT" style="display: none;">
        <td width="40%"><?=GetMessage("fields.type.input_expression")?>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($sCurrentType == 'INPUT' || $sCurrentType == 'INPUT_TEXT' ? $arValues['DATA']['EXPRESSION'] : '')?>" name="TYPE_INPUT_EXPRESSION"/></td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_INPUT" style="display: none;">
        <td width="40%"><?=GetMessage("fields.type.input_length")?>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($sCurrentType == 'INPUT' ? $arValues['DATA']['LENGTH'] : '')?>" name="TYPE_INPUT_LENGTH"/></td>
    </tr>
    <tr class="StartShopTypeField StartShopTypeField_LIST_IBLOCK_ELEMENT" style="display: none;">
        <td width="40%"><b><?=GetMessage("fields.type.list_iblock_element")?>:</b></td>
        <td width="60%">
            <select name="TYPE_LIST_IBLOCK_ELEMENT_IBLOCK_ID">
                <?foreach ($arIBlocks as $arIBlock):?>
                    <option value="<?=$arIBlock['ID']?>"<?=$arIBlock['ID'] == $arValues['DATA']['IBLOCK_ID'] && $sCurrentType == 'LIST_IBLOCK_ELEMENT' ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arIBlock['ID'])?>] <?=htmlspecialcharsbx($arIBlock['NAME'])?></option>
                <?endforeach?>
            </select>
        </td>
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
                    <td><?=GetMessage("fields.language.description")?></td>
                </tr>
                <?foreach ($arLanguages as $arLanguage):?>
                    <tr>
                        <td><?=$arLanguage['NAME']?></td>
                        <td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="LANG_<?=$arLanguage['LID']?>_NAME"/></td>
                        <td><textarea name="LANG_<?=$arLanguage['LID']?>_DESCRIPTION" style="resize: vertical; min-height: 50px; width: 250px;"><?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['DESCRIPTION'])?></textarea></td>
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

        $oTabControl->End();
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            var $oRoot = $('#StartshopOrderPropertyEdit');
            var $oUserFieldSelect = $oRoot.find('select[name="USER_FIELD"]');
            var $oUserFieldInput = $oRoot.find('input[name="USER_FIELD"]');

            function UpdateUserField() {
                if ($oUserFieldSelect.prop('selectedIndex') == 1) {
                    $oUserFieldInput.prop('disabled', false);
                    $oUserFieldInput.val('');
                } else {
                    $oUserFieldInput.prop('disabled', true);
                    $oUserFieldInput.val($oUserFieldSelect.val());
                }
            }

            $oUserFieldSelect.change(function () {
                UpdateUserField();
            });

            var $oTypeFieldSelect = $oRoot.find('select[name="TYPE_ID"]');
            var $oTypesFields = $oRoot.find('.StartShopTypeField');

            function UpdateTypesFields () {
                $oTypesFields.css('display', 'none');
                $oRoot.find('.StartShopTypeField.StartShopTypeField_' + $oTypeFieldSelect.val()).css('display', '');
            }

            $oTypeFieldSelect.change(function () {
                UpdateTypesFields();
            });

            UpdateTypesFields();
        });
    </script>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
