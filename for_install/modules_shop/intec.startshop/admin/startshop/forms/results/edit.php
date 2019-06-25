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

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_forms_results_edit.php?lang=".LANG."&action=add&FORM_ID=".$_REQUEST['FORM_ID'],
        'EDIT' => "/bitrix/admin/startshop_forms_results_edit.php?lang=".LANG."&action=edit&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'VIEW' => "/bitrix/admin/startshop_forms_results_edit.php?lang=".LANG."&action=view&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'BACK' => "/bitrix/admin/startshop_forms_results.php?lang=".LANG."&FORM_ID=".$_REQUEST['FORM_ID'],
        'FORMS' => "/bitrix/admin/startshop_forms.php?lang=".LANG
    );

    $arItem = CStartShopForm::GetByID($_REQUEST['FORM_ID'])->Fetch();

    if (empty($arItem)) {
        LocalRedirect($arLinks['FORMS']);
        die();
    }

    $arResult = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit', 'view');
    $sAction = $_REQUEST['action'];

    if (empty($sAction) && !empty($_REQUEST['ID']))
        $sAction = 'view';

    if (!$bRightsEdit && ($sAction == 'add' || $sAction == 'edit')) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $sSectionCurrent = 'RESULTS';
    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    if (!is_numeric($_REQUEST['SORT']) || !isset($_REQUEST['SORT']))
        $_REQUEST['SORT'] = 500;

    $arValues = array();
    $arValues['PROPERTIES'] = array();

    $arProperties = CStartShopUtil::DBResultToArray(CStartShopFormProperty::GetList(array(), array('FORM' => $arItem['ID'])), 'ID');
    $arPropertiesEmpty = array();
    $arPropertiesInvalid = array();

    if (empty($arProperties))
        $sError = GetMessage('messages.warning.not_exists_fields');

    foreach ($arProperties as $arProperty) {
        $cPropertyValue = $_REQUEST[$arProperty['CODE']];

        if ($arProperty['REQUIRED'] == 'Y' && empty($cPropertyValue))
            $arPropertiesEmpty[] = !empty($arProperty['LANG'][LANGUAGE_ID]['NAME']) ? $arProperty['LANG'][LANGUAGE_ID]['NAME'] : $arProperty['CODE'];

        if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT || $arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA) {
            if (!empty($arProperty['DATA']['EXPRESSION'])) {
                if (!preg_match('/'.$arProperty['DATA']['EXPRESSION'].'/'.$arProperty['DATA']['EXPRESSION_FLAGS'], $cPropertyValue))
                    $arPropertiesInvalid[] = !empty($arProperty['LANG'][LANGUAGE_ID]['NAME']) ? $arProperty['LANG'][LANGUAGE_ID]['NAME'] : $arProperty['CODE'];
            }
        }

        $arValues['PROPERTIES'][$arProperty['ID']] = $cPropertyValue;
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add', array('#NAME#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));

        if ($bActionSave || $bActionApply)
            if (empty($arPropertiesEmpty) && empty($arPropertiesInvalid)) {
                $iItemID = CStartShopForm::CreateResult($arItem['ID'], $arValues['PROPERTIES'], false, false);

                if ($iItemID) {
                    if ($bActionSave) LocalRedirect($arLinks['BACK']);
                    if ($bActionApply) LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], array("ID" => $iItemID)).'&ADDED=Y');
                    die();
                }

                $sError = GetMessage('messages.warning.exists');
            } else {
                if (!empty($arPropertiesEmpty)) {
                    $sError = GetMessage('messages.warning.empty_fields', array(
                        '#FIELDS#' => '\''.implode('\', \'', $arPropertiesEmpty).'\''
                    ));
                } else {
                    $sError = GetMessage('messages.warning.invalid_fields', array(
                        '#FIELDS#' => '\''.implode('\', \'', $arPropertiesInvalid).'\''
                    ));
                }
            }
    }

    if ($sAction == 'edit' || $sAction == 'view') {
        $arResult = CStartShopFormResult::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arResult) || $arResult['FORM'] != $arItem['ID']) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if (($bActionSave || $bActionApply) && $sAction == 'edit') {
            if (empty($arPropertiesEmpty) && empty($arPropertiesInvalid)) {
                $bUpdated = CStartShopFormResult::Update($arResult['ID'], $arValues);

                if ($bUpdated) {
                    if ($bActionSave) {
                        LocalRedirect($arLinks['BACK']);
                        die();
                    }

                    $sNotify = GetMessage('messages.notify.saved');
                } else {
                    $sError = GetMessage('messages.warning.exists');
                }

                $arResult = CStartShopFormResult::GetByID($_REQUEST['ID'])->GetNext();
            } else {
                if (!empty($arPropertiesEmpty)) {
                    $sError = GetMessage('messages.warning.empty_fields', array(
                        '#FIELDS#' => '\''.implode('\', \'', $arPropertiesEmpty).'\''
                    ));
                } else {
                    $sError = GetMessage('messages.warning.invalid_fields', array(
                        '#FIELDS#' => '\''.implode('\', \'', $arPropertiesInvalid).'\''
                    ));
                }
            }
        }

        $arValues = array();
        $arValues['PROPERTIES'] = $arResult['PROPERTIES'];

        if ($sAction == 'edit') {
            $APPLICATION->SetTitle(GetMessage('title.edit', array('#NAME#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));
        } else {
            $APPLICATION->SetTitle(GetMessage('title.view', array('#NAME#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));
        }
    }

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => $arLinks['BACK']
        )
    );

    if ($bRightsEdit) {
        $arContextMenu[] = array(
            "TEXT" => GetMessage("title.buttons.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
        );
    }

    if ($sAction == 'edit') {
        $arContextMenu[] = array(
            "TEXT" => GetMessage("title.buttons.view"),
            "LINK" => CStartShopUtil::ReplaceMacros($arLinks['VIEW'], $arResult),
        );
    } else if ($sAction == 'view' && $bRightsEdit) {
       $arContextMenu[] = array(
           "TEXT" => GetMessage("title.buttons.edit"),
           "LINK" => CStartShopUtil::ReplaceMacros($arLinks['EDIT'], $arResult),
       );
    }

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
<form method="POST" id="StartshopFormResultEdit">
    <?
        $oTabControl->Begin();
        $oTabControl->BeginNextTab();
    ?>
    <?if ($sAction == 'add'):?>
        <input type="hidden" name="ADDED" value="Y" />
    <?endif;?>
    <?if ($sAction == 'edit' || $sAction == 'view'):?>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.id")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx($arResult['ID'])?></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.date_create")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx(date('d.m.Y H:i:s', strtotime($arResult['DATE_CREATE'])))?></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.date_modify")?>:</b></td>
            <td width="60%"><?=htmlspecialcharsbx(date('d.m.Y H:i:s', strtotime($arResult['DATE_MODIFY'])))?></td>
        </tr>
    <?endif;?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage('fields.fields.caption')?></td>
    </tr>
    <?if ($sAction == 'view'):?>
        <?foreach ($arProperties as $arProperty):?>
            <?$cPropertyValue = $arValues['PROPERTIES'][$arProperty['ID']];?>
            <?if (empty($cPropertyValue)) continue;?>
            <tr>
                <td width="40%">
                    <?if ($arProperty['REQUIRED'] == 'Y'):?><b><?endif;?>
                    <?=!empty($arProperty['LANG'][LANGUAGE_ID]['NAME']) ? $arProperty['LANG'][LANGUAGE_ID]['NAME'] : $arProperty['CODE'];?><?if ($arProperty['REQUIRED'] == 'Y'):?><span class="required" style="vertical-align: super; font-size: smaller;">*</span><?endif;?>:
                    <?if ($arProperty['REQUIRED'] == 'Y'):?></b><?endif;?>
                </td>
                <td>
                    <?if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX):?>
                        <?=$cPropertyValue == 'Y' ? GetMessage('checked.yes') : GetMessage('checked.no');?>
                    <?else:?>
                        <?if (!is_array($cPropertyValue)):?>
                            <?=strval($cPropertyValue);?>
                        <?else:?>
                            <?=implode(', ', $cPropertyValue);?>
                        <?endif;?>
                    <?endif;?>
                </td>
            </tr>
        <?endforeach;?>
    <?else:?>
        <?foreach ($arProperties as $arProperty):?>
            <?$cPropertyValue = $arValues['PROPERTIES'][$arProperty['ID']];?>
            <tr>
                <td width="40%">
                    <?if ($arProperty['REQUIRED'] == 'Y'):?><b><?endif;?>
                        <?=!empty($arProperty['LANG'][LANGUAGE_ID]['NAME']) ? $arProperty['LANG'][LANGUAGE_ID]['NAME'] : $arProperty['CODE'];?><?if ($arProperty['REQUIRED'] == 'Y'):?><span class="required" style="vertical-align: super; font-size: smaller;">*</span><?endif;?>:
                    <?if ($arProperty['REQUIRED'] == 'Y'):?></b><?endif;?>
                </td>
                <td>
                    <?if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT):?>
                        <input type="text" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="<?=htmlspecialcharsbx(strval($cPropertyValue))?>" style="width: 170px;" />
                        <?if (!empty($arProperty['DATA']['MASK'])):?>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $('#StartshopFormResultEdit')
                                        .find('input[name="<?=htmlspecialcharsbx($arProperty['CODE'])?>"]')
                                        .mask(<?=CUtil::PhpToJSObject($arProperty['DATA']['MASK'])?>, {
                                            'placeholder': <?=CUtil::PhpToJSObject($arProperty['DATA']['MASK_PLACEHOLDER'])?>
                                        });
                                })
                            </script>
                        <?endif;?>
                    <?elseif ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA):?>
                        <textarea name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" style="width: 170px; height: 100px; resize: vertical;"><?=htmlspecialcharsbx(strval($cPropertyValue))?></textarea>
                    <?elseif ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_RADIO || $arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_SELECT):?>
                        <select name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" style="width: 182px;">
                            <option value=""><?=GetMessage('select.empty')?></option>
                            <?foreach ($arProperty['DATA']['VALUES'] as $arPropertyValue):?>
                                <option value="<?=htmlspecialcharsbx($arPropertyValue['VALUE'])?>"<?=$arPropertyValue['VALUE'] == $cPropertyValue ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arPropertyValue['VALUE'])?></option>
                            <?endforeach;?>
                        </select>
                    <?elseif ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX):?>
                        <input type="hidden" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="N" />
                        <input type="checkbox" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="Y"<?=$cPropertyValue == 'Y' ? ' checked="checked"' : ''?> />
                    <?elseif ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT):?>
                        <select name="<?=htmlspecialcharsbx($arProperty['CODE'])?>[]" multiple="multiple" style="width: 182px;">
                            <option value=""><?=GetMessage('select.empty')?></option>
                            <?foreach ($arProperty['DATA']['VALUES'] as $arPropertyValue):?>
                                <?$bSelected = is_array($cPropertyValue) ? in_array($arPropertyValue['VALUE'], $cPropertyValue) : $arPropertyValue['VALUE'] == $cPropertyValue;?>
                                <option value="<?=htmlspecialcharsbx($arPropertyValue['VALUE'])?>"<?=$bSelected ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arPropertyValue['VALUE'])?></option>
                            <?endforeach;?>
                        </select>
                    <?elseif ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_PASSWORD || $arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_HIDDEN):?>
                        <input type="text" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="<?=htmlspecialcharsbx(strval($cPropertyValue))?>" style="width: 170px;" />
                    <?endif;?>
                </td>
            </tr>
        <?endforeach?>
        <?if (!empty($arProperties)):?>
            <?
                $oTabControl->Buttons(
                    array(
                        "back_url" => $arLinks['BACK']
                    )
                );
            ?>
        <?endif;?>
    <?endif;?>
    <?
        $oTabControl->End();
    ?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
