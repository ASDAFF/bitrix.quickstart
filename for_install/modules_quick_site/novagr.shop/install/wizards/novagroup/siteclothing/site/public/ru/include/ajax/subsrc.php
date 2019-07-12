<?php
/**
 *  скрипт запускается через ajax - в зависимости от переданных параметров происходит 
 *  подписка на новости либо на блог
 * 
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//deb($_REQUEST);

if(!CModule::IncludeModule("subscribe"))
{
	//ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}

/*if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if($arParams["ALLOW_ANONYMOUS"]!="N")
	$arParams["ALLOW_ANONYMOUS"] = COption::GetOptionString("subscribe", "allow_anonymous", "Y");
if($arParams["ALLOW_ANONYMOUS"]!="N")
	$arParams["ALLOW_ANONYMOUS"] = "Y";
if($arParams["SHOW_AUTH_LINKS"]!="N")
	$arParams["SHOW_AUTH_LINKS"] = COption::GetOptionString("subscribe", "show_auth_links", "Y");
if($arParams["SHOW_AUTH_LINKS"]!="N")
	$arParams["SHOW_AUTH_LINKS"] = "Y";
if($arParams["SHOW_HIDDEN"]!="Y")
	$arParams["SHOW_HIDDEN"] = "N";
if($arParams["SET_TITLE"]!="N")
	$arParams["SET_TITLE"] = "Y";
$_REQUEST["CONFIRM_CODE"] = trim($_REQUEST["CONFIRM_CODE"]);
*/
//options
//$bAllowRegister = (COption::GetOptionString("main", "new_user_registration") == "Y");
/*$sLastLogin = ${
	COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};

	$ID = intval($_REQUEST["ID"]); // Id of the subscription
	//onscreen messages about actions
	$aMsg = array(
			"UPD"=>GetMessage("adm_upd_mess"),
			"SENT"=>GetMessage("adm_sent_mess"),
			"SENTPASS"=>GetMessage("subscr_pass_mess"),
			"CONF"=>GetMessage("adm_conf_mess"),
			"UNSUBSCR"=>GetMessage("adm_unsubscr_mess"),
			"ACTIVE"=>GetMessage("subscr_active_mess")
	);
	if(array_key_exists($_REQUEST["mess_code"], $aMsg))
		$iMsg = $_REQUEST["mess_code"];
	else
		$iMsg = "";
*/
	$obSubscription = new CSubscription;

	//*************************
	//settings form processing
	//*************************
	$arWarning = array();
	$bVarsFromForm = false; // 
	if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["PostAction"]) && check_bitrix_sessid() )
	{
		$result = array();
		
		
		$bDoSubscribe = true;
		$bVarsFromForm = true;

		/*if(!empty($_REQUEST["LOGIN"]))
		{
			//authorize the user
			$res = $USER->Login($_REQUEST["LOGIN"], $_REQUEST["PASSWORD"]);
			if($res["TYPE"] == "ERROR")
				$arWarning[] = $res["MESSAGE"];
			else
				$bDoSubscribe = false;
		}
		elseif(!empty($_REQUEST["NEW_LOGIN"]))
		{
			//new user
			$res = $USER->Register($_REQUEST["NEW_LOGIN"], "", "", $_REQUEST["NEW_PASSWORD"], $_REQUEST["CONFIRM_PASSWORD"], $_REQUEST["EMAIL"], false, $_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]);
			if($res["TYPE"] == "ERROR")
				$arWarning[] = $res["MESSAGE"];
			else
				$bDoSubscribe = false;
		}
*/
		//if anonymous users are not permitted then the user must be authorized
	/*	if($arParams["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized())
			$arWarning[] = GetMessage("adm_auth_err");

		//there must be at least one newsletter category
		if(!is_array($_REQUEST["RUB_ID"]) || count($_REQUEST["RUB_ID"]) == 0)
			$arWarning[] = GetMessage("adm_auth_err_rub");
		elseif($arParams["SHOW_HIDDEN"]=="N") //check for hidden categories
		{
			$bAllowSubscription=true;
			foreach($_REQUEST["RUB_ID"] as $rub_id)
			{
				$rsRubric = CRubric::GetByID($rub_id);
				if($arRubric = $rsRubric->Fetch())
					if($arRubric["VISIBLE"]=="N")
					$bAllowSubscription=false;
			}
			if($bAllowSubscription===false)
				$arWarning[] = GetMessage("subscr_wrong_rubric");
		}
*/
		if(count($arWarning)<=0 && $bDoSubscribe)
		{
			//Check if subscription already have hidden rubrics and they was not displayed.
			//In this case we will add those categories to the list in order not to lost.
			/*if(($arParams["SHOW_HIDDEN"] == "N") && ($ID > 0))
			{
				$arNewRubrics = $_REQUEST["RUB_ID"];
				$rsRubric = CSubscription::GetRubricList($ID);
				while($ar = $rsRubric->Fetch())
				{
					if($ar["VISIBLE"] == "N")
						$arNewRubrics[] = $ar["ID"];
				}
			}
			else
			{*/
				$arNewRubrics = $_REQUEST["RUB_ID"];
			//}

			$arFields = Array(
					"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false),
					"FORMAT" => ($_REQUEST["FORMAT"] <> "html"? "text":"html"),
					"EMAIL" => $_REQUEST["sf_EMAIL"],
					"RUB_ID" => $arNewRubrics,
			);
//deb($arFields);
	//	if($_REQUEST["CONFIRM_CODE"] <> "" && $ID > 0)
	//			$arFields["CONFIRM_CODE"] = $_REQUEST["CONFIRM_CODE"];
//deb($ID);
			$res = false;
			if($ID>0)
			{
				//allow edit only after authorization
				if(CSubscription::IsAuthorized($ID))
				{
					$res = $obSubscription->Update($ID, $arFields);
					if($res)
						$iMsg = ($obSubscription->LAST_MESSAGE<>""? $obSubscription->LAST_MESSAGE:"UPD");
				}
			}
			else
			{
				//can add without authorization
				$arFields["ACTIVE"] = "Y";
				$ID = $obSubscription->Add($arFields);
				$res = ($ID>0);
				//deb($ID);
				if($res)
				{
					$iMsg = "SENT";
					CSubscription::Authorize($ID);
				}
			}

			if($res)
			{
				//remember e-mail in cookies
				$bVarsFromForm = false;
				$APPLICATION->set_cookie("SUBSCR_EMAIL", $_REQUEST["EMAIL"], mktime(0,0,0,12,31,2030));

				$result['redirect_uri'] = '#SITE_DIR#cabinet/subscr/' . "?ID=".$ID.($iMsg <> ""? "&mess_code=".urlencode($iMsg):"");
				$result['message'] = "OK";
				$result['result'] = "OK";

			}
			else {
				
				$result['message'] = str_replace("<br>", "", $obSubscription->LAST_ERROR);
				$result['redirect_uri'] = '#SITE_DIR#cabinet/subscr/' . "?error=" . $result['message'];
				$result['result'] = "ERROR";
				
					
			}
			$resultJson = json_encode($result);
			die($resultJson);
		}
	}

die('fin');			
?>