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
        'STARTSHOP_ORDERS',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_ORDERS',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsEdit || !$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/modules/iblock/admin_tools.php');
    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'BACK' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=edit&ID=#ID#&tabs_active_tab=items",
        'SELF' => "/bitrix/admin/startshop_orders_add_item.php?lang=".LANG."&order=#ID#&item=#ITEM#",
        'ERROR' => "/bitrix/admin/startshop_orders.php?lang=".LANG
    );

    $sError = null;
    $sNotify = null;

    $arOrder = CStartShopOrder::GetByID($_REQUEST['order'])->Fetch();
    $arItem = array();

    if (empty($arOrder)) {
        LocalRedirect($arLinks['ERROR']);
        die();
    }

    $iStep = 0;

    if (!empty($_REQUEST['step-1']))
        $iStep = 1;

    $arCurrency = CStartShopCurrency::GetByCode($arOrder['CURRENCY'])->Fetch();

    if (!empty($_REQUEST['item']))
        $arItem = CStartShopCatalogProduct::GetByID($_REQUEST['item'])->Fetch();

    if (empty($arItem) && $iStep == 1) {
        $sError = GetMessage('messages.warning.not_exists');
        $iStep = 0;
    }

    $bActionSave = !empty($_REQUEST['save']);

    if ($iStep == 1) {
        $arValues['NAME'] = empty($_REQUEST['NAME']) ? $arItem['NAME'] : $_REQUEST['NAME'];
        $arValues['PRICE'] = !is_numeric($_REQUEST['PRICE']) ? CStartShopCurrency::Convert($arItem['STARTSHOP']['PRICES']['MINIMAL']['VALUE'], $arItem['STARTSHOP']['CURRENCY'], $arOrder['CURRENCY']) : floatval($_REQUEST['PRICE']);
        $arValues['QUANTITY'] = !is_numeric($_REQUEST['PRICE']) ? 1 : floatval($_REQUEST['QUANTITY']);

        if ($bActionSave) {
            if (!CStartShopOrder::GetItem($arOrder['ID'], $arItem['ID'])->Fetch()) {
                CStartShopOrder::AddItem($arOrder['ID'], $arItem['ID'], $arValues);
                LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['BACK'], $arOrder));
                die();
            } else {
                $sError = GetMessage('messages.warning.exists');
            }
        }
    }

    $APPLICATION->SetTitle(GetMessage('title'));

    $arContextMenu = array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => CStartShopUtil::ReplaceMacros($arLinks['BACK'], $arOrder)
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
<form method="POST">
    <?
        $oTabControl->Begin();
        $oTabControl->BeginNextTab();
    ?>
        <?if ($iStep == 0):?>
            <tr>
                <td width="40%"><b><?=GetMessage('fields.item')?> <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b></td>
                <td width="60%"><?=_ShowElementPropertyField('item', array('MULTIPLE' => 'N'), array(is_array($_REQUEST['item']) ? $_REQUEST['item'][0] : $_REQUEST['item']))?></td>
            </tr>
        <?elseif ($iStep == 1):?>
            <input type="hidden" name="step-1" value="Y" />
            <input type="hidden" name="item" value="<?=$arItem['ID']?>" />
            <tr>
                <td width="40%"><b><?=GetMessage('fields.item')?>:</b></td>
                <td width="60%"><?=$arItem['NAME']?></td>
            </tr>
            <tr>
                <td width="40%"><b><?=GetMessage('fields.name')?> <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b></td>
                <td width="60%"><input type="text" name="NAME" value="<?=$arValues['NAME']?>" /></td>
            </tr>
            <tr>
                <td width="40%"><b><?=GetMessage('fields.price')?> <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b></td>
                <td width="60%"><input type="text" name="PRICE" value="<?=$arValues['PRICE']?>" /> <?=trim(str_replace('#', '', $arCurrency['FORMAT'][LANGUAGE_ID]['FORMAT']))?></td>
            </tr>
            <tr>
                <td width="40%"><b><?=GetMessage('fields.quantity')?> <span class="required" style="vertical-align: super; font-size: smaller;">*</span>:</b></td>
                <td width="60%"><input type="text" name="QUANTITY" value="<?=$arValues['QUANTITY']?>" /> <?=GetMessage('fields.quantity.total', array("#QUANTITY#" => $arItem['STARTSHOP']['QUANTITY']['VALUE']))?></td>
            </tr>
        <?endif;?>
    <?
        $oTabControl->Buttons();
    ?>
        <?if ($iStep == 0):?>
            <input type="submit" name="step-1" value="<?=GetMessage("button.next");?>" title="<?=GetMessage("button.next");?>" class="adm-btn" />
        <?elseif ($iStep == 1):?>
            <input type="submit" name="save" value="<?=GetMessage("button.create");?>" title="<?=GetMessage("button.create");?>" class="adm-btn-save">
            <a name="save" href="<?=CStartShopUtil::ReplaceMacros($arLinks['SELF'], array('ID' => $arOrder['ID'], "ITEM" => $arItem['ID']))?>" title="<?=GetMessage("button.prev");?>" class="adm-btn"><?=GetMessage("button.prev");?></a>
        <?endif;?>
    <?
        $oTabControl->End();
    ?>
</form>
