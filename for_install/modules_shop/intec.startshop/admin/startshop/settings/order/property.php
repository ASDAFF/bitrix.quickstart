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
            'STARTSHOP_SETTINGS_ORDER_PROPERTY',
            'V'
        ) || $USER->IsAdmin();

    $bRightsEdit = CStartShopUtilsRights::AllowedForGroups(
            $USER->GetUserGroupArray(),
            'STARTSHOP_SETTINGS_ORDER_PROPERTY',
            'E'
        ) || $USER->IsAdmin();

    if (!$bRightsView) {
        include($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/404.php');
        die();
    }

    $arLinks = array(
        'ADD' => "startshop_settings_order_property_edit.php?lang=".LANG."&action=add&SID=#SITE_ID#",
        'EDIT' => "startshop_settings_order_property_edit.php?lang=".LANG."&action=edit&ID=#ID#",
        'DELETE' => "startshop_settings_order_property.php?lang=".LANG."&action=delete&ID=#ID#",
        'ACTIVATE' => "startshop_settings_order_property.php?lang=".LANG."&action=activate&ID=#ID#",
        'DEACTIVATE' => "startshop_settings_order_property.php?lang=".LANG."&action=deactivate&ID=#ID#",
        'REQUIRE' => "startshop_settings_order_property.php?lang=".LANG."&action=require&ID=#ID#",
        'UNREQUIRE' => "startshop_settings_order_property.php?lang=".LANG."&action=unrequire&ID=#ID#",
    );

    $APPLICATION->SetTitle(GetMessage('title'));

    $sAction = $_REQUEST['action'];
    $arActionGroup = explode('&', $sAction);

    $arUserFields = array(
        'LOGIN' => GetMessage('user_fields.login'),
        'EMAIL' => GetMessage('user_fields.email'),
        'NAME' => GetMessage('user_fields.name'),
        'LAST_NAME' => GetMessage('user_fields.last_name'),
        'SECOND_NAME' => GetMessage('user_fields.second_name'),
        'PERSONAL_PROFESSION' => GetMessage('user_fields.personal_profession'),
        'PERSONAL_WWW' => GetMessage('user_fields.personal_www'),
        'PERSONAL_ICQ' => GetMessage('user_fields.personal_icq'),
        'PERSONAL_PHONE' => GetMessage('user_fields.personal_phone'),
        'PERSONAL_FAX' => GetMessage('user_fields.personal_fax'),
        'PERSONAL_MOBILE' => GetMessage('user_fields.personal_mobile'),
        'PERSONAL_PAGER' => GetMessage('user_fields.personal_pager'),
        'PERSONAL_STREET' => GetMessage('user_fields.personal_street'),
        'PERSONAL_MAILBOX' => GetMessage('user_fields.personal_mailbox'),
        'PERSONAL_CITY' => GetMessage('user_fields.personal_city'),
        'PERSONAL_STATE' => GetMessage('user_fields.personal_state'),
        'PERSONAL_ZIP' => GetMessage('user_fields.personal_zip'),
        'PERSONAL_COUNTRY' => GetMessage('user_fields.personal_country'),
        'WORK_COMPANY' => GetMessage('user_fields.work_company'),
        'WORK_DEPARTMENT' => GetMessage('user_fields.work_department'),
        'WORK_POSITION' => GetMessage('user_fields.work_position'),
        'WORK_WWW' => GetMessage('user_fields.work_www'),
        'WORK_PHONE' => GetMessage('user_fields.work_phone'),
        'WORK_FAX' => GetMessage('user_fields.work_fax'),
        'WORK_PAGER' => GetMessage('user_fields.work_pager'),
        'WORK_STREET' => GetMessage('user_fields.work_street'),
        'WORK_MAILBOX' => GetMessage('user_fields.work_mailbox'),
        'WORK_CITY' => GetMessage('user_fields.work_city'),
        'WORK_STATE' => GetMessage('user_fields.work_state'),
        'WORK_ZIP' => GetMessage('user_fields.work_zip'),
        'WORK_COUNTRY' => GetMessage('user_fields.work_country')
    );

    $dbUserTypeEntities = CUserTypeEntity::GetList(array(), array(
        'ENTITY_ID' => 'USER',
        'USER_TYPE_ID' => 'string',
        'LANG' => LANGUAGE_ID
    ));

    while ($arUserTypeEntity = $dbUserTypeEntities->Fetch())
        $arUserFields[$arUserTypeEntity['FIELD_NAME']] = $arUserTypeEntity['LIST_COLUMN_LABEL'];

    if (count($arActionGroup) == 2) {
        $sAction = $arActionGroup[0];
        $_REQUEST['table_id'] = "startshop_order_property_list_".$arActionGroup[1];
    }

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopOrderProperty::Delete($_REQUEST['ID']);

        if ($sAction == 'activate' && is_numeric($_REQUEST['ID']))
            CStartShopOrderProperty::Update($_REQUEST['ID'], array("ACTIVE" => "Y"));

        if ($sAction == 'deactivate' && is_numeric($_REQUEST['ID']))
            CStartShopOrderProperty::Update($_REQUEST['ID'], array("ACTIVE" => "N"));

        if ($sAction == 'require' && is_numeric($_REQUEST['ID']))
            CStartShopOrderProperty::Update($_REQUEST['ID'], array("REQUIRED" => "Y"));

        if ($sAction == 'unrequire' && is_numeric($_REQUEST['ID']))
            CStartShopOrderProperty::Update($_REQUEST['ID'], array("REQUIRED" => "N"));

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderProperty::Delete($iItemID);

            if ($sAction == 'activate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderProperty::Update($iItemID, array("ACTIVE" => "Y"));

            if ($sAction == 'deactivate.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderProperty::Update($iItemID, array("ACTIVE" => "N"));

            if ($sAction == 'require.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderProperty::Update($iItemID, array("REQUIRED" => "Y"));

            if ($sAction == 'unrequire.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopOrderProperty::Update($iItemID, array("REQUIRED" => "N"));
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
        $arListsID[] = "startshop_order_property_list_".$arSite['ID'];

    if (!in_array($_REQUEST['table_id'], $arListsID))
        unset($_REQUEST['table_id']);

    foreach ($arSites as $arSite) {
        $sListID = "startshop_order_property_list_".$arSite['ID'];

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
        $dbResult = CStartShopOrderProperty::GetList(array($sSortBy => $sOrderBy), array("SID" => $arSite['ID']));
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
                "id" => "TYPE",
                "content" => GetMessage('table.header.type'),
                "sort" => "TYPE",
                "default" => true
            ),
            array(
                "id" => "USER_FIELD",
                "content" => GetMessage('table.header.user_field'),
                "sort" => "USER_FIELD",
                "default" => true
            ),
        ));

        if ($bRightsEdit)
            $oList->AddGroupActionTable(array(
                "delete.selected&".$arSite['ID'] => GetMessage('actions.group.delete'),
                "activate.selected&".$arSite['ID'] => GetMessage('actions.group.activate'),
                "deactivate.selected&".$arSite['ID'] => GetMessage('actions.group.deactivate'),
                "require.selected&".$arSite['ID'] => GetMessage('actions.group.require'),
                "unrequire.selected&".$arSite['ID'] => GetMessage('actions.group.unrequire'),
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

                if ($arResultItem['REQUIRED'] == 'Y') {
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
            }

            $sActive = $arResultItem['ACTIVE'] == 'Y' ?
                GetMessage('active.yes') :
                GetMessage('active.no');

            $sRequired = $arResultItem['REQUIRED'] == 'Y' ?
                GetMessage('active.yes') :
                GetMessage('active.no');

            $sUserField = GetMessage('select.empty');

            if (!empty($arResultItem['USER_FIELD']))
                if (array_key_exists($arResultItem['USER_FIELD'], $arUserFields)) {
                    $sUserField = '['.$arResultItem['USER_FIELD'].'] '.$arUserFields[$arResultItem['USER_FIELD']];
                } else {
                    $sUserField = '['.$arResultItem['USER_FIELD'].']';
                }

            $sType = GetMessage("property.type.".$arResultItem["TYPE"].((!empty($arResultItem["SUBTYPE"])) ? '.'.$arResultItem["SUBTYPE"] : ''));

            $oRow = &$oList->AddRow($arResultItem["ID"], array(
                "ID" => $arResultItem["ID"],
                "NAME" => $arResultItem['LANG'][LANGUAGE_ID]["NAME"],
                "CODE" => $arResultItem["CODE"],
                "SORT" => $arResultItem["SORT"],
                "ACTIVE" => $sActive,
                "REQUIRED" => $sRequired,
                "TYPE" => !empty($sType) ? $sType : GetMessage('property.type.custom'),
                "USER_FIELD" => $sUserField
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

        $oList->AddAdminContextMenu($arContextMenu, false, true);
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