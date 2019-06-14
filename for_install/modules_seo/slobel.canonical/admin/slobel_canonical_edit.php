<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

$MODULE_ID='slobel.canonical';

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$MODULE_ID."/classes/main.php");

$POST_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);
if($POST_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("slobel_tab"),"ICON"=>"main_user_edit", "TITLE"=>GetMessage("slobel_tab")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);
$message = null;
$bVarsFromForm = false;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid())
{
	$rules = new SlobelCanonical;
	$arFields = Array(
		"ACTIVE"	=> ($ACTIVE <> "Y"? "N":"Y"),
		"RULE"		=> $RULE,
		"FILE"		=> $FILE,
		"BASE"		=> $BASE
	);

	if($ID > 0)
	{
		$res = $rules->Update($ID, $arFields);
	}
	else
	{
		$ID = $rules->Add($arFields);
		$res = ($ID>0);
	}

	if($res)
	{
		if($apply!="")
			LocalRedirect("/bitrix/admin/slobel_canonical_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect("/bitrix/admin/slobel_canonical.php?lang=".LANG);
	}
	else
	{
		if($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("slobel_save_error"), $e);
		$bVarsFromForm = true;
	}

}

ClearVars();
$str_ACTIVE = "Y";
$str_RULE = "";
$str_BASE = "";
$str_FILE = "";

if($ID>0)
{
	$rules = SlobelCanonical::GetByID($ID);
	if(!$rules->ExtractFields("str_"))
		$ID=0;
}

if($bVarsFromForm)
	$DB->InitTableVarsForEdit("slobel_canonical_list", "", "str_");

$APPLICATION->SetTitle(($ID>0? GetMessage("slobel_title_edit").$ID : GetMessage("slobel_title_add")));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
	array(
		"TEXT"=>GetMessage("slobel_list"),
		"TITLE"=>GetMessage("slobel_list"),
		"LINK"=>"slobel_canonical.php?lang=".LANG,
		"ICON"=>"btn_list",
	)
);
if($ID>0)
{
	$aMenu[] = array("SEPARATOR"=>"Y");
	$aMenu[] = array(
		"TEXT"=>GetMessage("slobel_add"),
		"TITLE"=>GetMessage("slobel_mnu_add"),
		"LINK"=>"rubric_edit.php?lang=".LANG,
		"ICON"=>"btn_new",
	);
	$aMenu[] = array(
		"TEXT"=>GetMessage("slobel_delete"),
		"TITLE"=>GetMessage("slobel_mnu_del"),
		"LINK"=>"javascript:if(confirm('".GetMessage("slobel_mnu_del_conf")."'))window.location='slobel_canonical.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON"=>"btn_delete",
	);
	$aMenu[] = array("SEPARATOR"=>"Y");
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?
if($_REQUEST["mess"] == "ok" && $ID>0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("slobel_saved"), "TYPE"=>"OK"));

if($message)
	echo $message->Show();
?>

<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%"><?echo GetMessage("slobel_act")?></td>
		<td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>></td>
	</tr>
	<tr>
		<td><?echo GetMessage("slobel_rule")?></td>
		<td><input type="text" name="RULE" value="<?echo $str_RULE;?>" size="20"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("slobel_file")?></td>
		<td><input type="text" name="FILE" value="<?echo $str_FILE;?>" size="45"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("slobel_base")?></td>
		<td><input type="text" name="BASE" value="<?echo $str_BASE;?>" size="45"></td>
	</tr>
<?
$tabControl->Buttons(
	array(
		"disabled"=>($POST_RIGHT<"W"),
		"back_url"=>"slobel_canonical.php?lang=".LANG,

	)
);
?>
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
	<input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>