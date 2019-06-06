<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

if ($RIGHT >= "R") {

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('skdylan.timetable');

    $Table = "sk_timetable";
    $sTableID = "sk_timetable";
    $oSort = new CAdminSorting($sTableID, "SORT", "asc");
    $lAdmin = new CAdminList($sTableID, $oSort);

    // *Настроить фильтр!!

    $lAdmin->InitFilter(array("find_name"));
    $arFilter = Array("NAME" => $find_name);

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

    $arRes = MainTimeTable::GetGroupList();
    //$rsData = new CAdminResult($rsData, $sTableID);
    //$rsData->NavStart("20");

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
//            array("id" => "SORT",
//                "content" => GetMessage("shedule_list_SORTIROVKA"),
//                "sort" => "sort",
//                "default" => true,
//            ),
//            array("id" => "TIMESTAMP",
//                "content" => GetMessage("shedule_list_IZMENENO"),
//                "sort" => "timestamp",
//                "default" => true,
//            ),

    ));



    $arActions = Array();
    foreach ($arRes as $item) {
        $f_ID = $item["ID"];
        $row =& $lAdmin->AddRow($f_ID, $item);
        $row->AddField("ID", $f_ID);
        $row->AddViewField("ID", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["ID"].'</a>');
        $row->AddViewField("NAME", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["NAME"].'</a>');
        $arActions[] = array(
            "ICON" => "view",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_EVENTS"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_events_list.php?SID=".$f_ID),
            "DEFAULT" => true
        );
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_GROUP_EDIT"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_edit_group.php?ID=".$f_ID),
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
            "LINK" => "/bitrix/admin/skdylan_timetable_add_group.php?lang=".LANG,
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
?>

<?

$lAdmin->DisplayList();

?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
