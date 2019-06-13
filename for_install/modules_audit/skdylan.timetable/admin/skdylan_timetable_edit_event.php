<?php


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

//if ($RIGHT >= "R") {}

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('skdylan.timetable');

$Table = "b_timetable";
$sTableID = "tbl_sk_timetable";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

if($_REQUEST['SID']) {
    $sid = htmlspecialcharsbx($_REQUEST['SID']);
    $arRes = MainTimeTable::CheckEvents($sid);
}

if(isset($_REQUEST['EventID']) && $_REQUEST['EventID']!="")
{
    $eventID = htmlspecialcharsbx($_REQUEST['EventID']);
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

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("TAMETABLE_ADD_GROUP_BACK"), "ICON" => "", "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_BACK"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="")) {

    if(strlen($NAME)==0 && $START==0 && $END==0)
        $strError .= GetMessage("TAMETABLE_ADD_EVENT_ERROR_NO_NAME")."<br>";
    // *-�������� �����
}

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() && CModule::IncludeModuleEx('skdylan.timetable') && strlen($strError)==0) {

    if(isset($EID)) {
        $result = MainTimeTable::SetEvent($EID, $NAME, $START_TIME, $END_TIME, $COUNT, $COMMENT);
        if($result != false)
            LocalRedirect($APPLICATION->GetCurUri());
    }
    else
        $result = MainTimeTable::AddEvent($ID, $NAME, $START_TIME, $END_TIME, $COUNT, $COMMENT);

    if(!$result) {
        $message = new CAdminMessage(GetMessage("TAMETABLE_ADD_GROUP_ERROR_ADD"));
    }
    else
        $note = GetMessage("TAMETABLE_ADD_GROUP_ADD_SUCCESS");
}
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

if($event["PROPERTY"]["end_event"]["VALUE"])
    $dateEnd = $event["PROPERTY"]["end_event"]["VALUE"];
else
    $dateEnd = date('d.m.Y H:i:s');

if($event["PROPERTY"]["start_event"]["VALUE"])
    $dateStart = $event["PROPERTY"]["start_event"]["VALUE"];
else
    $dateStart = date('d.m.Y H:i:s');

?>

<form method="POST" Action="<?=$APPLICATION->GetCurPageParam()?>" ENCTYPE="multipart/form-data" name="post_form">
    <?=bitrix_sessid_post();?>
    <? $tabControl->Begin();?>

    <? $tabControl->BeginNextTab();?>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_GROUP_NAME")?>:</td>
        <td><b><?= MainTimeTable::GetGroupNameByID($sid)?></b></td>
    </tr>
    <tr>
        <td><b><?=GetMessage("TAMETABLE_ADD_GROUP_NAMEGROUP")?>:</b></td>
        <td><input type="text" name="NAME" value="<?=$event["NAME"];?>" size="80" maxlength="255" style="width:500px; min-width:500px; box-sizing:border-box;"></td>
    </tr>
    <tr>
        <td><b><?=GetMessage("TAMETABLE_ADD_EVENT_START_TIME")?>:</b></td>
        <td><?=CalendarDate("START_TIME", $dateStart, "post_form", "15")?></td>
    </tr>
    <tr>
        <td><b><?=GetMessage("TAMETABLE_ADD_EVENT_END_TIME")?>:</b></td>
        <td><?=CalendarDate("END_TIME", $dateEnd, "post_form", "15")?></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_COUNT")?>:</td>
        <td><input type="text" name="COUNT" value="<?=($event["PROPERTY"]["count_participant"]["VALUE"]) ? $event["PROPERTY"]["count_participant"]["VALUE"]:'0'?>" size="80" maxlength="255" style="width:100px; min-width:100px; box-sizing:border-box;"></td>
    </tr>
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_EVENT_SORT")?><!--:</td>-->
<!--        <td><input type="text" name="SORT" value="--><?//=($str_SORT) ? $str_SORT:'500';?><!--" size="80" maxlength="255" style="width:100px; min-width:100px; box-sizing:border-box;"></td>-->
<!--    </tr>-->
    <tr>
        <td><?=GetMessage("TAMETABLE_ADD_EVENT_COMMENT")?>:</td>
        <td><textarea name="COMMENT" cols="80" rows="3" style="width:500px; min-width:500px; min-height:60px; box-sizing:border-box;"><?=$event["DETAIL_TEXT"]?></textarea></td>
    </tr>

    <? if($eventID>0):?><input type="hidden" name="EID" value="<?if($eventID>0) echo $eventID?>"><? endif;?>
    <? if($sid>0):?><input type="hidden" name="ID" value="<?if($sid>0) echo $sid?>"><? endif;?>
    <? if($_REQUEST['bxpublic']=='Y'):?><input type="hidden" name="bxpublic" value="Y"><? endif;?>


    <?
    if ($RIGHT=="W") $tabControl->Buttons(array("disabled" => ($RIGHT < "W"), "back_url" => "skdylan_timetable_events_list.php?SID=".$sid."&lang=".LANG));
    $tabControl->End();
    $tabControl->ShowWarnings("post_form", $message);
    ?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
