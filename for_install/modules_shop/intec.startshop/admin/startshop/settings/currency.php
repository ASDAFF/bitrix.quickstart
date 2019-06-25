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
        'STARTSHOP_SETTINGS_CURRENCY',
        'V'
    ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
        $USER->GetUserGroupArray(),
        'STARTSHOP_SETTINGS_CURRENCY',
        'E'
    ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $arLinks = array(
        'ADD' => "startshop_settings_currency_edit.php?lang=".LANG."&action=add",
        'EDIT' => "startshop_settings_currency_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_settings_currency.php?lang=".LANG."&action=delete&ID=#ID#",
        'ACTIVATE' => "startshop_settings_currency.php?lang=".LANG."&action=activate&ID=#ID#",
		'DEACTIVATE' => "startshop_settings_currency.php?lang=".LANG."&action=deactivate&ID=#ID#",
        'BASE' => "startshop_settings_currency.php?lang=".LANG."&action=base&ID=#ID#",
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopCurrency::Delete($_REQUEST['ID']);

        if ($sAction == 'activate' && is_numeric($_REQUEST['ID']))
            CStartShopCurrency::Update($_REQUEST['ID'], array("ACTIVE" => "Y"));

        if ($sAction == 'deactivate' && is_numeric($_REQUEST['ID']))
            CStartShopCurrency::Update($_REQUEST['ID'], array("ACTIVE" => "N"));

        if ($sAction == 'base' && is_numeric($_REQUEST['ID']))
            CStartShopCurrency::SetBase($_REQUEST['ID']);

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCurrency::Delete($iItemID);

            if ($sAction == 'activate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCurrency::Update($iItemID, array("ACTIVE" => "Y"));

            if ($sAction == 'deactivate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopCurrency::Update($iItemID, array("ACTIVE" => "N"));
        }
    }

    $sListID = "startshop_currency_list";

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
    $dbResult = CStartShopCurrency::GetList(array($sSortBy => $sOrderBy), array());
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
            "id" => "BASE",
            "content" => GetMessage('table.header.base'),
            "sort" => "BASE",
            "default" => true,
        ),
        array(
            "id" => "RATING",
            "content" => GetMessage('table.header.rating'),
            "sort" => "RATING",
            "default" => true,
        ),
        array(
            "id" => "RATE",
            "content" => GetMessage('table.header.rate'),
            "sort" => "RATE",
            "default" => true,
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

            if ($arResultItem['ACTIVE'] == 'Y') {
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

            if ($arResultItem['BASE'] != 'Y')
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.base'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['BASE'], $arResultItem))
                );
        }

        $sActive = $arResultItem['ACTIVE'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sBase = $arResultItem['BASE'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $oRow = &$oList->AddRow($arResultItem["ID"], array(
            "ID" => $arResultItem["ID"],
            "ACTIVE" => $sActive,
            "NAME" =>  $arResultItem['LANG'][LANGUAGE_ID]["NAME"],
            "CODE" =>  $arResultItem["CODE"],
            "SORT" =>  $arResultItem["SORT"],
            "BASE" => $sBase,
            "RATING" => number_format($arResultItem["RATING"], 0, '.', ' '),
            "RATE" =>  number_format($arResultItem["RATE"], 4, '.', ' ')
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
