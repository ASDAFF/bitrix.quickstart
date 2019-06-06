<?php



require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

$APPLICATION->SetTitle(GetMessage("TAMETABLE_EDIT_PARTICIPANT_TITLE"));

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('skdylan.timetable');

$Table = "b_timetable";
$sTableID = "tbl_sk_timetable";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

if(isset($_REQUEST['SID']) && $_REQUEST['SID']!="")
{
    $sid = htmlspecialchars($_REQUEST['SID']);
    $participant = MainTimeTable::GetParticipantByID($sid);
    if($event == null)
        $errorFlag = true;
}

if(isset($_REQUEST['EID']) && $_REQUEST['EID']!=""){
    $eventID = $_REQUEST['EID'];
}


$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("TAMETABLE_PARTICIPANT"), "ICON" => "", "TITLE" => GetMessage("TAMETABLE_PARTICIPANT"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="")) {

    if(strlen($NAME)==0 && !isset($EVENT_ID))
        $strError .= GetMessage("TAMETABLE_ADD_EVENT_ERROR_NO_NAME")."<br>";
    // *-�������� �����
}

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() && CModule::IncludeModuleEx('skdylan.timetable') && strlen($strError)==0) {

    if(isset($SID)) {
        $result = MainTimeTable::SetParticipant($SID, $EVENT_ID, $NAME, $PHONE, $EMAIL, $COMMENT);
        if($result != false)
            LocalRedirect($APPLICATION->GetCurUri());
    }
    else
    {
        if($NAME == "")
            $errorMessage.= GetMessage("TAMETABLE_EDIT_PARTICIPANT_ERROR_NONAME")."<br>";
        if($EVENT_ID == "")
            $errorMessage.= GetMessage("TAMETABLE_EDIT_PARTICIPANT_ERROR_NOEVENT")."<br>";
        if($errorMessage == "")
            $result = MainTimeTable::AddParticipant($EVENT_ID, $NAME, $PHONE, $EMAIL, $COMMENT);
    }

    if(isset($errorMessage)) {
        $message = new CAdminMessage($errorMessage);
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

if(isset($eventID))
    $aMenu = array(
        array(
            "TEXT" => GetMessage("TAMETABLE_ADD_PART_BACK_TO")."".MainTimeTable::GetElementNameByID($eventID),
            "TITLE" => GetMessage("TAMETABLE_ADD_PART_BACK"),
            "LINK" => "skdylan_timetable_view_event.php?EventID=".$eventID."&lang=".LANG,
            "ICON" => "btn_list",
        )
    );
else
    $aMenu = array(
        array(
            "TEXT" => GetMessage("TAMETABLE_ADD_PART_BACK"),
            "TITLE" => GetMessage("TAMETABLE_ADD_PART_BACK"),
            "LINK" => "skdylan_timetable_participant_event_list.php?SID=".$sid."&lang=".LANG,
            "ICON" => "btn_list",
        )
    );



$context = new CAdminContextMenu($aMenu);
$context->Show();

if(isset($eventID))
    $EID = $eventID;
if(isset($participant["PROPERTY"]["event"]["VALUE"]))
    $EID = $participant["PROPERTY"]["event"]["VALUE"];

$APPLICATION->SetTitle(GetMessage("TAMETABLE_EDIT_PARTICIPANT_TITLE_EDIT")." - ".$participant["NAME"]);
?>

<form method="POST" Action="<?=$APPLICATION->GetCurPageParam()?>" ENCTYPE="multipart/form-data" name="post_form">
    <?=bitrix_sessid_post();?>
    <? $tabControl->Begin();?>

    <? $tabControl->BeginNextTab();?>
    <tr>
        <td><b><?=GetMessage("TAMETABLE_FULLNAME")?>:</b></td>
        <td><input type="text" name="NAME" value="<?=$participant["NAME"];?>" size="80" maxlength="255" style="width:500px; min-width:500px; box-sizing:border-box;"></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_PHONE")?>:</td>
        <td><input type="text" name="PHONE" value="<?=$participant["PROPERTY"]["phone_number"]["VALUE"];?>" size="80" maxlength="255" style="width:500px; min-width:500px; box-sizing:border-box;"></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_EMAIL")?>:</td>
        <td><input type="text" name="EMAIL" value="<?=$participant["PROPERTY"]["email"]["VALUE"];?>" size="80" maxlength="255" style="width:500px; min-width:500px; box-sizing:border-box;"></td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_COMMENT")?>:</td>
        <td><textarea name="COMMENT" cols="80" rows="3" style="width:500px; min-width:500px; min-height:60px; box-sizing:border-box;"><?=$participant["PROPERTY"]["comment"]["VALUE"]?></textarea></td>
    </tr>

    <tr class="as-tt-shedule-id">
        <td><span id="as-tt-shedule-id-hint"></span><script type="text/javascript">BX.hint_replace(BX('as-tt-shedule-id-hint'), '<?=GetMessage("TAMETABLE_EVENT_HELP")?>');</script>&nbsp;<b><?=GetMessage("TAMETABLE_EVENT")?> (ID):</b></td>
        <td>
            <input name="EVENT_ID" id="IBLOCK_ELEMENT_ID" value="<?=$EID?>" size="5" type="text" data-cip-id="IBLOCK_ELEMENT_ID">
            <input type="button" value="..." onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=ru&IBLOCK_ID=0&n=IBLOCK_ELEMENT_ID&k=iei', 900, 700);">&nbsp <?=MainTimeTable::GetElementNameByID($EID)?></td>

    </tr>
    <!-- � �� ���� ��� ���-->
    <? if($sid>0):?><input type="hidden" name="ID" value="<?if($sid > 0) echo($sid)?>"><? endif;?>
    <? if($_REQUEST['bxpublic']=='Y'):?><input type="hidden" name="bxpublic" value="Y"><? endif;?>


    <?
    if ($RIGHT=="W") $tabControl->Buttons(array("disabled" => ($RIGHT < "W"), "back_url" => "skdylan_timetable_participant_event_list.php?lang=".LANG));
    $tabControl->End();
    $tabControl->ShowWarnings("post_form", $message);
    ?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
