<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/include.php");
IncludeModuleLangFile(__FILE__);

$module_id = "rarus.sms4b";
$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($SMS_RIGHT < "R") 
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

global $SMS4B; 

$arResult["RESULT_MESSAGE"]["TYPE"] = '';

//read user account
if(	$SMS4B->LastError == '' && $SMS4B->GetSOAP("AccountParams",array("SessionID" => $SMS4B->GetSID())) === true)
{
	$arResult["Rest"] = $SMS4B->arBalance["Rest"];
	$arResult["RESULT_MESSAGE"]["TYPE"] = "OK";
	
	$arResult["Login"] = $USER->GetLogin();
}
else
{
	$arResult["RESULT_MESSAGE"]["TYPE"] = "ERROR";
	$arResult["RESULT_MESSAGE"]["MESSAGE"] = GetMessage("ERROR_CONNECTION");
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name = "form1" method="POST" action="<?=$APPLICATION->GetCurPage()?>">


<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="action" value="<?=$action?>">
<input type="hidden" name="OLD_SID" value="<?=$SID?>">
<?

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage('SMS_LEFT'), "ICON"=>"sms4b_balance", "TITLE"=>GetMessage('SMS_LEFT')),

);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
$tabControl->BeginNextTab();

if ($arResult["RESULT_MESSAGE"]["TYPE"] == 'OK')
{
?>
	<tr>
		<td><?=GetMessage("NUMBER_SENDER")?></td>
		<td>
			<table>
				<tr>
					<td align="right"><?=GetMessage("Login")?></td>
					<td><b><?=$arResult["Login"]?></b></td>
				</tr>
				<tr>
					<td align="right"><?=GetMessage("SMS_CAPT");?></td>
					<td><b><?=round($arResult["Rest"],1)?></b><?=GetMessage("SMS_PS");?></td>
				</tr>
			</table>
		</td>
	</tr>
<?
}
else
{
	echo '<tr><td colspan="2">'.CAdminMessage::ShowMessage($arResult["RESULT_MESSAGE"]["MESSAGE"]).'</td></tr>';
}
	$disable = true;
	if(($isAdmin || $isDemo) && $isEditMode)
			$disable = false;
	$tabControl->Buttons();
?>
<input type = "submit" value="<?=GetMessage("REFRESH")?>" name="apply">
<?
	$tabControl->End();
?>
</form>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
