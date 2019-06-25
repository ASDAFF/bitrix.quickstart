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

    $arLinks = array(
        'ADD' => "startshop_forms_edit.php?lang=".LANG."&action=add",
        'EDIT' => "startshop_forms_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_forms.php?lang=".LANG."&action=delete&ID=#ID#",
        'FIELDS' => "startshop_forms_fields.php?lang=".LANG."&FORM_ID=#ID#",
        'RESULTS' => "startshop_forms_results.php?lang=".LANG."&FORM_ID=#ID#",
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopForm::Delete($_REQUEST['ID']);

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopForm::Delete($iItemID);
        }
    }

    $sListID = "startshop_forms_list";

    $sSortBy = "ID";
    $sOrderBy = "asc";
    $oSort = new CAdminSorting(
        $sListID,
        $sSortBy,
        $sOrderBy,
        $sListID.'_by',
        $sListID.'_orders'
    );

    $sSortBy = $_REQUEST[$sListID.'_by'];
    $sOrderBy = $_REQUEST[$sListID.'_orders'];

    $oList = new CAdminList($sListID, $oSort);
    $dbResult = CStartShopForm::GetList(array($sSortBy => $sOrderBy));
    $dbResult = new CAdminResult($dbResult, $sListID);
    $dbResult->NavStart(20);
    $oList->NavText($dbResult->GetNavPrint(GetMessage('nav.title'), true));

    $arHeaders = array(
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
            "id" => "SID",
            "content" => GetMessage('table.header.sid'),
            "default" => true
        ),
        array(
            "id" => "USE_POST",
            "content" => GetMessage('table.header.use_post'),
            "sort" => "USE_POST",
            "default" => true
        ),
        array(
            "id" => "USE_CAPTCHA",
            "content" => GetMessage('table.header.use_captcha'),
            "sort" => "USE_CAPTCHA",
            "default" => true
        )
    );

    $oList->AddHeaders($arHeaders);

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
            "clear_results.selected" => GetMessage('actions.group.clear_results')
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
        }

        $arActions[] = array(
            "TEXT" => GetMessage('actions.results'),
            "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['RESULTS'], $arResultItem))
        );

        $arActions[] = array(
            "TEXT" => GetMessage('actions.fields'),
            "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['FIELDS'], $arResultItem))
        );

        $sUsePost = $arResultItem["USE_POST"] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sUseCaptcha = $arResultItem["USE_POST"] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $oRow = &$oList->AddRow($arResultItem["ID"], array(
            'ID' => $arResultItem['ID'],
            'NAME' => $arResultItem['LANG'][LANGUAGE_ID]['NAME'],
            'CODE' => $arResultItem['CODE'],
            'SORT' => $arResultItem['SORT'],
            'SID' => implode(', ', $arResultItem['SID']),
            'USE_POST' => $sUsePost,
            'USE_CAPTCHA' => $sUseCaptcha
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
