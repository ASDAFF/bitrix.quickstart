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

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'BACK' => "/bitrix/admin/startshop_orders_edit.php?lang=".LANG."&action=edit&ID=#ID#&tabs_active_tab=items",
        'SELF' => "/bitrix/admin/startshop_orders_add_item.php?lang=".LANG."&order=#ID#&item=#ITEM#",
        'ERROR' => "/bitrix/admin/startshop_orders.php?lang=".LANG
    );

    $sError = null;
    $sNotify = null;

    $arOrder = CStartShopOrder::GetByID($_REQUEST['order'])->Fetch();
    $arItem = $arOrder['ITEMS'][$_REQUEST['item']];
    $arCatalogItem = CStartShopCatalogProduct::GetByID($arItem['ITEM'])->Fetch();

    if (empty($arOrder)) {
        LocalRedirect($arLinks['ERROR']);
        die();
    }

    if (empty($arItem)) {
        LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['BACK'], $arOrder));
        die();
    }

    $arCurrency = CStartShopCurrency::GetByCode($arOrder['CURRENCY'])->Fetch();

    $bActionSave = !empty($_REQUEST['save']);

    $arValues['NAME'] = empty($_REQUEST['NAME']) ? $arItem['NAME'] : $_REQUEST['NAME'];
    $arValues['PRICE'] = !is_numeric($_REQUEST['PRICE']) ? $arItem['PRICE'] : floatval($_REQUEST['PRICE']);
    $arValues['QUANTITY'] = !is_numeric($_REQUEST['PRICE']) ? $arItem['QUANTITY'] : floatval($_REQUEST['QUANTITY']);

    if ($bActionSave) {
        CStartShopOrder::UpdateItem($arOrder['ID'], $arItem['ITEM'], $arValues);
        LocalRedirect(CStartShopUtil::ReplaceMacros($arLinks['BACK'], $arOrder));
        die();
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
        <tr>
            <td width="40%"><b><?=GetMessage('fields.item')?>:</b></td>
            <td width="60%"><?=!empty($arCatalogItem) ? $arCatalogItem['NAME'] : GetMessage('fields.item.removed')?></td>
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
            <td width="60%"><input type="text" name="QUANTITY" value="<?=$arValues['QUANTITY']?>" /> <?=GetMessage('fields.quantity.total', array("#QUANTITY#" => intval($arCatalogItem['STARTSHOP']['QUANTITY']['VALUE'])))?></td>
        </tr>
    <?
        $oTabControl->Buttons();
    ?>
        <input type="submit" name="save" value="<?=GetMessage("button.apply");?>" title="<?=GetMessage("button.apply");?>" class="adm-btn-save">
        <a href="<?=CStartShopUtil::ReplaceMacros($arLinks['BACK'], $arOrder)?>" class="adm-btn"><?=GetMessage('button.cancel')?></a>
    <?
        $oTabControl->End();
    ?>
</form>
