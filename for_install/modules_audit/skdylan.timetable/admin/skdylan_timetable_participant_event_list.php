<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

if ($RIGHT >= "R") {

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('skdylan.timetable');
    $APPLICATION->SetTitle(GetMessage("TAMETABLE_PARTICIPANT_EVENT_TITLE"));
    $Table = "b_timetable";
    $sTableID = "tbl_timetable";
    $oSort = new CAdminSorting($sTableID, "SORT", "asc");
    $lAdmin = new CAdminList($sTableID, $oSort);

    // *��������� ������!!

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
//        default:
//            $arOrder = array($by => $order, 'ID' => 'ASC');
//            break;
    }


    $lAdmin->InitFilter(array("find_name", "full_name"));
    $arFilter = Array("NAME" => $full_name, "EVENT_NAME" => $find_name);

    if(isset($_REQUEST["SIZEN_1"]))
        $nPageSize = $_REQUEST["SIZEN_1"];


    if(($arID = $lAdmin->GroupAction())) {
        if($_REQUEST["action_button"] == "delete")
        {
            if(is_array($_REQUEST["ID"]))
                foreach ($_REQUEST["ID"] as $item)
                    MainTimeTable::DeleteParticipant($item);
            else
                MainTimeTable::DeleteParticipant($_REQUEST["ID"]);
        }
    }

    $navResult = new CAdminResult(null, '');
    $arNavParams = array("nPageSize"=>$navResult->GetNavSize(
        $sTableID,
        array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage().'?IBLOCK_ID='.$IBLOCK_ID))
    );
    unset($navResult);

    $arRes = MainTimeTable::GetParticipant($arOrder,$arFilter,$arNavParams);

    if($arRes == null)
    {
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
        ShowError(GetMessage("TAMETABLE_ERROR_NOIBLOCK"));
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
        die();
    }

    $rsData = new CAdminResult($arRes, $sTableID);
    $rsData->NavStart();
    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("TAMETABLE_PARTICIPANT_LIST")));

    //$rsData = new CAdminResult($rsData, $sTableID);
    //$rsData->NavStart("20");


    $lAdmin->AddHeaders(array(
        array("id" => "ID",
            "content" => GetMessage("TAMETABLE_PARTICIPANT_ID"),
            "sort" => "id",
            "default" => true,
        ),
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
        $row->AddViewField("ID", '<a href="skdylan_timetable_view_participant.php?SID='.$f_ID.'&lang='.LANG.'">'.$item["ID"].'</a>');
        $row->AddViewField("NAME", '<a href="skdylan_timetable_view_participant.php?SID='.$f_ID.'&lang='.LANG.'">'.$item["NAME"].'</a>');
        $row->AddViewField("PHONE", '<a href="skdylan_timetable_view_participant.php?SID='.$f_ID.'&lang='.LANG.'">'.$item["PROPERTY_PHONE_NUMBER_VALUE"].'</a>');
        $row->AddViewField("EMAIL", '<a href="skdylan_timetable_view_participant.php?SID='.$f_ID.'&lang='.LANG.'">'.$item["PROPERTY_EMAIL_VALUE"].'</a>');
        $row->AddViewField("EVENT", '<a href="skdylan_timetable_view_participant.php?SID='.$f_ID.'&lang='.LANG.'">'.$item["PROPERTY_EVENT_NAME_VALUE"].' ('.$item["PROPERTY_EVENT_VALUE"].')</a>');

        $arActions[] = array(
            "ICON" => "view",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_EVENTS"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_view_participant.php?SID=".$f_ID."&lang=".LANG),
            "DEFAULT" => true
        );
        $arActions[] = array(
            "ICON" => "edit",
            "TEXT" => GetMessage("TAMETABLE_ADD_GROUP_TO_GROUP_EDIT"),
            "ACTION" => $lAdmin->ActionRedirect("skdylan_timetable_edit_participant.php?SID=".$f_ID."&lang=".LANG),
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
}
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter($sTableID."_filter", array(GetMessage("TAMETABLE_EVENT"),GetMessage("TAMETABLE_FULL_NAME")));
?>

<form name="find_form" method="get" action="<? $APPLICATION->GetCurPage();?>">
    <? $oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("TAMETABLE_EVENT")?>:</td>
        <td>
            <input type="text" name="find_name" size="47" value="<? htmlspecialcharsex($find_name)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("TAMETABLE_FULL_NAME")?>:</td>
        <td>
            <input type="text" name="full_name" size="47" value="<? htmlspecialcharsex($full_name)?>">
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
