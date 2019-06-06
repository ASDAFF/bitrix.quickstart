<?php


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

//if ($RIGHT >= "R") {}

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('skdylan.timetable');

$Table = "b_timetable";
$sTableID = "tbl_timetable";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

// *Настроить фильтр!!

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
    case 'EVENT':
        $arOrder = array('PROPERTY_EVENT' => $order, 'ID' => 'ASC');
        break;
}

$navResult = new CAdminResult(null, '');
$arNavParams = array("nPageSize"=>$navResult->GetNavSize(
    $sTableID,
    array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?IBLOCK_ID='.$IBLOCK_ID))
);

if($_REQUEST['SID']) {
    $sid = $_REQUEST['SID'];
    $arRes = MainTimeTable::CheckEvents($sid);
}

if(isset($_REQUEST['EventID']) && $_REQUEST['EventID']!="")
{
    $eventID = $_REQUEST['EventID'];
    $event = MainTimeTable::GetEventByID($eventID);
    if($event == null)
        $errorFlag = true;
}

if($arRes == null || $errorFlag == true)
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    ShowError(GetMessage("TAMETABLE_EVENTS_ERROR_NO_GROUP"));
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
    die();
}

$arRes = MainTimeTable::GetParticipant($arOrder,array("PROPERTY_EVENT" => $eventID),$arNavParams);

//while ($item = $arRes->Fetch()) {
//    echo "<pre>".print_r($item)."</pre>";
//}
//die();

$rsData = new CAdminResult($arRes, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("TAMETABLE_PARTICIPANT_LIST")));

$lAdmin->AddHeaders(array(

    array("id" => "NAME",
        "content" => GetMessage("TAMETABLE_FULLNAME"),
        "sort" => "name",
        "default" => true,
    ),
    array("id" => "PHONE",
        "content" => GetMessage("TAMETABLE_PARTICIPANT_PHONE"),
        "sort" => "phone",
        "default" => true,
    ),
    array("id" => "EMAIL",
        "content" => GetMessage("TAMETABLE_PARTICIPANT_EMAIL"),
        "sort" => "email",
        "default" => true,
    ),
    array("id" => "EVENT",
        "content" => GetMessage("TAMETABLE_EVENT"),
        "sort" => "event",
        "default" => true,
    ),
));

//print_r($arRes);

$arActions = Array();
while ($item = $arRes->Fetch()) {

    $item["PROPERTY_EVENT_NAME_VALUE"] = MainTimeTable::GetElementNameByID($item["PROPERTY_EVENT_VALUE"]);

    $f_ID = $item["ID"];
    $row =& $lAdmin->AddRow($f_ID, $item);

    $row->AddField("ID", $f_ID);
    $row->AddViewField("ID", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["ID"].'</a>');
    $row->AddViewField("NAME", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["NAME"].'</a>');
    $row->AddViewField("PHONE", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["PROPERTY_PHONE_NUMBER_VALUE"].'</a>');
    $row->AddViewField("EMAIL", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["PROPERTY_EMAIL_VALUE"].'</a>');
    $row->AddViewField("EVENT", '<a href="skdylan_timetable_events_list.php?SID='.$item["ID"].'&lang='.LANG.'">'.$item["PROPERTY_EVENT_NAME_VALUE"].' ('.$item["PROPERTY_EVENT_VALUE"].')</a>');

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
        "TEXT" => GetMessage("TAMETABLE_ADD_PARTICIPANT"),
        "LINK" => "/bitrix/admin/skdylan_timetable_edit_participant.php?lang=".LANG,
        "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_EVENTS"),
        "ICON" => "btn_new",
    ),
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?

if($message)
    echo $message->Show();

if($note)
    CAdminMessage::ShowNote($note);

$aMenu = array(
    array(
        "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_BACK"),
        "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_BACK"),
        "LINK" => "skdylan_timetable_events_list.php?SID=".$sid."&lang=".LANG,
        "ICON" => "btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

$dateEnd = $event["PROPERTY"]["end_event"]["VALUE"];
$dateStart = $event["PROPERTY"]["start_event"]["VALUE"];

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("TAMETABLE_ADD_GROUP_BACK"), "ICON" => "", "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_BACK")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>
    <? $tabControl->Begin();?>

    <? $tabControl->BeginNextTab();?>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_GROUP_NAME")?>:</td>
        <td><?= MainTimeTable::GetGroupNameByID($sid)?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_GROUP_NAMEGROUP")?>:</td>
        <td><?=$event["NAME"];?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_START_TIME")?></td>
        <td><?=$dateStart;?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_END_TIME")?></td>
        <td><?=$dateEnd;?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_COUNT")?>:</td>
        <td><?=$event["PROPERTY"]["count_participant"]["VALUE"]?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_SORT")?>:</td>
        <td><?=($str_SORT) ? $str_SORT:'500';?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_COMMENT")?>:</td>
        <td><textarea disabled name="COMMENT" cols="80" rows="3" style="width:500px; min-width:500px; min-height:60px; box-sizing:border-box;"><?=$event["DETAIL_TEXT"]?></textarea></td>
    </tr>
    <?
    $tabControl->BeginNextTab();



    $tabControl->End();
    $tabControl->ShowWarnings("post_form", $message);
    ?>
<br>
    <?
    $lAdmin->DisplayList();
    ?>


<?



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
