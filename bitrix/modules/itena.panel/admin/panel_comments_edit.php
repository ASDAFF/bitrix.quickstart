<?
#################################################
#	Company developer: ITENA
#	Site: http://itena.ru
#	E-mail: info@itena.ru
#	Copyright (c) 2012 ITENA
#################################################
?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/prolog.php");
IncludeModuleLangFile(__FILE__);

$PANEL_RIGHT = $APPLICATION->GetGroupRight("itena.panel");
if($PANEL_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!CModule::IncludeModule('forum') || !CModule::IncludeModule('iblock')) 
{
  echo GetMessage("PANEL_NOMODULE");
  return; 
}
$sError = false;

if ($REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
  $erMsg = array(); $arFields = array();
  $APPLICATION->ResetException();
  
  $arFields = array("POST_DATE" => $_REQUEST["POST_DATE"], "AUTHOR_NAME" => $_REQUEST["AUTHOR_NAME"], "AUTHOR_EMAIL" => $_REQUEST["AUTHOR_EMAIL"], "POST_MESSAGE" => $_REQUEST["POST_MESSAGE"]);
  
  if ($_REQUEST["ID"] > 0)
  {
    if (!CForumMessage::Update($_REQUEST["ID"], $arFields))
      $err = $APPLICATION->GetException();
  }

  if (!$err && !empty($_REQUEST['save']))
    LocalRedirect("panel_comments.php?lang=".LANG);
  elseif ($err)
  {
    $sError = $err->GetString();
  }
}
$arFields = array();
if ($_REQUEST["ID"] > 0)
{
  $arFields = CForumMessage::GetByID($_REQUEST["ID"]);
}
$APPLICATION->SetTitle(GetMessage("PANEL_EDIT_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
  array(
    "TEXT" => GetMessage("PANEL_EDIT_LIST"),
    "LINK" => "/bitrix/admin/panel_comments.php?lang=".LANG,
    "ICON" => "btn_list"));
$context = new CAdminContextMenu($aMenu);
$context->Show();
if ($sError)
  CAdminMessage::ShowMessage($sError);

?>
<form method="POST" action="<?=$APPLICATION->GetCurPage()?>?" name="panel_comments_edit">
	<input type="hidden" name="lang" value="<?=LANG ?>" />
	<input type="hidden" name="ID" value="<?=$arFields["ID"]?>" />
	<?=bitrix_sessid_post()?><?
	$aTabs = array(array("DIV" => "edit", "TAB" => GetMessage("PANEL_EDIT_COMMENT"), "ICON" => "panel_comments", "TITLE" => ""));
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();
	$tabControl->BeginNextTab();
?>	
  <tr>
    <td width="40%"><span class="required">*</span>&nbsp; <?=GetMessage("PANEL_EDIT_POST_DATE_LABEL")?>:</td>
    <td width="60%"><?echo CalendarDate("POST_DATE", $arFields["POST_DATE"], "panel_comments_edit", "20")?></td>
  </tr>
  <tr>
    <td width="40%"><?=GetMessage("PANEL_EDIT_AUTHOR_NAME_LABEL")?>:</td>
    <td width="60%"><input type="text" name="AUTHOR_NAME" size="40" value="<?=$arFields["AUTHOR_NAME"]?>" /></td>
  </tr>
  <tr>
    <td width="40%"><?=GetMessage("PANEL_EDIT_AUTHOR_EMAIL_LABEL")?>:</td>
    <td width="60%"><input type="text" name="AUTHOR_EMAIL" size="40" value="<?=$arFields["AUTHOR_EMAIL"]?>" /></td>
  </tr>
  <tr>
    <td width="40%"><span class="required">*</span>&nbsp; <?=GetMessage("PANEL_EDIT_TEXT_LABEL")?>:</td>
    <td width="60%"><textarea name="POST_MESSAGE" rows="10" cols="45"><?=htmlspecialchars($arFields["POST_MESSAGE"])?></textarea></td>
  </tr>
<?$tabControl->EndTab();?>
<?$tabControl->Buttons(
		array(
				"disabled" => false,
				"back_url" => "/bitrix/admin/panel_comments.php?lang=".LANG
			)
	);?>
<?$tabControl->End();?>
</form>
<?=BeginNote();?>
<span class="required">*</span><font class="legendtext"> - <?=GetMessage("REQUIRED_FIELDS")?>
<?=EndNote();?>		
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>