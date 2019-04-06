<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/prolog.php");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("imaginweb.sms");
if($POST_RIGHT=="D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("rub_tab_rubric"), "ICON" => "main_user_edit", "TITLE" => GetMessage("rub_tab_rubric_title"))
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);		// Id of the edited record
$message = null;
$bVarsFromForm = false;

if($REQUEST_METHOD == "POST" && ($save != "" || $apply != "") && $POST_RIGHT == "W" && check_bitrix_sessid()){
	$rubric = new SMSCRubric;
	$arFields = Array(
		"ACTIVE"	=> ($ACTIVE <> "Y"? "N":"Y"),
		"NAME"		=> $NAME,
		"SORT"		=> $SORT,
		"DESCRIPTION"	=> $DESCRIPTION,
		"LID"		=> $LID,
		"AUTO"		=> ($AUTO <> "Y"? "N":"Y"),
		"DAYS_OF_MONTH"	=> $DAYS_OF_MONTH,
		"DAYS_OF_WEEK"	=> (is_array($DAYS_OF_WEEK) ? implode(",", $DAYS_OF_WEEK):""),
		"TIMES_OF_DAY"	=> $TIMES_OF_DAY,
		"TEMPLATE"	=> $TEMPLATE,
		"VISIBLE"	=> ($VISIBLE <> "Y"? "N":"Y"),
		"FROM_FIELD"	=> $FROM_FIELD,
		"LAST_EXECUTED"	=> $LAST_EXECUTED
	);

	if($ID > 0){
		$res = $rubric->Update($ID, $arFields);
	}
	else{
		$ID = $rubric->Add($arFields);
		$res = ($ID>0);
	}

	if($res){
		if($apply != ""){
			LocalRedirect("/bitrix/admin/imaginweb.sms_rubric_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		}
		else{
			LocalRedirect("/bitrix/admin/imaginweb.sms_rubric_admin.php?lang=".LANG);
		}
	}
	else{
		if($e = $APPLICATION->GetException()){
			$message = new CAdminMessage(GetMessage("rub_save_error"), $e);
		}
		$bVarsFromForm = true;
	}

}

//Edit/Add part
ClearVars();
$str_SORT = 100;
$str_ACTIVE = "Y";
$str_AUTO = "N";
$str_DAYS_OF_MONTH = "";
$str_DAYS_OF_WEEK = "";
$str_TIMES_OF_DAY = "";
$str_VISIBLE = "Y";
$str_LAST_EXECUTED = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
$str_FROM_FIELD = COption::GetOptionString("imaginweb.sms", "default_from");

if($ID > 0){
	$rubric = SMSCRubric::GetByID($ID);
	if(!$rubric->ExtractFields("str_")){
		$ID = 0;
	}
}
if($ID > 0 && !$message){
	$DAYS_OF_WEEK = explode(",", $str_DAYS_OF_WEEK);
}
if(!is_array($DAYS_OF_WEEK)){
	$DAYS_OF_WEEK = array();
}
if($bVarsFromForm){
	$DB->InitTableVarsForEdit("iwebsms_list_rubric", "", "str_");
}

$APPLICATION->SetTitle(($ID > 0 ? GetMessage("rub_title_edit").$ID : GetMessage("rub_title_add")));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"=>GetMessage("rub_list"),
		"TITLE"=>GetMessage("rub_list_title"),
		"LINK"=>"imaginweb.sms_rubric_admin.php?lang=".LANG,
		"ICON"=>"btn_list",
	)
);
if($ID > 0){
	$aMenu[] = array("SEPARATOR"=>"Y");
	$aMenu[] = array(
		"TEXT"=>GetMessage("rub_add"),
		"TITLE"=>GetMessage("rubric_mnu_add"),
		"LINK"=>"imaginweb.sms_rubric_edit.php?lang=".LANG,
		"ICON"=>"btn_new",
	);
	$aMenu[] = array(
		"TEXT"=>GetMessage("rub_delete"),
		"TITLE"=>GetMessage("rubric_mnu_del"),
		"LINK"=>"javascript:if(confirm('".GetMessage("rubric_mnu_del_conf")."'))window.location='imaginweb.sms_rubric_admin.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON"=>"btn_delete",
	);
	/* $aMenu[] = array("SEPARATOR"=>"Y");
	$aMenu[] = array(
		"TEXT"=>GetMessage("rub_check"),
		"TITLE"=>GetMessage("rubric_mnu_check"),
		"LINK"=>"imaginweb.sms_template_test.php?lang=".LANG."&ID=".$ID
	); */
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?
if($_REQUEST["mess"] == "ok" && $ID > 0){
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("rub_saved"), "TYPE"=>"OK"));
}
if($message){
	echo $message->Show();
}
elseif($rubric->LAST_ERROR!=""){
	CAdminMessage::ShowMessage($rubric->LAST_ERROR);
}
?>

<form method="POST" Action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?
$tabControl->Begin();
?>
<?
//********************
//Rubric
//********************
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%"><?=GetMessage("rub_act")?></td>
		<td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>></td>
	</tr>
	<tr>
		<td><?=GetMessage("rub_visible")?></td>
		<td><input type="checkbox" name="VISIBLE" value="Y"<?if($str_VISIBLE == "Y") echo " checked"?>></td>
	</tr>
	<tr>
		<td><?=GetMessage("rub_site")?></td>
		<td><?=CLang::SelectBox("LID", $str_LID);?></td>
	</tr>
	<tr>
		<td><span class="required">*</span><?=GetMessage("rub_name")?></td>
		<td><input type="text" name="NAME" value="<?=$str_NAME;?>" size="30" maxlength="100"></td>
	</tr>
	<tr>
		<td><?=GetMessage("rub_sort")?></td>
		<td><input type="text" name="SORT" value="<?=$str_SORT;?>" size="30"></td>
	</tr>
	<tr>
		<td><?=GetMessage("rub_desc")?></td>
		<td><textarea class="typearea" name="DESCRIPTION" cols="45" rows="5" wrap="VIRTUAL"><?=$str_DESCRIPTION; ?></textarea></td>
	</tr>
<?
$tabControl->Buttons(
	array(
		"disabled" => ($POST_RIGHT < "W"),
		"back_url" => "imaginweb.sms_rubric_admin.php?lang=".LANG,

	)
);
?>
<?=bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID > 0 && !$bCopy):?>
	<input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<script language="JavaScript">
<!--
	if(document.post_form.AUTO.checked)
		tabControl.EnableTab('edit2');
	else
		tabControl.DisableTab('edit2');
//-->
</script>

<?=BeginNote();?>
<span class="required">*</span><?=GetMessage("REQUIRED_FIELDS")?>
<?=EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>