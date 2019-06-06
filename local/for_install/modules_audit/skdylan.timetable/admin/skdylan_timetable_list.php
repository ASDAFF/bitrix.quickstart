<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

if ($RIGHT >= "R") {

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('skdylan.timetable');

    $APPLICATION->SetTitle(GetMessage("TAMETABLE_LIST_TITLE"));

    $Table = "sk_timetable";
    $sTableID = "sk_timetable";
    $oSort = new CAdminSorting($sTableID, "SORT", "asc");
    $lAdmin = new CAdminList($sTableID, $oSort);

    if (!isset($by))
        $by = 'ID';
    if (!isset($order))
        $order = 'asc';
    $by = strtoupper($by);
    switch ($by)
    {
        case 'ID':
            $arOrder = array('ID' => $order);
            break;
        case 'NAME':
            $arOrder = array('CATALOG_TYPE' => $order, 'CATALOG_BUNDLE' => $order, 'ID' => 'ASC');
            break;
    }

    $lAdmin->InitFilter(array("filter_id", "filter_name"));
    $arFilter = Array("NAME" => $filter_name, "ID" => $filter_id);

    if(($arID = $lAdmin->GroupAction())) {

        if($_REQUEST["action_button"] == "delete")
        {
            if(is_array($_REQUEST["ID"]))
                foreach ($_REQUEST["ID"] as $item)
                    MainTimeTable::DeleteGroup($item);
            else
                MainTimeTable::DeleteGroup($_REQUEST["ID"]);
        }
    }

    $arRes = MainTimeTable::GetGroupList($arOrder, $arFilter);

    $lAdmin->AddHeaders(array(
        array("id" => "NAME",
            "content" => GetMessage("TAMETABLE_ADD_GROUP_TABLE_NAME"),
            "sort" => "name",
            "default" => true,
        ),
        array("id" => "ID",
            "content" => "ID",
            "sort" => "id",
            "default" => true,
        ),

    ));

    $arActions = Array();
    while ($item = $arRes->Fetch()) {
        $f_ID = $item["ID"];
        $row =& $lAdmin->AddRow($f_ID, $item);
        $row->AddField("ID", $f_ID);
        $row->AddViewField("ID", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["ID"].'</a>');
        $row->AddViewField("NAME", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["NAME"].'</a>');
        $arActions[] = array(
            "ICON" => "view",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_EVENTS"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_events_list.php?SID=".$f_ID.'&lang='.LANG),
            "DEFAULT" => true
        );
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_GROUP_EDIT"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_edit_group.php?SID=".$f_ID.'&lang='.LANG),
            "DEFAULT" => false
        );
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_GROUP_DELTE"),
            "ACTION" => "if(confirm('".GetMessage("TAMETABLE_ADD_GROUP_TO_DELETE_GROUP")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"),
            "DEFAULT" => false
        );
        $row->AddActions($arActions);
        unset($arActions);
    }

    $lAdmin->AddGroupActionTable(Array(
        "delete" => true,
    ));

    $aContext = array(
        array(
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_EVENTS"),
            "LINK" => "/bitrix/admin/skdylan_timetable_edit_group.php?lang=".LANG,
            "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_EVENTS"),
            "ICON" => "btn_new",
        ),
    );
    $lAdmin->AddAdminContextMenu($aContext);

    $lAdmin->CheckListMode();
}
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter($sTableID."_filter", array(GetMessage("TAMETABLE_EVENT"),GetMessage("TAMETABLE_FULL_NAME")));
?>

<form name="find_form" method="get" action="<? $APPLICATION->GetCurPage();?>">
    <? $oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("TAMETABLE_EVENT_ID")?>:</td>
        <td>
            <input type="text" name="filter_id" size="47" value="<? htmlspecialcharsex($filter_id)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_EVENT_NAME")?>:</td>
        <td>
            <input type="text" name="filter_name" size="47" value="<? htmlspecialcharsex($filter_name)?>">
        </td>
    </tr>
    <?
    $oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
    $oFilter->End();
    ?>
</form>


<?
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
