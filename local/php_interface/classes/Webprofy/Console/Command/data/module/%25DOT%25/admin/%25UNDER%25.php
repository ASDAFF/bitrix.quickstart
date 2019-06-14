<?php

$MODULE_ID = "%DOT%";
$MLANG = "%UNDER_CAPS%_";

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/tools.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$MODULE_ID."/classes/general/%UNDER%.php");

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage($MLANG."TITLE"));

$ID = intVal($ID);
$arError = $arFields = array();

$bInitVars = false;
$uselessComplete = false;

if ($REQUEST_METHOD == "POST" && (strlen($save) > 0 || strlen($apply) > 0)){
	if (!check_bitrix_sessid()){
		$arError[] = array(
			"id" => "bad_sessid",
			"text" => GetMessage($MLANG."ERROR_BAD_SESSID"));
	}

	if (empty($arError)){
		$GLOBALS["APPLICATION"]->ResetException();

		$uselessCount = rand(0,123);
		if ($e = $GLOBALS["APPLICATION"]->GetException()){
			$arError[] = array(
				"id" => "",
				"text" => $e->getString()
			);
		}else{
			$uselessComplete = true;
		}
	}
	$e = new CAdminException($arError);
	$message = new CAdminMessage(GetMessage($MLANG."ERROR_SAVE"), $e);
	$bInitVars = true;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if ($uselessComplete){
	CAdminMessage::ShowMessage(array(
		"MESSAGE"=>GetMessage($MLANG."COMPLETE"),
		"DETAILS"=>GetMessage($MLANG."TOTAL", Array('#COUNT#' => $uselessCount)),
		"HTML"=>true,
		"TYPE"=>"OK",
	));
}elseif (isset($message) && $message){
	echo $message->Show();
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage($MLANG."TAB_PARAMS"), "ICON" => "smile", "TITLE" => GetMessage("SMILE_TAB_SMILE_DESCR"))
);

?>

<form method="POST" action="<?=$APPLICATION->GetCurPageParam()?>" name="smile_import" enctype="multipart/form-data">
	<input type="hidden" name="Update" value="Y" />
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value="<?=$ID?>" />
	<?=bitrix_sessid_post()?>
	<?
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();

	$tabControl->BeginNextTab();
	?>
	<tr>
		<td>
			<?=GetMessage($MLANG."FORM_FILE")?>:<br><small><?=GetMessage($MLANG."FORM_FILE_NOTE")?></small></td>
		<td>
			<input type="text" name="IMPORT" size="30" />
		</td>
	</tr>
	<?
	$tabControl->EndTab();

	$tabControl->Buttons(array(
		"btnApply" => false,
	));
	?>
</form>

<?
$tabControl->End();
$tabControl->ShowWarnings("smile_import", $message);
?>

<?=BeginNote();?>
<div><?=GetMessage($MLANG.'HELP_1', Array('#LINK_START#'=>'<a href="/bitrix/admin/fileman_admin.php?lang='.LANG.'&path=%2Fbitrix%2Fmodules%2Fmain%2Finstall%2Fsmiles">', '#LINK_END#'=>'</a>'))?></div>
<div style="padding-top:5px"><?=GetMessage($MLANG.'HELP_2')?></div>
<?=EndNote();?>

<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>
