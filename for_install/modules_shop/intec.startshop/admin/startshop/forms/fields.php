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

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');

    $arLinks = array(
        'ADD' => "startshop_forms_fields_edit.php?lang=".LANG."&action=add&FORM_ID=".$_REQUEST['FORM_ID'],
        'EDIT' => "startshop_forms_fields_edit.php?lang=".LANG."&action=edit&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'DELETE' => "startshop_forms_fields.php?lang=".LANG."&action=delete&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'ACTIVATE' => "startshop_forms_fields.php?lang=".LANG."&action=activate&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'DEACTIVATE' => "startshop_forms_fields.php?lang=".LANG."&action=deactivate&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'REQUIRE' => "startshop_forms_fields.php?lang=".LANG."&action=require&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'UNREQUIRE' => "startshop_forms_fields.php?lang=".LANG."&action=unrequire&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'READONLY' => "startshop_forms_fields.php?lang=".LANG."&action=readonly&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'UNREADONLY' => "startshop_forms_fields.php?lang=".LANG."&action=unreadonly&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'BACK' => "/bitrix/admin/startshop_forms.php?lang=".LANG
    );

    $arItem = CStartShopForm::GetByID($_REQUEST['FORM_ID'])->Fetch();

    if (empty($arItem)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    $APPLICATION->SetTitle(GetMessage('title', array('#FORM#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Delete($_REQUEST['ID']);

        if ($sAction == 'activate' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("ACTIVE" => "Y"));

        if ($sAction == 'deactivate' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("ACTIVE" => "N"));

        if ($sAction == 'require' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("REQUIRED" => "Y"));

        if ($sAction == 'unrequire' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("REQUIRED" => "N"));

        if ($sAction == 'readonly' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("READONLY" => "Y"));

        if ($sAction == 'unreadonly' && is_numeric($_REQUEST['ID']))
            CStartShopFormProperty::Update($_REQUEST['ID'], array("READONLY" => "N"));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Delete($iItemID);

            if ($sAction == 'activate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("ACTIVE" => "Y"));

            if ($sAction == 'deactivate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("ACTIVE" => "N"));

            if ($sAction == 'require.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("REQUIRED" => "Y"));

            if ($sAction == 'unrequire.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("REQUIRED" => "N"));

            if ($sAction == 'readonly.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("READONLY" => "Y"));

            if ($sAction == 'unreadonly.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormProperty::Update($iItemID, array("READONLY" => "N"));
        }
    }

    $arTypes = array(
        STARTSHOP_FORM_PROPERTY_TYPE_TEXT => GetMessage('types.text'),
        STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA => GetMessage('types.textarea'),
        STARTSHOP_FORM_PROPERTY_TYPE_RADIO => GetMessage('types.radio'),
        STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX => GetMessage('types.checkbox'),
        STARTSHOP_FORM_PROPERTY_TYPE_SELECT => GetMessage('types.select'),
        STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT => GetMessage('types.multiselect'),
        STARTSHOP_FORM_PROPERTY_TYPE_PASSWORD => GetMessage('types.password'),
        STARTSHOP_FORM_PROPERTY_TYPE_HIDDEN => GetMessage('types.hidden')
    );

    $sSectionCurrent = 'FIELDS';
    $sListID = "startshop_form_fields_list";

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
    $dbResult = CStartShopFormProperty::GetList(array($sSortBy => $sOrderBy), array('FORM' => $arItem['ID']));
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
            "id" => "REQUIRED",
            "content" => GetMessage('table.header.required'),
            "sort" => "REQUIRED",
            "default" => true
        ),
        array(
            "id" => "READONLY",
            "content" => GetMessage('table.header.readonly'),
            "sort" => "READONLY",
            "default" => true
        ),
        array(
            "id" => "TYPE",
            "content" => GetMessage('table.header.type'),
            "sort" => "TYPE",
            "default" => true
        )
    ));

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
            "activate.selected" => GetMessage('actions.group.activate'),
            "deactivate.selected" => GetMessage('actions.group.deactivate'),
            "require.selected" => GetMessage('actions.group.require'),
            "unrequire.selected" => GetMessage('actions.group.unrequire'),
            "readonly.selected" => GetMessage('actions.group.readonly'),
            "unreadonly.selected" => GetMessage('actions.group.unreadonly'),
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

            if ($arResultItem['REQUIRED'] == "Y") {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.unrequire'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['UNREQUIRE'], $arResultItem))
                );
            } else {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.require'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['REQUIRE'], $arResultItem))
                );
            }

            if ($arResultItem['READONLY'] == "Y") {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.unreadonly'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['UNREADONLY'], $arResultItem))
                );
            } else {
                $arActions[] = array(
                    "TEXT" => GetMessage('actions.readonly'),
                    "ACTION" => $oList->ActionAjaxReload(CStartShopUtil::ReplaceMacros($arLinks['READONLY'], $arResultItem))
                );
            }
        }

        $sActive = $arResultItem['ACTIVE'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sRequired = $arResultItem['REQUIRED'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $sReadonly = $arResultItem['READONLY'] == 'Y' ?
            GetMessage('active.yes') :
            GetMessage('active.no');

        $oRow = &$oList->AddRow($arResultItem["ID"], array(
            "ID" => $arResultItem["ID"],
            "ACTIVE" => $sActive,
            "NAME" =>  $arResultItem['LANG'][LANGUAGE_ID]["NAME"],
            "CODE" =>  $arResultItem["CODE"],
            "SORT" =>  $arResultItem["SORT"],
            "REQUIRED" => $sRequired,
            "READONLY" => $sReadonly,
            "TYPE" => $arTypes[$arResultItem["TYPE"]]
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

    $oContextMenu = new CAdminContextMenu(array(
        array(
            "TEXT" => GetMessage("title.buttons.back"),
            "ICON" => "btn_list",
            "LINK" => $arLinks['BACK']
        )
    ));

    $oList->AddAdminContextMenu($arContextMenu, false, true);
    $oList->CheckListMode();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>
<?require_once('sections.php');?>
<?$oContextMenu->Show();?>
<?$oList->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>