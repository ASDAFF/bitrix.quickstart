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
        'STARTSHOP_SETTINGS_CURRENCY',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CURRENCY',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsEdit || !$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_settings_currency_edit.php?lang=".LANG."&action=add",
        'EDIT' => "/bitrix/admin/startshop_settings_currency_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_settings_currency.php?lang=".LANG
    );

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
    $arValues['SORT'] = intval($_REQUEST['SORT']);
    $arValues['ACTIVE'] = $_REQUEST['ACTIVE'] == "Y" ? "Y" : "N";
    $arValues['BASE'] = $_REQUEST['BASE'] == "Y" ? "Y" : "N";
    $arValues['RATE'] = floatval($_REQUEST['RATE']);
    $arValues['RATING'] = intval($_REQUEST['RATING']);
    $arValues['FORMAT'] = array();
    $arValues['LANG'] = array();

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList($by = "lid", $order = "desc");

    while ($arLanguage = $dbLanguages->Fetch()) {
        $arLanguages[] = $arLanguage;
        $arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_NAME'];
        $arValues['FORMAT'][$arLanguage['LID']]['FORMAT'] = $_REQUEST['FORMAT_'.$arLanguage['LID'].'_FORMAT'];
        $arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_DECIMAL'] = $_REQUEST['FORMAT_'.$arLanguage['LID'].'_DELIMITER_DECIMAL'];
        $arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_THOUSANDS'] = $_REQUEST['FORMAT_'.$arLanguage['LID'].'_DELIMITER_THOUSANDS'];
        $arValues['FORMAT'][$arLanguage['LID']]['DECIMALS_COUNT'] = $_REQUEST['FORMAT_'.$arLanguage['LID'].'_DECIMALS_COUNT'];
        $arValues['FORMAT'][$arLanguage['LID']]['DECIMALS_DISPLAY_ZERO'] = $_REQUEST['FORMAT_'.$arLanguage['LID'].'_DECIMALS_DISPLAY_ZERO'];
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['CODE'])) {
                $iItemID = CStartShopCurrency::Add($arValues);

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
        $arItem = CStartShopCurrency::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if ($bActionSave || $bActionApply) {
            $bUpdated = CStartShopCurrency::Update($arItem['ID'], $arValues);

            if ($bUpdated) {
                if ($bActionSave) {
                    LocalRedirect($arLinks['BACK']);
                    die();
                }

                $sNotify = GetMessage('messages.notify.saved');
            } else {
                $sError = GetMessage('messages.warning.exists');
            }

            $arItem = CStartShopCurrency::GetByID($_REQUEST['ID'])->GetNext();
        }

        $arValues['CODE'] = strval($arItem['CODE']);
        $arValues['SORT'] = intval($arItem['SORT']);
        $arValues['ACTIVE'] = strval($arItem['ACTIVE']);
        $arValues['BASE'] = strval($arItem['BASE']);
        $arValues['RATE'] = floatval($arItem['RATE']);
        $arValues['RATING'] = intval($arItem['RATING']);
        $arValues['FORMAT'] = $arItem['FORMAT'];
        $arValues['LANG'] = $arItem['LANG'];

        $APPLICATION->SetTitle(GetMessage('title.edit'));
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
        ),
        array(
            "DIV" => "lang&format",
            "TAB" => GetMessage("tabs.lang&format"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.lang&format")
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
<form method="POST">
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
        <td width="40%"><?=GetMessage("fields.base")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="BASE"<?=$arValues['BASE'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
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
        <td width="40%"><b><?=GetMessage("fields.rating")?>: <span class="required" style="vertical-align: super; font-size: smaller;">1</span></b></td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['RATING'])?>" name="RATING"/></td>
    </tr>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.rate")?>: <span class="required" style="vertical-align: super; font-size: smaller;">1</span></b></td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx(number_format($arValues['RATE'], 4, '.', ''))?>" name="RATE"/></td>
    </tr>
    <?
        $oTabControl->BeginNextTab();
    ?>
    <?foreach ($arLanguages as $arLanguage):?>
        <tr class="heading">
            <td colspan="2"><?=$arLanguage['NAME']?></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.lang&format.name")?>:</td>
            <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="LANG_<?=$arLanguage['LID']?>_NAME"/></td>
        </tr>
        <tr>
            <td width="40%"><b><?=GetMessage("fields.lang&format.format")?>: <span class="required" style="vertical-align: super; font-size: smaller;">2</span></b></td>
            <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['FORMAT'][$arLanguage['LID']]['FORMAT'])?>" name="FORMAT_<?=$arLanguage['LID']?>_FORMAT"/></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.lang&format.delimiter.decimal")?>:</td>
            <td width="60%">
                <?
                    $iSelectedField = 0;
                    switch($arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_DECIMAL']) {
                        case null: $iSelectedField = 0; break;
                        case '.': $iSelectedField = 1; break;
                        case ',': $iSelectedField = 2; break;
                        case ' ': $iSelectedField = 3; break;
                        default: $iSelectedField = 4; break;
                    }
                ?>
                <select name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL">
                    <option value=""<?=$iSelectedField == 0 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.none')?></option>
                    <option value="."<?=$iSelectedField == 1 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.point')?></option>
                    <option value=","<?=$iSelectedField == 2 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.comma')?></option>
                    <option value=" "<?=$iSelectedField == 3 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.whitespace')?></option>
                    <option value=""<?=$iSelectedField == 4 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.other')?></option>
                </select>
                <input type="text" maxlength="1" value="<?=$iSelectedField == 4 ? htmlspecialcharsbx($arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_DECIMAL']) : ''?>" name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL" style="width: 60px;" disabled="disabled"/>
                <script type="text/javascript">
                    $('document').ready(function(){
                        function Update() {
                            var $oSelect = $('select[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL"]');
                            var $oSelectedItem = $oSelect.find('option:selected');
                            var $iSelectedIndex = $oSelectedItem.index();

                            if ($iSelectedIndex >= 4) {
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL"]').removeAttr('disabled');
                            } else {
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL').attr('disabled', 'disabled');
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL').val($oSelect.val());
                            }
                        }

                        $('select[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_DECIMAL"]').change(function(){
                            Update();
                        });

                        Update();
                    });
                </script>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.lang&format.delimiter.thousands")?>:</td>
            <td width="60%">
                <?
                    $iSelectedField = 0;
                    switch($arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_THOUSANDS']) {
                        case null: $iSelectedField = 0; break;
                        case '.': $iSelectedField = 1; break;
                        case ',': $iSelectedField = 2; break;
                        case ' ': $iSelectedField = 3; break;
                        default: $iSelectedField = 4; break;
                    }
                ?>
                <select name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS">
                    <option value=""<?=$iSelectedField == 0 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.none')?></option>
                    <option value="."<?=$iSelectedField == 1 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.point')?></option>
                    <option value=","<?=$iSelectedField == 2 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.comma')?></option>
                    <option value=" "<?=$iSelectedField == 3 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.whitespace')?></option>
                    <option value=""<?=$iSelectedField == 4 ? ' selected="selected"' : ''?>><?=GetMessage('fields.lang&format.delimiter.type.other')?></option>
                </select>
                <input type="text" maxlength="1" value="<?=$iSelectedField == 4 ? htmlspecialcharsbx($arValues['FORMAT'][$arLanguage['LID']]['DELIMITER_THOUSANDS']) : ''?>" name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS" style="width: 60px;" disabled="disabled"/>
                <script type="text/javascript">
                    $('document').ready(function(){
                        function Update() {
                            var $oSelect = $('select[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS"]');
                            var $oSelectedItem = $oSelect.find('option:selected');
                            var $iSelectedIndex = $oSelectedItem.index();

                            if ($iSelectedIndex >= 4) {
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS"]').removeAttr('disabled');
                            } else {
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS').attr('disabled', 'disabled');
                                $('input[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS').val($oSelect.val());
                            }
                        }

                        $('select[name="FORMAT_<?=$arLanguage['LID']?>_DELIMITER_THOUSANDS"]').change(function(){
                            Update();
                        });

                        Update();
                    });
                </script>
            </td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.lang&format.decimals.count")?>: <b><span class="required" style="vertical-align: super; font-size: smaller;">3</span></b></td>
            <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['FORMAT'][$arLanguage['LID']]['DECIMALS_COUNT'])?>" name="FORMAT_<?=$arLanguage['LID']?>_DECIMALS_COUNT"/></td>
        </tr>
        <tr>
            <td width="40%"><?=GetMessage("fields.lang&format.decimals.display_zero")?>: <b><span class="required" style="vertical-align: super; font-size: smaller;">4</span></b></td>
            <td width="60%"><input type="checkbox" value="Y" name="FORMAT_<?=$arLanguage['LID']?>_DECIMALS_DISPLAY_ZERO"<?=$arValues['FORMAT'][$arLanguage['LID']]['DECIMALS_DISPLAY_ZERO'] == 'Y' ? ' checked="checked"' : ''?>/></td>
        </tr>
    <?endforeach;?>
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
</form>
<div class="adm-info-message-wrap" style="display: block;">
    <div class="adm-info-message" style="display: block;">
        <b><span class="required" style="vertical-align: super; font-size: smaller;">1</span></b> - <?=GetMessage("fields.rating.description")?><br />
        <b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b> - <?=GetMessage("fields.lang&format.format.description")?><br />
        <b><span class="required" style="vertical-align: super; font-size: smaller;">3</span></b> - <?=GetMessage("fields.lang&format.decimals.count.description")?><br />
        <b><span class="required" style="vertical-align: super; font-size: smaller;">4</span></b> - <?=GetMessage("fields.lang&format.decimals.display_zero.description")?>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
