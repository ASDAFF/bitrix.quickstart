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
        'STARTSHOP_SETTINGS_PAYMENT',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_PAYMENT',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $arLinks = array(
        'ADD' => "startshop_settings_payment_edit.php?lang=".LANG."&action=add",
        'EDIT' => "startshop_settings_payment_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_settings_payment.php?lang=".LANG."&action=delete&ID=#ID#",
        'ACTIVATE' => "startshop_settings_payment.php?lang=".LANG."&action=activate&ID=#ID#",
        'DEACTIVATE' => "startshop_settings_payment.php?lang=".LANG."&action=deactivate&ID=#ID#"
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopPayment::Delete($_REQUEST['ID']);

        if ($sAction == 'activate' && is_numeric($_REQUEST['ID']))
            CStartShopPayment::Update($_REQUEST['ID'], array("ACTIVE" => "Y"));

        if ($sAction == 'deactivate' && is_numeric($_REQUEST['ID']))
            CStartShopPayment::Update($_REQUEST['ID'], array("ACTIVE" => "N"));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopPayment::Delete($iItemID);

            if ($sAction == 'activate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopPayment::Update($iItemID, array("ACTIVE" => "Y"));

            if ($sAction == 'deactivate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopPayment::Update($iItemID, array("ACTIVE" => "N"));
        }
    }

    $arHandlers = CStartShopUtil::DBResultToArray(CStartShopPayment::GetHandlersList(), 'CODE');

    $sListID = "startshop_payment_list";

    $sSortBy = "ID";
    $sOrderBy = "asc";
    $oSort = new CAdminSorting(
        $sListID,
        $sSortBy,
        $sOrderBy,
        'by',
        'order'
    );

    $sSortBy = $_REQUEST['by'];
    $sOrderBy = $_REQUEST['order'];

    $oList = new CAdminList($sListID, $oSort);
    $dbResult = CStartShopPayment::GetList(array($sSortBy => $sOrderBy), array());
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
            "id" => "ACTIVE",
            "content" => GetMessage('table.header.active'),
            "sort" => "ACTIVE",
            "default" => true
        ),
        array(
            "id" => "HANDLER",
            "content" => GetMessage('table.header.handler'),
            "sort" => "HANDLER",
            "default" => true
        )
    ));

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
            "activate.selected" => GetMessage('actions.group.activate'),
            "deactivate.selected" => GetMessage('actions.group.deactivate'),
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

            if ($arResultItem['ACTIVE'] == "Y") {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.deactivate'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['DEACTIVATE'], $arResultItem))
                );
            } else {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.activate'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['ACTIVATE'], $arResultItem))
                );
            }
        }

        $sActive = $arResultItem['ACTIVE'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sHandler = !empty($arResultItem['HANDLER']) ?
            $arHandlers[$arResultItem['HANDLER']]['NAME'] :
            GetMessage('field.handler.empty');

        $oRow = &$oList->AddRow($arResultItem["ID"], array(
            "ID" => $arResultItem["ID"],
            "ACTIVE" => $sActive,
            "NAME" =>  $arResultItem['LANG'][LANGUAGE_ID]["NAME"],
            "CODE" =>  $arResultItem["CODE"],
            "SORT" =>  $arResultItem["SORT"],
            "HANDLER" => $sHandler
        ));

        $oRow->AddActions($arActions);
    }

    $arContextMenu = array();

    if ($bRightsEdit)
        $arContextMenu[] = array(
            "TEXT" => GetMessage("actions.add"),
            "ICON" => "btn_new",
            "LINK" => $arLinks['ADD'],
            "TITLE" => GetMessage("actions.add")
        );

    $oList->AddAdminContextMenu($arContextMenu, false, true);
    $oList->CheckListMode();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?$oList->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>