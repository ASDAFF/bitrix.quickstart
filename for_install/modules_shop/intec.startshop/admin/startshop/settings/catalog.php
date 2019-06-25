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
        'STARTSHOP_SETTINGS_CATALOG',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CATALOG',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $arLinks = array(
        'ADD' => "startshop_settings_catalog_edit.php?lang=".LANG."&action=add",
        'EDIT' => "startshop_settings_catalog_edit.php?lang=".LANG."&action=edit&ID=#IBLOCK#",
        'DELETE' => "startshop_settings_catalog.php?lang=".LANG."&action=delete&ID=#IBLOCK#",
        'ENABLE_QUANTITY' => "startshop_settings_catalog.php?lang=".LANG."&action=enable_quantity&ID=#IBLOCK#",
        'DISABLE_QUANTITY' => "startshop_settings_catalog.php?lang=".LANG."&action=disable_quantity&ID=#IBLOCK#"
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    if ($bRightsEdit) {
        $sAction = $_REQUEST['action'];

        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopCatalog::Delete($_REQUEST['ID']);

        if ($sAction == 'enable_quantity' && is_numeric($_REQUEST['ID']))
            CStartShopCatalog::Update($_REQUEST['ID'], array('USE_QUANTITY' => 1));

        if ($sAction == 'disable_quantity' && is_numeric($_REQUEST['ID']))
            CStartShopCatalog::Update($_REQUEST['ID'], array('USE_QUANTITY' => 0));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCatalog::Delete($iItemID);

            if ($sAction == 'enable_quantity.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCatalog::Update($iItemID, array('USE_QUANTITY' => 1));

            if ($sAction == 'disable_quantity.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCatalog::Update($iItemID, array('USE_QUANTITY' => 0));
        }
    }

    $sListID = "startshop_catalog_list";

    $sSortBy = "IBLOCK";
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
    $dbResult = CStartShopCatalog::GetList(array($sSortBy => $sOrderBy), array());
    $dbResult = new CAdminResult($dbResult, $sListID);
    $dbResult->NavStart(20);
    $oList->NavText($dbResult->GetNavPrint(GetMessage('nav.title'), true));

    $oList->AddHeaders(array(
        array(
            "id" => "IBLOCK",
            "content" => GetMessage('table.header.iblock'),
            "sort" => "IBLOCK",
            "default" => true
        ),
        array(
            "id" => "USE_QUANTITY",
            "content" => GetMessage('table.header.use_quantity'),
            "sort" => "USE_QUANTITY",
            "default" => true
        )
    ));

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
            "enable_quantity.selected" => GetMessage('actions.group.enable_quantity'),
            "disable_quantity.selected" => GetMessage('actions.group.disable_quantity')
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
                "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['DELETE'], $arResultItem))
            );

            $arActions[] = array(
                "SEPARATOR" => "Y"
            );

            if ($arResultItem['USE_QUANTITY']) {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.disable_quantity'),
                    "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['DISABLE_QUANTITY'], $arResultItem))
                );
            } else {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.enable_quantity'),
                    "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['ENABLE_QUANTITY'], $arResultItem))
                );
            }
        }

        $sUseQuantity = $arResultItem['USE_QUANTITY'] ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $oRow = &$oList->AddRow($arResultItem["IBLOCK"], array(
            "IBLOCK" => $arResultItem["IBLOCK"],
            "USE_QUANTITY" => $sUseQuantity
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

    $oList->AddAdminContextMenu($arContextMenu, true, true);
    $oList->CheckListMode();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?$oList->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
