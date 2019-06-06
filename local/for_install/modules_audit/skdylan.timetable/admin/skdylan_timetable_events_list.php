<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

if ($RIGHT >= "R") {

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('skdylan.timetable');

    if($_REQUEST['SID'] || $SID != null) {
        $sid = htmlspecialcharsbx($_REQUEST['SID']);
        $APPLICATION->SetTitle(GetMessage("TAMETABLE_EVENT_LIST_GROUP_TITLE")." - ".MainTimeTable::GetGroupNameByID($sid));
        $check = MainTimeTable::CheckEvents($sid);
    }

    if($check == null)
    {
        //$APPLICATION->SetTitle($arIBTYPE["NAME"]);
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
        ShowError(GetMessage("TAMETABLE_EVENTS_ERROR_NO_GROUP"));
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
        die();
    }

    $Table = "b_timetable";
    $sTableID = "tbl_timetable";
    $oSort = new CAdminSorting($sTableID, "SORT", "asc");
    $lAdmin = new CAdminList($sTableID, $oSort);

    $sThisSectionUrl = '&SID='.$sid.'&lang='.LANG;

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
            $arOrder = array('NAME' => $order, 'ID' => 'ASC');
            break;
        case 'TIME_EVENT_START':
            $arOrder = array('PROPERTY_START_EVENT' => $order, 'ID' => 'ASC');
            break;
        case 'TIME_EVENT_END':
            $arOrder = array('PROPERTY_END_EVENT' => $order, 'ID' => 'ASC');
            break;
//        default:
//            $arOrder = array($by => $order, 'ID' => 'ASC');
//            break;
    }

    $lAdmin->InitFilter(array("filter_name", "filter_ID", "filter_active"));
    $arFilter = Array("NAME" => $filter_name, "ID" => $filter_ID, "ACTIVE" =>$filter_active);

     if(($arID = $lAdmin->GroupAction())) {
        if($_REQUEST["action_button"] == "delete")
        {
            if(is_array($_REQUEST["ID"]))
                foreach ($_REQUEST["ID"] as $item)
                    MainTimeTable::DeleteEvent($item);
            else
                MainTimeTable::DeleteEvent($_REQUEST["ID"]);
        }
         if($_REQUEST["action_button"] == "deactive")
         {
             if(is_array($_REQUEST["ID"]))
                 foreach ($_REQUEST["ID"] as $item)
                     MainTimeTable::SetActiveEventByID($_REQUEST["ID"], "N");
             else
                 MainTimeTable::SetActiveEventByID($_REQUEST["ID"], "N");
         }
         if($_REQUEST["action_button"] == "active")
         {
             if(is_array($_REQUEST["ID"]))
                 foreach ($_REQUEST["ID"] as $item)
                     MainTimeTable::SetActiveEventByID($_REQUEST["ID"], "Y");
             else
                 MainTimeTable::SetActiveEventByID($_REQUEST["ID"], "Y");
         }
    }

    $navResult = new CAdminResult(null, '');
    $arNavParams = array("nPageSize"=>$navResult->GetNavSize(
        $sTableID,
        array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?IBLOCK_ID='.$IBLOCK_ID))
    );
    unset($navResult);

    $arRes = MainTimeTable::GetListOfEvents($sid, $arOrder, $arFilter);

//    $rsData = new CAdminResult($arRes, $sTableID);
//    $rsData->NavStart();
//    $lAdmin->NavText($rsData->GetNavPrint(""));

    $lAdmin->AddHeaders(array(
        array("id" => "NAME",
            "content" => GetMessage("TAMETABLE_ADD_EVENT_TABLE_NAME"),
            "sort" => "name",
            "default" => true,
        ),
        array("id" => "time_event_start",
            "content" => GetMessage("TAMETABLE_ADD_EVENT_TIME_EVENT_START"),
            "sort" => "time_event_start",
            "default" => true,
        ),
        array("id" => "time_event_end",
            "content" => GetMessage("TAMETABLE_ADD_EVENT_TIME_EVENT_END"),
            "sort" => "time_event_end",
            "default" => true,
        ),
        array("id" => "count_participant",
            "content" => GetMessage("TAMETABLE_ADD_EVENT_COUNT_PARTICIPANT"),
            //"sort" => "id",
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
        $row->AddViewField("ID", '<a href="skdylan_timetable_view_event.php?SID='.$sid.'&EventID='.$f_ID.'&lang='.LANG.'">'.$item["ID"].'</a>');
        $row->AddViewField("NAME", '<a href="skdylan_timetable_view_event.php?SID='.$sid.'&EventID='.$f_ID.'&lang='.LANG.'">'.$item["NAME"].'</a>');
        $row->AddViewField("time_event_start", $item["PROPERTY_START_EVENT_VALUE"]);
        $row->AddViewField("time_event_end", $item["PROPERTY_END_EVENT_VALUE"]);

        $countOfParticipant = MainTimeTable::GetCountOfParticipant($item["ID"]);

        if($item["PROPERTY_COUNT_PARTICIPANT_VALUE"] == 0)
            $count_participant = $countOfParticipant;
        else
            $count_participant = $countOfParticipant." / ".$item["PROPERTY_COUNT_PARTICIPANT_VALUE"];
        $row->AddViewField("count_participant", $count_participant);

        $arActions[] = array(
            "ICON" => "view",
            "TEXT" => GetMessage("TAMETABLE_EVENT_LIST_VIEW"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_view_event.php?SID=".$sid."&EventID=".$f_ID.'&lang='.LANG),
            "DEFAULT" => true
        );
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("TAMETABLE_EVENT_LIST_EDIT"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_edit_event.php?SID=".$sid."&EventID=".$f_ID.'&lang='.LANG),
            "DEFAULT" => false
        );
        $arActions[] = array("SEPARATOR"=>true);
        if($item["ACTIVE"] == "Y")
            $arActions[] = array(
                "TEXT" => GetMessage("TAMETABLE_EVENT_LIST_DEACTIVE"),
                "ACTION" => $lAdmin->ActionDoGroup($f_ID, "deactive", $sThisSectionUrl),
                "DEFAULT" => false
            );
        else
            $arActions[] = array(
                "TEXT" => GetMessage("TAMETABLE_EVENT_LIST_ACTIVE"),
                //"ACTION" => MainTimeTable::SetActiveEventByID($f_ID, "Y"),
                "ACTION" => $lAdmin->ActionDoGroup($f_ID, "active", $sThisSectionUrl),
                "DEFAULT" => false
            );
        $arActions[] = array("SEPARATOR"=>true);
        $arActions[] = array(
            "ICON" => "delete",
            "TEXT" => GetMessage("TAMETABLE_EVENT_LIST_DEL"),
            "ACTION" => "if(confirm('".GetMessage("TAMETABLE_EVENT_LIST_DELETE")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete", $sThisSectionUrl),
            "DEFAULT" => false
        );
        $row->AddActions($arActions);
        unset($arActions);
    }

    $lAdmin->AddGroupActionTable(Array(
        "delete" => GetMessage("TAMETABLE_EVENT_LIST_DEL"),
        "deactive" => GetMessage("SKDYLAN_TIMETABLE_DEAKTIVIROVATQ"),
        "active" => GetMessage("TAMETABLE_EVENT_LIST_ACTIVE")

    ));

    $aContext = array(
        array(
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_EVENTS"),
            "LINK" => "/bitrix/admin/skdylan_timetable_edit_event.php?SID=".$sid."&lang=".LANG,
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
$aMenu = array(
    array(
        "TEXT" => GetMessage("TAMETABLE_EVENT_BACK"),
        "TITLE" => GetMessage("TAMETABLE_EVENT_BACK"),
        "LINK" => "skdylan_timetable_list.php?&lang=".LANG,
        "ICON" => "btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

$oFilter = new CAdminFilter($sTableID."_filter", array(GetMessage("TAMETABLE_EVENT"),GetMessage("TAMETABLE_FULL_NAME")));
?>

<form name="find_form" method="get" action="<? $APPLICATION->GetCurPage();?>">
    <? $oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("TAMETABLE_EVENT_FILTER_NAME")?>:</td>
        <td>
            <input type="text" name="filter_name" size="47" value="<? htmlspecialcharsex($find_name)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_FILTER_ID")?>:</td>
        <td>
            <input type="text" name="filter_ID" size="47" value="<? htmlspecialcharsex($full_name)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_FILTER_ACTIVE")?>:</td>
        <td>
            <select name="filter_active">
                <option value=""><?=GetMessage("TAMETABLE_ACTIVE_ALL")?></option>
                <option value="Y"><?=GetMessage("TAMETABLE_ACTIVE")?></option>
                <option value="N"><?=GetMessage("TAMETABLE_DEACTIVE")?></option>
            </select>
        </td>
    </tr>
    <input type="hidden" name="SID" value="<?if($sid > 0) echo $sid?>">
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
