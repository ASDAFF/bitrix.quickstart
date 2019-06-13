<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$RIGHT = $APPLICATION->GetGroupRight('skdylan.timetable');

//if ($RIGHT >= "R") {}

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("TAMETABLE_EDIT_GROUP"));
CModule::IncludeModule('skdylan.timetable');

$Table = "b_timetable";
$sTableID = "tbl_sk_timetable";
$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("TAMETABLE_ADD_GROUP_OPTIONS"), "ICON" => "", "TITLE" => GetMessage("TAMETABLE_ADD_GROUP_OPTIONS"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if(isset($_REQUEST['SID']) && $_REQUEST['SID']!="")
{
    $sid = htmlspecialchars($_REQUEST['SID']);
    $group = MainTimeTable::GetGroupByID($sid);

    if($group == null)
    {
        //$APPLICATION->SetTitle($arIBTYPE["NAME"]);
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
        ShowError(GetMessage("TAMETABLE_EVENTS_ERROR_NO_GROUP"));
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
        die();
    }
    $APPLICATION->SetTitle(GetMessage("TAMETABLE_EDIT_GROUP_EDIT")." - ".MainTimeTable::GetGroupNameByID($sid));
}

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="")) {
    if(strlen($NAME)==0)
        $strError .= GetMessage("TAMETABLE_ADD_GROUP_ERROR_NO_NAME")."<br>";
}

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() && CModule::IncludeModuleEx('skdylan.timetable') && strlen($strError)==0) {

//    $checkResult[] = array(
//        'FULLNAME' => (isset($_POST["FULLNAME"]) && 'Y' == $_POST["FULLNAME"] ? 'Y' : 'N'),
//        'TELEPHONE' => (isset($_POST["TELEPHONE"]) && 'Y' == $_POST["TELEPHONE"] ? 'Y' : 'N'),
//        'EMAIL' => (isset($_POST["EMAIL"]) && 'Y' == $_POST["EMAIL"] ? 'Y' : 'N'),
//        'COMMENTS' => (isset($_POST["COMMENTS"]) && 'Y' == $_POST["COMMENTS"] ? 'Y' : 'N'),
//    );

    if(isset($sid))
        $result = MainTimeTable::SetGroup($sid,array("NAME" => $NAME, "SORT" => $SORT, "ACTIVE" => "Y"));
    else
        $result = MainTimeTable::AddGroup($NAME);

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
        "LINK" => "skdylan_timetable_list.php?lang=".LANG,
        "ICON" => "btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);
$context->Show();

?>

<form method="POST" Action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
    <?=bitrix_sessid_post();?>
    <? $tabControl->Begin();?>

    <? $tabControl->BeginNextTab();?>


    <tr class="heading">
        <td colspan="2"><?=GetMessage("TAMETABLE_ADD_GROUP_INF")?></td>
    </tr>
    <tr>
        <td><b><?=GetMessage("TAMETABLE_ADD_GROUP_NAMEGROUP")?>:</b></td>
        <td><input type="text" name="NAME" value="<?=$group["NAME"];?>" size="80" maxlength="255" style="width:500px; min-width:500px; box-sizing:border-box;"></td>
    </tr>
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_GROUP_SORT")?><!--:</td>-->
<!--        <td><input type="text" name="SORT" value="--><?//=$group["SORT"];?><!--" size="80" maxlength="255" style="width:100px; min-width:100px; box-sizing:border-box;"></td>-->
<!--    </tr>-->
<!--    <tr class="heading">-->
<!--        <td colspan="2">--><?//=GetMessage("TAMETABLE_ADD_GROUP_REQUIRED_FIELD")?><!--</td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_GROUP_CHECK_FULLNAME")?><!--:</td>-->
<!--        <td><input type="checkbox" name="FULLNAME" value="Y" checked></td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_GROUP_CHECK_TELEPHONE")?><!--:</td>-->
<!--        <td><input type="checkbox" name="TELEPHONE" value="Y" checked></td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_GROUP_CHECK_EMAIL")?><!--:</td>-->
<!--        <td><input type="checkbox" name="EMAIL" value="Y" checked></td>-->
<!--    </tr>-->
<!--    <tr>-->
<!--        <td>--><?//=GetMessage("TAMETABLE_ADD_GROUP_CHECK_COMMENT")?><!--:</td>-->
<!--        <td><input type="checkbox" name="COMMENTS" value="Y"></td>-->
<!--    </tr>-->

    <? if($sid>0):?><input type="hidden" name="sid" value="<?if($sid > 0) echo $sid?>"><? endif;?>
    <? if($_REQUEST['bxpublic']=='Y'):?><input type="hidden" name="bxpublic" value="Y"><? endif;?>

    <?
    if ($RIGHT=="W") $tabControl->Buttons(array("disabled" => ($RIGHT < "W"), "back_url" => "skdylan_timetable_list.php?lang=".LANG));
        $tabControl->End();
        $tabControl->ShowWarnings("post_form", $message);
    ?>
</form>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
