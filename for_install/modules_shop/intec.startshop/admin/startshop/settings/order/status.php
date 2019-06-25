<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");?>
<?
    global $USER, $APPLICATION;
    IncludeModuleLangFile(__FILE__);

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    if (!CModule::IncludeModule("iblock"))
        return;

    if (!CModule::IncludeModule("intec.startshop"))
        return;

    $bRightsView = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_ORDER_STATUS',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_ORDER_STATUS',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $arLinks = array(
        'ADD' => "startshop_settings_order_status_edit.php?lang=".LANG."&action=add&SID=#SITE_ID#",
        'EDIT' => "startshop_settings_order_status_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_settings_order_status.php?lang=".LANG."&action=delete&ID=#ID#",
        'CAN_PAY' => "startshop_settings_order_status.php?lang=".LANG."&action=can_pay&ID=#ID#",
        'CAN_NOT_PAY' => "startshop_settings_order_status.php?lang=".LANG."&action=can_not_pay&ID=#ID#",
        'DEFAULT' => "startshop_settings_order_status.php?lang=".LANG."&action=default&ID=#ID#"
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];
    $arActionGroup = explode('&', $sAction);

    if (count($arActionGroup) == 2) {
        $sAction = $arActionGroup[0];
        $_REQUEST['table_id'] = "startshop_order_status_list_".$arActionGroup[1];
    }

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopOrderStatus::Delete($_REQUEST['ID']);

        if ($sAction == 'can_pay' && is_numeric($_REQUEST['ID']))
            CStartShopOrderStatus::Update($_REQUEST['ID'], array("CAN_PAY" => "Y"));

        if ($sAction == 'can_not_pay' && is_numeric($_REQUEST['ID']))
            CStartShopOrderStatus::Update($_REQUEST['ID'], array("CAN_PAY" => "N"));

        if ($sAction == 'default' && is_numeric($_REQUEST['ID']))
            CStartShopOrderStatus::Update($_REQUEST['ID'], array("DEFAULT" => "Y"));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderStatus::Delete($iItemID);

            if ($sAction == 'can_pay.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderStatus::Update($iItemID, array("CAN_PAY" => "Y"));

            if ($sAction == 'can_not_pay.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderStatus::Update($iItemID, array("CAN_PAY" => "N"));
        }
    }

    $arTabs = array();
    $arSites = array();
    $dbSites = CSite::GetList($by = "sort", $order = "asc");

    while ($arSite = $dbSites->Fetch())	{
        $arSites[] = $arSite;
        $arTabs[] = array(
            "DIV" => $arSite['ID'],
            "TAB" => $arSite["NAME"],
            "TITLE" => $arSite["NAME"]
        );
    }

    unset($dbSites, $arSite);

    $arLists = array();
    $arListsID = array();

    foreach ($arSites as $arSite)
        $arListsID[] = "startshop_order_status_list_".$arSite['ID'];

    if (!in_array($_REQUEST['table_id'], $arListsID))
        unset($_REQUEST['table_id']);

    foreach ($arSites as $arSite) {
        $sListID = "startshop_order_status_list_".$arSite['ID'];

        $_GET['table_id'] = $sListID;
        $_POST['table_id'] = $sListID;

        if (!empty($_REQUEST['table_id']) && $_REQUEST['table_id'] != $sListID)
            continue;

        $sSortBy = "ID";
        $sOrderBy = "asc";
        $oSort = new CAdminSorting(
            $sListID,
            $sSortBy,
            $sOrderBy,
            "by".$arSite['ID'],
            "order".$arSite['ID']
        );

        $sOrderBy = $_REQUEST["order".$arSite['ID']];
        $sSortBy = $_REQUEST["by".$arSite['ID']];

        $oList = new CAdminList($sListID, $oSort);
        $dbResult = CStartShopOrderStatus::GetList(array($sSortBy => $sOrderBy), array("SID" => $arSite['ID']));
        $dbResult = new CAdminResult($dbResult, $sListID);
        $dbResult->NavStart(20);
        $oList->NavText($dbResult->GetNavPrint(GetMessage('nav.title'), true));

        $oList->AddHeaders(array(
            array(
                "id" => "ID",
                "content" => GetMessage('table.header.id'),
                "sort" => "ID",
                "default" => true
            ),
            array(
                "id" => "NAME",
                "content" => GetMessage('table.header.name'),
                "default" => true
            ),
            array(
                "id" => "CODE",
                "content" => GetMessage('table.header.code'),
                "sort" => "CODE",
                "default" => true
            ),
            array(
                "id" => "SORT",
                "content" => GetMessage('table.header.sort'),
                "sort" => "SORT",
                "default" => true
            ),
            array(
                "id" => "CAN_PAY",
                "content" => GetMessage('table.header.can_pay'),
                "sort" => "CAN_PAY",
                "default" => true
            ),
            array(
                "id" => "DEFAULT",
                "content" => GetMessage('table.header.default'),
                "sort" => "DEFAULT",
                "default" => true
            )
        ));

        if ($bRightsEdit)
            $oList->AddGroupActionTable(array(
                "delete.selected&".$arSite['ID'] => GetMessage('actions.group.delete'),
                "can_pay.selected&".$arSite['ID'] => GetMessage('actions.group.can_pay'),
                "can_not_pay.selected&".$arSite['ID'] => GetMessage('actions.group.can_not_pay')
            ));

        while ($arResultItem = $dbResult->GetNext()) {
            $arActions = array();

            if ($bRightsEdit) {
                $arActions[] = array(
                    "ICON" => "edit",
                    "TEXT" => GetMessage('actions.edit'),
                    "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['EDIT'], $arResultItem))
                );

                $arActions[] = array(
                    "ICON" => "delete",
                    "TEXT" => GetMessage('actions.delete'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['DELETE'], $arResultItem))
                );

                $arActions[] = array(
                    "SEPARATOR" => "Y"
                );

                if ($arResultItem['DEFAULT'] == 'N') {
                    $arActions[] = array(
                        "TEXT" => GetMessage('actions.default'),
                        "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['DEFAULT'], $arResultItem))
                    );
                }

                if ($arResultItem['CAN_PAY'] == 'Y') {
                    $arActions[] = array(
                        "TEXT" => GetMessage('actions.can_not_pay'),
                        "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['CAN_NOT_PAY'], $arResultItem))
                    );
                } else {
                    $arActions[] = array(
                        "TEXT" => GetMessage('actions.can_pay'),
                        "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['CAN_PAY'], $arResultItem))
                    );
                }
            }

            $sPayed = $arResultItem['CAN_PAY'] == 'Y' ?
                GetMessage('active.yes') :
                GetMessage('active.no');

            $sDefault = $arResultItem['DEFAULT'] == 'Y' ?
                GetMessage('active.yes') :
                GetMessage('active.no');

            $oRow = &$oList->AddRow($arResultItem["ID"], array(
                "ID" => $arResultItem["ID"],
                "NAME" => $arResultItem['LANG'][LANGUAGE_ID]["NAME"],
                "CODE" => $arResultItem["CODE"],
                "SORT" => $arResultItem["SORT"],
                "CAN_PAY" => $sPayed,
                "DEFAULT" => $sDefault
            ));

            $oRow->AddActions($arActions);
        }

        $arContextMenu = array();

        if ($bRightsEdit)
            $arContextMenu[] = array(
                "TEXT" => GetMessage("actions.add"),
                "ICON" => "btn_new",
                "LINK" => CStartShopUtil::ReplaceMacros($arLinks['ADD'], array('SITE_ID' => $arSite['ID'])),
                "TITLE" => GetMessage("actions.add")
            );

        $oList->AddAdminContextMenu($arContextMenu, true, true);
        $oList->CheckListMode();

        $arLists[$arSite['ID']] = $oList;
    }

    $oTabs = new CAdminTabControl(
        "sites",
        $arTabs
    );
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
    $oTabs->Begin();
?>
<?foreach ($arSites as $arSite):?>
    <?$oTabs->BeginNextTab()?>
    <tr>
        <td>
            <?$arLists[$arSite['ID']]->DisplayList();?>
        </td>
    <tr>
<?endforeach;?>
<?
    $oTabs->End();
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>