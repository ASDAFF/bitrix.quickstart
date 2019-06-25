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
        'STARTSHOP_SETTINGS_PAYMENT',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_PAYMENT',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsEdit || !$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "/bitrix/admin/startshop_settings_payment_edit.php?lang=".LANG."&action=add",
        'EDIT' => "/bitrix/admin/startshop_settings_payment_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'BACK' => "/bitrix/admin/startshop_settings_payment.php?lang=".LANG
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
    $arValues['HANDLER'] = strval($_REQUEST['HANDLER']);
    $arValues['CURRENCY'] = strval($_REQUEST['CURRENCY']);
    $arValues['PROPERTIES'] = array();
    $arValues['LANG'] = array();

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList($by = "lid", $order = "desc");

    while ($arLanguage = $dbLanguages->Fetch()) {
        $arLanguages[] = $arLanguage;
        $arValues['LANG'][$arLanguage['LID']]['NAME'] = $_REQUEST['LANG_' . $arLanguage['LID'] . '_NAME'];
    }

    $arHandlers = CStartShopUtil::DBResultToArray(CStartShopPayment::GetHandlersList(), 'CODE');
    $arCurrentHandler = $arHandlers[$arValues['HANDLER']];

    $arCurrencies = CStartShopUtil::DBResultToArray(CStartShopCurrency::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y')), 'CODE');

    if (!empty($arCurrentHandler))
        foreach ($arCurrentHandler['PARAMETERS'] as $sHandlerParameterKey => $arHandlerParameter) {
            $arValues['PROPERTIES'][$sHandlerParameterKey] = $_REQUEST[$arValues['HANDLER'].'_'.$sHandlerParameterKey];
        }

    if ($sAction == 'add') {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        if ($bActionSave || $bActionApply)
            if (!empty($arValues['CODE'])) {
                $iItemID = CStartShopPayment::Add($arValues);

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
        $arItem = CStartShopPayment::GetByID($_REQUEST['ID'])->GetNext();

        if ($_REQUEST['ADDED'] == 'Y')
            $sNotify = GetMessage('messages.notify.added');

        if (empty($arItem)) {
            LocalRedirect($arLinks['BACK']);
            die();
        }

        if ($bActionSave || $bActionApply) {
            $bUpdated = CStartShopPayment::Update($arItem['ID'], $arValues);

            if ($bUpdated) {
                if ($bActionSave) {
                    LocalRedirect($arLinks['BACK']);
                    die();
                }

                $sNotify = GetMessage('messages.notify.saved');
            } else {
                $sError = GetMessage('messages.warning.exists');
            }

            $arItem = CStartShopPayment::GetByID($_REQUEST['ID'])->GetNext();
        }

        $arValues['CODE'] = strval($arItem['CODE']);
        $arValues['SORT'] = intval($arItem['SORT']);
        $arValues['ACTIVE'] = strval($arItem['ACTIVE']);
        $arValues['HANDLER'] = strval($arItem['HANDLER']);
        $arValues['CURRENCY'] = strval($arItem['CURRENCY']);
        $arValues['PROPERTIES'] = $arItem['PROPERTIES'];
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
            "DIV" => "handler",
            "TAB" => GetMessage("tabs.handler"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.handler")
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
<form method="POST" id="StartShopPaymentEdit">
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
        <td width="40%"><?=GetMessage("fields.handler")?>:</td>
        <td>
            <?=CStartShopToolsForms::SelectBox('HANDLER', array(), array_merge(array('' => GetMessage('fields.handler.empty')), $arHandlers), $arValues['HANDLER'], false, 'NAME');?>
        </td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.currency")?>:</td>
        <td width="60%">
            <select name="CURRENCY">
                <option value=""><?=GetMessage('select.empty')?></option>
                <?foreach ($arCurrencies as $arCurrency):?>
                    <?if ($arCurrency['ACTIVE'] != 'Y') continue;?>
                    <option value="<?=htmlspecialcharsbx($arCurrency['CODE'])?>"<?=$arValues['CURRENCY'] == $arCurrency['CODE'] ? ' selected="selected"' : ''?>>[<?=htmlspecialcharsbx($arCurrency['CODE'])?>] <?=htmlspecialcharsbx($arCurrency['LANG'][LANGUAGE_ID]['NAME'])?></option>
                <?endforeach;?>
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
        $oTabControl->BeginNextTab();
    ?>
    <?foreach ($arHandlers as $sKey => $arHandler):?>
        <?foreach ($arHandler['PARAMETERS'] as $sParameterKey => $arParameter):?>
            <?$sParameterControlName = $sKey.'_'.$sParameterKey?>
            <?if ($arParameter['TYPE'] == "STRING"):?>
                <tr class="HandlerProperty">
                    <td width="40%"><?=$arParameter['NAME']?>:</td>
                    <td width="60%"><input type="text" name="<?=htmlspecialcharsbx($sParameterControlName)?>" value="<?=$arValues['PROPERTIES'][$sParameterKey]?>" /></td>
                </tr>
            <?elseif ($arParameter['TYPE'] == "CHECKBOX"):?>
                <tr class="HandlerProperty">
                    <td width="40%"><?=$arParameter['NAME']?>:</td>
                    <td width="60%">
                        <input type="hidden" name="<?=htmlspecialcharsbx($sParameterControlName)?>" value="N" />
                        <input type="checkbox" name="<?=htmlspecialcharsbx($sParameterControlName)?>" value="Y"<?=$arValues['PROPERTIES'][$sParameterKey] == 'Y' ? ' checked="checked"' : ''?> />
                    </td>
                </tr>
            <?endif;?>
        <?endforeach;?>
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
<script type="text/javascript">
    $(document).ready(function () {
        var $oRoot = $('#StartShopPaymentEdit');
        var $oHandlers = <?=CUtil::PhpToJSObject($arHandlers)?>;
        var $oHandlerField = $oRoot.find('select[name=HANDLER]');
        var $oCurrencyField = $oRoot.find('select[name=CURRENCY]');
        var $oHandlersProperties = $oRoot.find('.HandlerProperty');

        function Update () {
            var $sCurrentHandlerKey = $oHandlerField.val();
            var $oCurrentHandler = $oHandlers[$sCurrentHandlerKey];

            $oHandlersProperties.hide();

            if ($oCurrentHandler !== undefined) {
                var $iParametersCount = 0;

                $oCurrencyField.parents('tr').first().show();

                Startshop.Functions.forEach($oCurrentHandler['PARAMETERS'], function($sParameterKey, $oParameter) {
                    $oRoot.find('[name=' + $sCurrentHandlerKey + '_' + $sParameterKey + ']').parents('.HandlerProperty').show();
                    $iParametersCount++;
                });

                if ($iParametersCount > 0) {
                    tabs.EnableTab("handler");
                } else {
                    tabs.DisableTab("handler");
                }
            } else {
                tabs.DisableTab("handler");
                $oCurrencyField.parents('tr').first().hide();
            }
        }

        $oHandlerField.change(function () {
            Update();
        });

        Update();
    });
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
<?
    /*require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
    require_once($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("intec.startshop");

    global $USER, $APPLICATION;

    if(!$USER->IsAdmin())
        return;

    $sAction = $_REQUEST['action'];
    $arValues = array();
    $bSave = !empty($_REQUEST['save']);
    $bApply = !empty($_REQUEST['apply']);
    $bError = false;
    $bEmptyFields = false;

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => "/bitrix/admin/startshop_settings_payment.php?lang=".LANG,
        )
    );

    $arSaveFields = array(
        'NAME' => $_REQUEST['NAME'],
        'CODE' => $_REQUEST['CODE'],
        'ACTIVE' => $_REQUEST['ACTIVE'] == "Y" ? "Y" : "N",
        'HANDLER' => $_REQUEST['HANDLER']
    );

    $arTabs = array(
        array(
            "DIV" => "common",
            "TAB" => GetMessage("tabs.common"),
            "ICON" => "catalog",
            "TITLE" => GetMessage("tabs.common")
        )
    );

    $arHandlers = CStartShopPayment::GetHandlersList();

    $arLanguages = array();
    $dbLanguages = CLanguage::GetList();

    while ($arLanguage = $dbLanguages->Fetch())
        $arLanguages[] = $arLanguage;

    $arSaveLanguages = array();

    foreach ($arLanguages as $arLanguage)
    {
        $arSaveLanguages[] = array(
            "LID" => $arLanguage['LID'],
            "NAME" => $_REQUEST['NAME_'.$arLanguage['LID']]
        );
    }

    $arPayment = array();

    if ($sAction == 'add')
    {
        $APPLICATION->SetTitle(GetMessage('title.add'));

        $arValues['NAME'] = $_REQUEST['NAME'];
        $arValues['CODE'] = $_REQUEST['CODE'];
        $arValues['ACTIVE'] = $_REQUEST['ACTIVE'];
        $arValues['HANDLER'] = $_REQUEST['HANDLER'];
        $arValues['HANDLER_FIELDS'] = array();
        $arValues['LANG'] = array();

        foreach ($arSaveLanguages as $arSaveLanguage)
            $arValues['LANG'][$arSaveLanguage['LID']]['NAME'] = $arSaveLanguage['NAME'];

        if ($bApply || $bSave)
            if (!empty($arValues['NAME']) && !empty($arValues['CODE']))
            {
                if ($iCurrencyID = CStartShopPayment::Add($arSaveFields, false, $arSaveLanguages))
                {
                    if ($bApply)
                    {
                        LocalRedirect("/bitrix/admin/startshop_settings_payment_edit.php?lang=".LANG."&action=edit&ID=".$iCurrencyID);
                        return;
                    }
                }
                else
                {

                    $bError = true;
                }

            }
            else
            {
                $bEmptyFields = true;
            }
    }

    if ($sAction == 'edit')
    {
        $APPLICATION->SetTitle(GetMessage('title.edit'));

        $dbPayment = CStartShopPayment::GetList(array(), array('ID' => $_REQUEST['ID']));

        if (!$arPayment = $dbPayment->Fetch()) {
            LocalRedirect("/bitrix/admin/startshop_settings_payment.php?lang=" . LANG);
            return;
        }

        $arContextMenu[] = array(
            "TEXT" => GetMessage("title.buttons.add"),
            "ICON" => "btn_new",
            "LINK" => "/bitrix/admin/startshop_settings_payment_edit.php?lang=".LANG."&action=add",
        );

        $arCurrentHandler = array();

        if (!empty($arPayment['HANDLER']))
            foreach ($arHandlers as $arHandler)
                if ($arHandler['CODE'] == $arPayment['HANDLER'])
                    $arCurrentHandler = $arHandler;

        $arSaveHandlerParameters = array();

        foreach ($arCurrentHandler['PARAMETERS'] as $sCurrentHandlerKey => $arCurrentHandlerParameter)
            $arSaveHandlerParameters[$sCurrentHandlerKey] = $_REQUEST['HANDLER_FIELD_'.$sCurrentHandlerKey];

        if ($bApply || $bSave)
        {
            if (!CStartShopPayment::Update($_REQUEST['ID'], $arSaveFields, $arSaveHandlerParameters, $arSaveLanguages))
            {
                $bError = true;
            }
        }

        $dbPayment = CStartShopPayment::GetList(array(), array('ID' => $_REQUEST['ID']));
        $arPayment = $dbPayment->Fetch();

        $arValues['NAME'] = $arPayment['NAME'];
        $arValues['CODE'] = $arPayment['CODE'];
        $arValues['ACTIVE'] = $arPayment['ACTIVE'];
        $arValues['HANDLER'] = $arPayment['HANDLER'];
        $arValues['HANDLER_FIELDS'] = $arPayment['HANDLER_FIELDS'];
        $arValues['LANG'] = $arPayment['LANG'];

        $arCurrentHandler = array();

        if (!empty($arValues['HANDLER']))
            foreach ($arHandlers as $arHandler)
                if ($arHandler['CODE'] == $arValues['HANDLER'])
                    $arCurrentHandler = $arHandler;
    }

    if ($bSave && !$bError && !$bEmptyFields)
    {
        LocalRedirect("/bitrix/admin/startshop_settings_payment.php?lang=".LANG);
        return;
    }

    if ($bEmptyFields)
    {
        CAdminMessage::ShowMessage(GetMessage('messages.warning.empty_fields'));
    }

    $oContectMenu = new CAdminContextMenu($arContextMenu);
    $oContectMenu->Show();
?>
<form method="POST">
<?
    $oTabControl = new CAdminTabControl("tabControl", $arTabs);
    $oTabControl->Begin();
    $oTabControl->BeginNextTab();
?>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.name")?></b>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['NAME'])?>" name="NAME"/></td>
    </tr>
    <tr>
        <td width="40%"><b><?=GetMessage("fields.code")?></b>:</td>
        <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['CODE'])?>" name="CODE"/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.active")?>:</td>
        <td width="60%"><input type="checkbox" value="Y" name="ACTIVE"<?=$arValues['ACTIVE'] == 'Y' ? ' checked="checked"' : ''?>/></td>
    </tr>
    <tr>
        <td width="40%"><?=GetMessage("fields.handler")?>:</td>
        <td width="60%">
            <select name="HANDLER">
                <option value=""><?=GetMessage("fields.handler.empty")?></option>
                <?foreach ($arHandlers as $arHandler):?>
                    <option value="<?=htmlspecialcharsbx($arHandler['CODE'])?>"<?=$arValues['HANDLER'] == $arHandler['CODE'] ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arHandler['NAME'])?></option>
                <?endforeach;?>
            </select>
        </td>
    </tr>
    <?if (!empty($arCurrentHandler) && !empty($arCurrentHandler['PARAMETERS'])):?>
        <tr class="heading">
            <td colspan="2"><?=GetMessage("fields.handler.caption")?></td>
        </tr>
        <?foreach ($arCurrentHandler['PARAMETERS'] as $sCurrentHandlerParameterKey => $arCurrentHandlerParameter):?>
            <?if ($arCurrentHandlerParameter['TYPE'] == "STRING"):?>
                <tr>
                    <td width="40%"><?=$arCurrentHandlerParameter['NAME']?>:</td>
                    <td width="60%"><input type="text" value="<?=htmlspecialcharsbx($arValues['HANDLER_FIELDS'][$sCurrentHandlerParameterKey])?>" name="HANDLER_FIELD_<?=$sCurrentHandlerParameterKey?>" /></td>
                </tr>
            <?endif;?>
            <?if ($arCurrentHandlerParameter['TYPE'] == "CHECKBOX"):?>
                <tr>
                    <td width="40%"><?=$arCurrentHandlerParameter['NAME']?>:</td>
                    <td width="60%">
                        <input type="hidden" value="N" name="HANDLER_FIELD_<?=$sCurrentHandlerParameterKey?>" />
                        <input type="checkbox" value="Y" name="HANDLER_FIELD_<?=$sCurrentHandlerParameterKey?>"<?=$arValues['HANDLER_FIELDS'][$sCurrentHandlerParameterKey] == "Y" ? ' checked="checked"' : ''?> />
                    </td>
                </tr>
            <?endif;?>
        <?endforeach;?>
    <?endif;?>
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
                        <td><input type="text" value="<?=htmlspecialcharsbx($arValues['LANG'][$arLanguage['LID']]['NAME'])?>" name="NAME_<?=$arLanguage['LID']?>"/></td>
                    </tr>
                <?endforeach;?>
            </table>
        </td>
    </tr>
    <?
        $oTabControl->Buttons(
            array(
                "back_url" => "/bitrix/admin/startshop_settings_payment.php?lang=".LANG
            )
        );

        $oTabControl->End();
    ?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");*/?>
