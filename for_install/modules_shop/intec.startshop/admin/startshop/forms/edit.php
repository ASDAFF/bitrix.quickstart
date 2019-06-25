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
        'ADD' => "/bitrix/admin/startshop_forms_edit.php?lang=".LANG."&action=add",
        'EDIT' => "/bitrix/admin/startshop_forms_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_forms.php?lang=".LANG,
        'EVENT' => "/bitrix/admin/type_edit.php?EVENT_NAME=#EVENT_NAME#&lang=".LANG
    );

    $arItem = array();

    $bActionSave = !empty($_REQUEST['save']);
    $bActionApply = !empty($_REQUEST['apply']);

    $arActions = array('add', 'edit');
    $sAction = $_REQUEST['action'];

    $sSectionCurrent = 'FORM';
    $sError = null;
    $sNotify = null;

    if (!in_array($sAction, $arActions)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    if (!is_numeric($_REQUEST['SORT']) || !isset($_REQUEST['SORT']))
        $_REQUEST['SORT'] = 500;

    $arValues = array();
    $arValues['CODE'] = strval($_REQUEST['CODE']);
    $arValues['SORT'] = intval($_REQUEST['SORT']);
    $arValues['USE_POST'] = $_REQUEST['USE_POST'] == "Y" ? "Y" : "N";
    $arValues['USE_CAPTCHA'] = $_REQUEST['USE_CAPTCHA'] == "Y" ? "Y" : "N";
    $arValues['LANG'] = array();
    $arValues['SID'] = is_array($_REQUEST['SID']) ? $_REQUEST['SID'] : array();

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList($by = "lid", $order = "asc");

    while ($arLanguage = $dbLanguages->Fetch()) {
        $arLanguages[] = $arLanguage;
        $arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_NAME'];
        $arValues['LANG'][$arLanguage['LID']]['BUTTON'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_BUTTON'];
        $arValues['LANG'][$arLanguage['LID']]['SENT'] = $_REQUEST['LANG_'.$arLanguage['LID'].'_SENT'];
    }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['CODE'])) {
                $iItemID = CStartShopForm::Add($arValues);

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
        $arItem = CStartShopForm::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if ($bActionSave || $bActionApply) {
            $bUpdated = CStartShopForm::Update($arItem['ID'], $arValues);

            if ($bUpdated) {
                if ($bActionSave) {
                    LocalRedirect($arLinks['BACK']);
                    die();
                }

                $sNotify = GetMessage('messages.notify.saved');
            } else {
                $sError = GetMessage('messages.warning.exists');
            }

            $arItem = CStartShopForm::GetByID($_REQUEST['ID'])->GetNext();
        }

        CStartShopForm::UpdateEventTypes($_REQUEST['ID']);

        $arValues['CODE'] = strval($arItem['CODE']);
        $arValues['SORT'] = intval($arItem['SORT']);
        $arValues['USE_POST'] = strval($arItem['USE_POST']);
        $arValues['USE_CAPTCHA'] = strval($arItem['USE_CAPTCHA']);
        $arValues['LANG'] = $arItem['LANG'];
        $arValues['SID'] = $arItem['SID'];

        $APPLICATION->SetTitle(GetMessage('title.edit'));
    }

    $arSites = CStartShopUtil::DBResultToArray(CSite::GetList($by = "sort", $order = "asc"), 'ID');

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
    if ($sAction == 'edit')
        require_once('sections.php');

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
        <td width="40%"><b><?=GetMessage("fields.code")?>:</b></td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['CODE'])?>" name="CODE"/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.sort")?>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['SORT'])?>" name="SORT"/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.site")?>:</td>
        <td width="60%">
            <div class="adm-list">
            <?foreach ($arSites as $arSite):?>
                <div class="adm-list-item">
                    <label><input type="checkbox" value="<?=$arSite['ID']?>" name="SID[]"<?=in_array($arSite['ID'], $arValues['SID']) ? ' checked="checked"' : ''?>/><?=$arSite['NAME']?> (<?=$arSite['ID']?>)</label><br />
                </div>
            <?endforeach?>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.use_post")?>:</td>
        <td width="60%">
            <input type="checkbox" value="Y" name="USE_POST"<?=$arValues['USE_POST'] == 'Y' ? ' checked="checked"' : ''?>/>
            <?if ($sAction == 'edit'):?>[<a href="<?=CStartShopUtil::ReplaceMacros($arLinks['EVENT'], array('EVENT_NAME' => CStartShopForm::GetEventTypesName($arItem['ID'])))?>"><?=CStartShopForm::GetEventTypesName($arItem['ID'])?></a>]<?endif;?>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.use_captcha")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="USE_CAPTCHA"<?=$arValues['USE_CAPTCHA'] == 'Y' ? ' checked="checked"' : ''?>/></td>
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
                    <td><?=GetMessage("fields.language.button")?></td>
                    <td><?=GetMessage("fields.language.sent")?></td>
                </tr>
                <?foreach ($arLanguages as $arLanguage):?>
                    <tr>
                        <td><?=$arLanguage['NAME']?></td>
                        <td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="LANG_<?=$arLanguage['LID']?>_NAME"/></td>
                        <td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['BUTTON'])?>" name="LANG_<?=$arLanguage['LID']?>_BUTTON"/></td>
                        <td><textarea name="LANG_<?=$arLanguage['LID']?>_SENT" style="width: 200px; height: 100px;"><?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['SENT'])?></textarea></td>
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
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
