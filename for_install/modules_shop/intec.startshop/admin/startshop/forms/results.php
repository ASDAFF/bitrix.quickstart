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
        'ADD' => "startshop_forms_results_edit.php?lang=".LANG."&action=add&FORM_ID=".$_REQUEST['FORM_ID'],
        'EDIT' => "startshop_forms_results_edit.php?lang=".LANG."&action=edit&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'VIEW' => "startshop_forms_results_edit.php?lang=".LANG."&action=view&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'DELETE' => "startshop_forms_results.php?lang=".LANG."&action=delete&ID=#ID#&FORM_ID=".$_REQUEST['FORM_ID'],
        'BACK' => "/bitrix/admin/startshop_forms.php?lang=".LANG
    );

    $arItem = CStartShopForm::GetByID($_REQUEST['FORM_ID'])->Fetch();

    if (empty($arItem)) {
        LocalRedirect($arLinks['BACK']);
        die();
    }

    $arProperties = CStartShopUtil::DBResultToArray(CStartShopFormProperty::GetList(array('SORT' => 'ASC'), array('FORM' => $arItem['ID'])), 'ID');

    $APPLICATION->SetTitle(GetMessage('title', array('#FORM#' => '#'.$arItem['ID'].': '.(!empty($arItem['LANG'][LANGUAGE_ID]['NAME']) ? $arItem['LANG'][LANGUAGE_ID]['NAME'] : $arItem['CODE']))));

    $sAction = $_REQUEST['action'];

    if ($bRightsEdit) {
        if ($sAction == 'delete' && is_numeric($_REQUEST['ID']))
            CStartShopFormResult::Delete($_REQUEST['ID']);

        if (is_array($_REQUEST['ID'])) {
            if ($sAction == 'delete.selected')
                foreach ($_REQUEST['ID'] as $iItemID)
                    CStartShopFormResult::Delete($iItemID);
        }
    }

    $arFilterFields = array(
        'find_id' => 'ID',
        'find_date_create_from' => '>=DATE_CREATE',
        'find_date_create_to' => '<=DATE_CREATE',
        'find_date_modify_from' => '>=DATE_MODIFY',
        'find_date_modify_to' => '<=DATE_MODIFY',
    );

    $arFilter = array('FORM' => $arItem['ID']);

    foreach ($arFilterFields as $sFilterFieldKey => $sFilterField)
        if (!empty($_REQUEST[$sFilterFieldKey]) || is_numeric($_REQUEST[$sFilterFieldKey]))
            $arFilter[$sFilterField] = $_REQUEST[$sFilterFieldKey];

    if (!empty($arFilter['>=DATE_CREATE']))
        $arFilter['>=DATE_CREATE'] = date('Y-m-d H:i:s', strtotime($arFilter['>=DATE_CREATE']));

    if (!empty($arFilter['<=DATE_CREATE']))
        $arFilter['<=DATE_CREATE'] = date('Y-m-d H:i:s', strtotime($arFilter['<=DATE_CREATE']));

    if (!empty($arFilter['>=DATE_MODIFY']))
        $arFilter['>=DATE_MODIFY'] = date('Y-m-d H:i:s', strtotime($arFilter['>=DATE_MODIFY']));

    if (!empty($arFilter['<=DATE_MODIFY']))
        $arFilter['<=DATE_MODIFY'] = date('Y-m-d H:i:s', strtotime($arFilter['<=DATE_MODIFY']));

    $sSectionCurrent = 'RESULTS';
    $sListID = "startshop_form_results_list";

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

    $oFilter = new CAdminFilter($sListID.'_filter', array(
        GetMessage('table.header.id'),
        GetMessage('table.header.date_create'),
        GetMessage('table.header.date_modify')
    ));

    $oList = new CAdminList($sListID, $oSort);
    $dbResult = CStartShopFormResult::GetList(array($sSortBy => $sOrderBy), $arFilter);
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
            "id" => "DATE_CREATE",
            "content" => GetMessage('table.header.date_create'),
            "sort" => "DATE_CREATE",
            "default" => true
        ),
        array(
            "id" => "DATE_MODIFY",
            "content" => GetMessage('table.header.date_modify'),
            "sort" => "DATE_MODIFY",
            "default" => true
        )
    );

    foreach ($arProperties as $arProperty)
        $arHeaders[] = array(
            "id" => 'PROPERTY_'.$arProperty['ID'],
            "content" => !empty($arProperty['LANG'][LANGUAGE_ID]['NAME']) ? $arProperty['LANG'][LANGUAGE_ID]['NAME'] : $arProperty['CODE'],
            "default" => false
        );

    $oList->AddHeaders($arHeaders);

    if ($bRightsEdit)
        $oList->AddGroupActionTable(array(
            "delete.selected" => GetMessage('actions.group.delete'),
        ));

    while ($arResultItem = $dbResult->GetNext()) {
        $arActions = array();

        $arActions[] = array(
            "TEXT" => GetMessage('actions.view'),
            "ACTION" => $oList->ActionRedirect(CStartShopUtil::ReplaceMacros($arLinks['VIEW'], $arResultItem))
        );

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
        }

        $arFields = array(
            "ID" => $arResultItem["ID"],
            "DATE_CREATE" => date('d.m.Y H:i:s', strtotime($arResultItem["DATE_CREATE"])),
            "DATE_MODIFY" => date('d.m.Y H:i:s', strtotime($arResultItem["DATE_MODIFY"]))
        );

        foreach ($arResultItem['PROPERTIES'] as $iPropertyID => $cPropertyValue)
            $arFields['PROPERTY_'.$iPropertyID] = is_array($cPropertyValue) ? implode(', ', $cPropertyValue) : strval($cPropertyValue);

        $oRow = &$oList->AddRow($arResultItem["ID"], $arFields);

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
<form name="find_form" method="POST">
    <?$oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("table.header.id")?>:</td>
        <td><input type="text" name="find_id" value="<?=htmlspecialcharsbx($_REQUEST['find_id'])?>" /></td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.date_create")?></td>
        <td><?=CAdminCalendar::CalendarPeriod("find_date_create_from", "find_date_create_to", $_REQUEST['find_date_create_from'], $_REQUEST['find_date_create_to'])?></td>
    </tr>
    <tr>
        <td><?=GetMessage("table.header.date_modify")?></td>
        <td><?=CAdminCalendar::CalendarPeriod("find_date_modify_from", "find_date_modify_to", $_REQUEST['find_date_modify_from'], $_REQUEST['find_date_modify_to'])?></td>
    </tr>
    <?
    $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
    $oFilter->End();
    ?>
</form>
<?$oList->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>