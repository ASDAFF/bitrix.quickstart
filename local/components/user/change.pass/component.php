<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $USER;
	if(!$USER->IsAuthorized())
		die();

	if($_REQUEST['do']=='send'){
		if($_REQUEST['password']!=$_REQUEST['confirm_password'])
			$arResult['ERROR'] .= GetMessage('NOT_THE_SAME')."<br />";
		
		if($_REQUEST['password']=='')
			$arResult['ERROR'] = GetMessage('PASSWORD_EMPTY')."<br />";
		
		if($_REQUEST['confirm_password']=='')
			$arResult['ERROR'] = GetMessage('CONFIRM_PASSWORD_EMPTY')."<br />";

		
		
		$arAuthResult = $USER->Login($USER->GetLogin(), $_REQUEST['old_password'], "Y");
		if($arAuthResult['TYPE']=='ERROR')
			$arResult['ERROR'] .= GetMessage('PASSWORD_WRONG')."<br />";

		if($arResult['ERROR']==''){
			$ID = intval($USER->GetID());
			$salt = randString(8);
			$checkword = md5(CMain::GetServerUniqID().uniqid());
			$_checkword = $salt.md5($salt.$checkword);
			$strSql = "UPDATE b_user SET ".
				"	CHECKWORD = '".$_checkword."', ".
				"	CHECKWORD_TIME = ".$DB->CurrentTimeFunction().", ".
				"	LID = '".$DB->ForSql($SITE_ID, 2)."', ".
				"   TIMESTAMP_X = TIMESTAMP_X ".
				"WHERE ID = '".$ID."'".
				"	AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";

			$DB->Query($strSql);
			if($arResult['ERROR']==''){
				$res = $USER->ChangePassword($USER->GetLogin(), $checkword, $_REQUEST['password'], $_REQUEST['confirm_password']);

			
			if($res["TYPE"] == "OK")
				$arResult['SUCCESS'] = 'Y';
			else 
				$arResult['ERROR'] = $res['MESSAGE'];

			}
		}
	}
	$this->IncludeComponentTemplate();
	?>