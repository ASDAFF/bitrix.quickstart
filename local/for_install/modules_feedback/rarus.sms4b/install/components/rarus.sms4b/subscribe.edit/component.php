<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//kill login from cookies
$_COOKIE["BITRIX_SM_LOGIN"] = '';

if(!CModule::IncludeModule("subscribe"))
{
	ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("rarus.sms4b"))
{
	ShowError(GetMessage("sms4b_MODULE_NOT_INSTALLED"));
	return;
}

$arWarning = array();
$arResult = array();  
$ID = 0;

//params for showing rubrics
if (empty($arParams["SHOW_RUBS"]))
	$arResult["SHOW_ALL"] = "Y";
else
	$arResult["SHOWED_RUBS"] = $arParams["SHOW_RUBS"];

//connect class for hint
require_once("classes.php");

$_REQUEST["CONFIRM_CODE"] = trim($_REQUEST["CONFIRM_CODE"]);

//options
$bAllowRegister = (COption::GetOptionString("main", "new_user_registration") == "Y");
$sLastLogin = ${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"};


//new object from extended class
$obSubscription = new CSubscriptionMod;
$obSubscription->template_id = $arParams["TEMPLATE_ID"];

if($arParams["SHOW_POST_FORM"] == "Y")
	$arResult["SHOW_POST_FORM"] = true;
else
	$arResult["SHOW_POST_FORM"] = false;
	
if($arParams["SHOW_SMS_FORM"] == "Y")
	$arResult["SHOW_SMS_FORM"] = true;
else
	$arResult["SHOW_SMS_FORM"] = false;	

//for priority
if($arResult["SHOW_SMS_FORM"] == $arResult["SHOW_POST_FORM"])
	$arResult["SHOW_SMS_FORM"] = true;

if(preg_match("/^[ \+\-\(\)0-9]+?$/i",$_REQUEST["sf_EMAIL"]))
{
	global $SMS4B;
	$_REQUEST["sf_EMAIL"] = $SMS4B->is_phone($_REQUEST["sf_EMAIL"]);

	if(strlen($_REQUEST["sf_EMAIL"]) > 11)
		$_REQUEST["sf_EMAIL"] = substr($_REQUEST["sf_EMAIL"],0,11);

	if(strlen($_REQUEST["sf_EMAIL"]) == 11 && $_REQUEST["sf_EMAIL"][0] == '8')
		$_REQUEST["sf_EMAIL"] = '7'.substr($_REQUEST["sf_EMAIL"],1,10);
		
	if(strlen($_REQUEST["sf_EMAIL"]) == 10)
		$_REQUEST["sf_EMAIL"] = '7'.$_REQUEST["sf_EMAIL"];
		
	if(strlen($_REQUEST["sf_EMAIL"]) == 10)
		$_REQUEST["sf_EMAIL"] = '7'.$_REQUEST["sf_EMAIL"];

	if(strlen($_REQUEST["sf_EMAIL"]) < 11)
		$_REQUEST["sf_EMAIL"] = '';
	
}
	
//getting data for registered user
$to = '';
if($USER->IsAuthorized())
{
	$rsUser = $USER->GetByID($USER->GetID());
	$arUser = $rsUser->Fetch();
	
	$to = findsub($USER->GetID(),&$arResult);
	if($to == '')
	{
		if($arResult["SHOW_SMS_FORM"] && $arUser["PERSONAL_MOBILE"])
		{
			$to = $_REQUEST["sf_EMAIL"] <> '' ? '' : add_postfix($arUser["PERSONAL_MOBILE"]);
		}
		elseif($arResult["SHOW_POST_FORM"])
			$to = $USER->GetEmail();

		if ($to == '' && preg_match("/^[ \+\-\(\)0-9]+$/i",$_REQUEST["sf_EMAIL"]) && $arResult["SHOW_SMS_FORM"] && $arUser["PERSONAL_MOBILE"] == '')
		{
			$user = new CUser;
			$fields = Array(
				"PERSONAL_MOBILE" => $_REQUEST["sf_EMAIL"],
			);
			if ($user->Update($arUser["ID"], $fields))
				$to =  add_postfix($_REQUEST["sf_EMAIL"]);
			else
				$arResult["ERROR"][] = GetMessage("ERROR_ADD_PHONE_TO_PROF");
		}
	}

}

//for anonymous user
$is_sms = preg_match("/^[ \+\-\(\)0-9]+$/",$_REQUEST["sf_EMAIL"]);
if($to == '' && $_REQUEST["sf_EMAIL"] <> '' && (($is_sms && $arResult["SHOW_SMS_FORM"]) || ($arResult["SHOW_POST_FORM"] && !$is_sms)))
{
	if ($is_sms && $arResult["SHOW_SMS_FORM"])
	{
		$to = add_postfix($_REQUEST["sf_EMAIL"]);
	}
	elseif(check_email($_REQUEST["sf_EMAIL"]) && $arResult["SHOW_POST_FORM"])
	{
		$to = $_REQUEST["sf_EMAIL"]; 
	}
}


//getting address from cookies if it is exist
if($APPLICATION->get_cookie("M_SUBSCR_EMAIL") <> '' && $arResult["SHOW_POST_FORM"] && $to == '')
{
	$to = $APPLICATION->get_cookie("M_SUBSCR_EMAIL");
}

if ($APPLICATION->get_cookie("M_SUBSCR_SMS") <> '' && $arResult["SHOW_SMS_FORM"] && $to == '')
{	
	$to = add_postfix($APPLICATION->get_cookie("M_SUBSCR_SMS"));
}

//pass
if($_REQUEST["CONFIRM_CODE"] == '' && $_SESSION["AUTH_PASS_EMAIL"] && $arResult["SHOW_POST_FORM"] && $_REQUEST["AUTH_PASS"] == '')
	$_REQUEST["AUTH_PASS"] = $_SESSION["AUTH_PASS_EMAIL"];
elseif($_REQUEST["CONFIRM_CODE"] == '' && $_SESSION["AUTH_PASS_SMS"] && $arResult["SHOW_SMS_FORM"] && $_REQUEST["AUTH_PASS"] == '')
	$_REQUEST["AUTH_PASS"] = $_SESSION["AUTH_PASS_SMS"];

//find subscribe for email
if($to <> '')
{
	$rsSubscr = $obSubscription->GetByEmail($to);

	$arSubscr = $rsSubscr->Fetch();

	if($arSubscr && $arSubscr["USER_ID"] <> $USER->GetID())
	{
		$arWarning[] = GetMessage("BUSY_TO");
		if($USER->IsAuthorized())
			$arWarning[] = GetMessage("REGU_BUSY_TO");	
		$to = '';
		unset($arSubscr);
	}
}

//if doesn't exist trying from request
$_REQUEST["ID"] = intval($_REQUEST["ID"]);

if(!$arSubscr && $_REQUEST["ID"] > 0 && (($arResult["SHOW_SMS_FORM"]) || ($arResult["SHOW_POST_FORM"])))
{
	$rsSubscr = $obSubscription->GetByID($_REQUEST["ID"]);
	$arSubscr = $rsSubscr->Fetch();

	if($arSubscr && $arSubscr["USER_ID"] <> $USER->GetID())
	{
		$arWarning[] = GetMessage("BUSY_TO");
		if($USER->IsAuthorized())
			$arWarning[] = GetMessage("REGU_BUSY_TO");	
	}
	elseif($arSubscr)
	{
		$is_sms = preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$arSubscr["EMAIL"]);
		if	(	
				$to == '' &&
				(($is_sms && $arResult["SHOW_SMS_FORM"]) || ($arResult["SHOW_POST_FORM"] && !$is_sms))
			)
			$to = $arSubscr["EMAIL"];
	}
		

	
}

//if subscribtion exist
if($arSubscr)
{
	$ID = $arSubscr["ID"];
	$to = $arSubscr["EMAIL"];
	if ($_REQUEST["CONFIRM_CODE"] == '')
	if($USER->IsAuthorized())
		$_REQUEST["CONFIRM_CODE"] = $arSubscr["CONFIRM_CODE"];
}

//checking if exist user with such TN/email
if ($arResult["SHOW_POST_FORM"])
{
	$cdb_sub_check = CUser::GetList(($by="ID"),($order="desc"),array("EMAIL"=>$to));
	$user_inf = $cdb_sub_check->Fetch();
}
elseif($arResult["SHOW_SMS_FORM"])
{
	$cdb_sub_check = CUser::GetList(($by="ID"),($order="desc"),array("PERSONAL_MOBILE"=>kill_post_fix($to)));
	$user_inf = $cdb_sub_check->Fetch();
}


if($_REQUEST["sf_EMAIL"] <> '')
{
	if($arResult["SHOW_SMS_FORM"])
		$REQ_sf_EMAIL =  add_postfix($_REQUEST["sf_EMAIL"]);
	else
		$REQ_sf_EMAIL =  $_REQUEST["sf_EMAIL"];   

	$REQ_subscription = $obSubscription->GetByEmail($REQ_sf_EMAIL);

	if($arREQ = $REQ_subscription->Fetch()) 
	{
		if (!$USER->IsAuthorized() && $arREQ["USER_ID"] > 0)
		{
			$name = $user_inf['NAME'];
			$last_name = $user_inf['LAST_NAME'];
			$email = $user_inf['EMAIL'];
			$reg_date = $user_inf['DATE_REGISTER'];
			$tf = $user_inf['PERSONAL_MOBILE'];
			$to_add = ($arResult["SHOW_SMS_FORM"]) ? $email : $tf;
			$APPLICATION->AuthForm(implode('<br>',$arWarning), false);	
		}
	}
}
 
//trying to register subscriber 
if($ID > 0 && $_REQUEST["CONFIRM_CODE"] <> '' && !$obSubscription->IsAuthorized($ID))
{
	$obSubscription->Authorize($ID,$_REQUEST["CONFIRM_CODE"]);
	
}

$ACTIVE_FORM = false;
$is_sms = preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$to);
if (
	(($is_sms && $arResult["SHOW_SMS_FORM"]) || (!$is_sms && $arResult["SHOW_POST_FORM"])) &&
	($_REQUEST["ID"] == $ID || ($_REQUEST["ID"] == 0 && $ID == 0)) &&
	$to <> '' 
	)
	$ACTIVE_FORM = true;

if($ACTIVE_FORM)
{
	//onscreen messages about actions
	$aMsg = array(
		"UPD"=>GetMessage("adm_upd_mess"),
		"SENT"=>GetMessage("adm_sent_mess"),
		"SENTPASS"=>GetMessage("subscr_pass_mess"),
		"CONF"=>GetMessage("adm_conf_mess"),
		"UNSUBSCR"=>GetMessage("adm_unsubscr_mess"),
		"ACTIVE"=>GetMessage("subscr_active_mess"),
		"SUB_REGISTERED"=>GetMessage("subscr_registered")
	);
	if(array_key_exists($_REQUEST["mess_code"], $aMsg))
		$iMsg = $_REQUEST["mess_code"];
	else
		$iMsg = "";


	//*************************
	//settings form processing
	//*************************

	$bVarsFromForm = false;


	//processing post data
	if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["PostAction"]) && check_bitrix_sessid())
	{ 	

		$bDoSubscribe = true;
		$bVarsFromForm = true;

		if(!empty($_REQUEST["LOGIN"]))
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

		//if anonymous users are not permitted then the user must be authorized
		if($arParams["ALLOW_ANONYMOUS"]=="N" && !$USER->IsAuthorized())
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


		if(count($arWarning)<=0 && $bDoSubscribe)
		{
			//Check if subscription already have hidden rubrics and they was not displayed.
			//In this case we will add those categories to the list in order not to lost.
			if(($arParams["SHOW_HIDDEN"] == "N") && ($ID > 0))
			{
				$arNewRubrics = $_REQUEST["RUB_ID"];
				$rsRubric = $obSubscription->GetRubricList($ID);
				while($ar = $rsRubric->Fetch())
				{
					if($ar["VISIBLE"] == "N")
						$arNewRubrics[] = $ar["ID"];
				}
			}
			else
			{
				$arNewRubrics = $_REQUEST["RUB_ID"];
			}

			
			//search user on email
			$cdb_sub_check = CUser::GetList($by="ID",$order="desc",array("EMAIL"=>$_REQUEST["sf_EMAIL"]));

			if ($user_info = $cdb_sub_check->Fetch())
				$us_id = $user_info['ID'];
			else
				$us_id = false;

			if($arResult["SHOW_SMS_FORM"])
				$REQ_sf_EMAIL =  add_postfix($_REQUEST["sf_EMAIL"]);
			else
				$REQ_sf_EMAIL =  $_REQUEST["sf_EMAIL"];   

			$REQ_subscription = $obSubscription->GetByEmail($REQ_sf_EMAIL);

			if($arREQ = $REQ_subscription->Fetch()) 
			{
				if ($USER->GetID() <> $arREQ["USER_ID"])
					$arWarning[] = GetMessage("BUSY_TO");
			}
			else
			{
				if(!$USER->IsAuthorized() && $to <> $REQ_sf_EMAIL)
				{
					$confirms = "N";
				}
				$to =  $REQ_sf_EMAIL;
			}

			//for sms could be only in text format
			if ($arResult["SHOW_SMS_FORM"])
				$format = "text";
			else
				$_REQUEST["FORMAT"] <> "text" ? $format = "html" : $format ="text";	
	
			$confirm_sub_cdb = $obSubscription->GetByID($ID);
			$confirm_sub = $confirm_sub_cdb->Fetch();
			($obSubscription->IsAuthorized($ID) && $confirm_sub["CONFIRMED"] == "Y" && $confirms <> "N") ? $confirmed = true  : $confirmed = false;

			$arFields = Array(
				"USER_ID" => ($USER->IsAuthorized() ? $USER->GetID() : $us_id),
				"FORMAT" => $format,
				"EMAIL" => $to,
				"RUB_ID" => $arNewRubrics,
				"CONFIRMED" => ($USER->IsAuthorized() || $confirmed) ? "Y" : "N",
			);

			$res = false;

			if($ID > 0)
			{ 
				//allow edit only after authorization
				if($obSubscription->IsAuthorized($ID))
				{
					$res = $obSubscription->Update($ID, $arFields);
				
					if($res)
					{
						$rsSubscr = $obSubscription->GetByID($ID);
						$arSubscr = $rsSubscr->Fetch();
						
						$iMsg = ($obSubscription->LAST_MESSAGE<>""? $obSubscription->LAST_MESSAGE:"UPD");
					}
				}
			 
			}
			else
			{
				//can add without authorization
				$arFields["ACTIVE"] = "Y";
				$ID = $obSubscription->Add($arFields);
			
			
			
				$res = ($ID > 0);
				if($res)
				{
					$rsSubscr = $obSubscription->GetByID($ID);
					$arSubscr = $rsSubscr->Fetch();		

					if (!$USER->IsAuthorized())
					{
						$iMsg = "SENT";
					}
					else
					{
						$iMsg = "SUB_REGISTERED";
					}
					$obSubscription->Authorize($ID);
				}
			}

			if($res)
			{
				$bVarsFromForm = false;
			}
			else
				$arWarning[] = $obSubscription->LAST_ERROR;
		}//$arWarning
	}//POST
}

//try to authorize subscription by CONFIRM_CODE or user password AUTH_PASS
if($ID > 0 && !$obSubscription->IsAuthorized($ID))
{
	if($arSubscr["USER_ID"] > 0 && !empty($_REQUEST["AUTH_PASS"]))
	{
		//trying to login user
		$rsUser = CUser::GetByID($arSubscr["USER_ID"]);
		if(($arUser = $rsUser->Fetch()))
		{
			$res = $USER->Login($arUser["LOGIN"], $_REQUEST["AUTH_PASS"]);
			if($res["TYPE"] == "ERROR")
				$arWarning[] = $res["MESSAGE"];
		}
	}
	if(!$obSubscription->Authorize($ID, (empty($_REQUEST["AUTH_PASS"]) ? $_REQUEST["CONFIRM_CODE"] : $_REQUEST["AUTH_PASS"])) && !empty($_REQUEST["AUTH_PASS"]))
	{
		$arWarning[] = GetMessage("error_in_pass");
	}
	else
	{
		$obSubscription->Update($ID, array("CONFIRM_CODE" => $_REQUEST["AUTH_PASS"]));	
	}
}

if($obSubscription->IsAuthorized($ID))
{
	if($arResult["SHOW_SMS_FORM"] && preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$to) && $_REQUEST["auth_type"] == "sms")
	{		
		$APPLICATION->set_cookie("M_SUBSCR_SMS", $to, 0);
		$_SESSION["AUTH_PASS_SMS"] = $_REQUEST["CONFIRM_CODE"] ? $_REQUEST["CONFIRM_CODE"] : $_REQUEST["AUTH_PASS"];
	}

	if($arResult["SHOW_POST_FORM"] && $_REQUEST["auth_type"] == "email")
	{
		$APPLICATION->set_cookie("M_SUBSCR_EMAIL", $to, 0);
		$_SESSION["AUTH_PASS_EMAIL"] = $_REQUEST["CONFIRM_CODE"] ? $_REQUEST["CONFIRM_CODE"] : $_REQUEST["AUTH_PASS"];
	}
}

//confirmation code from letter or confirmation form
if($_REQUEST["CONFIRM_CODE"] <> "" && $ID > 0 && empty($_REQUEST["action"]))
{
	if($str_CONFIRMED <> "Y")
	{
		//subscribtion confirmation
		if($obSubscription->Update($ID, array("CONFIRM_CODE"=>$_REQUEST["CONFIRM_CODE"])))
			$arSubscr["CONFIRMED"] = "Y";
		if($obSubscription->LAST_ERROR<>"")
			$arWarning[] = $obSubscription->LAST_ERROR;
		$iMsg = $obSubscription->LAST_MESSAGE;
	}
}

//processing actions
if($ACTIVE_FORM)
{
if($ID > 0 && (($_REQUEST["action"] == "unsubscribe") || ($_REQUEST["action"] == "activate") || check_bitrix_sessid()))
{
	//confirmation code request
	switch($_REQUEST["action"])
	{
	case "sendcode":
		if (!$USER->IsAuthorized())
		{
			if($obSubscription->ConfirmEvent($ID))
				$iMsg = "SENT";
		}
		break;
	case "sendpassword":
		if(intval($arSubscr["USER_ID"]) == 0)
		{
			//anonymous subscription
			if($obSubscription->ConfirmEvent($ID))
				$iMsg = "SENT";
		}
		else
		{
			//user account subscription
			CUser::SendUserInfo($arSubscr["USER_ID"], LANG, GetMessage("subscr_send_pass_mess"));
			$iMsg = "SENTPASS";
			LocalRedirect($APPLICATION->GetCurPage()."?sf_EMAIL=".urlencode($_REQUEST["sf_EMAIL"])."&change_password=yes&mess_code=".urlencode($iMsg));
		}
		break;
	case "unsubscribe":
		if($obSubscription->IsAuthorized($ID))
		{
			//unsubscription
			if($obSubscription->Update($ID, array("ACTIVE"=>"N")))
			{
				$arSubscr["ACTIVE"] = "N";
				$iMsg = "UNSUBSCR";
				$arResult["UNSUBSCRIBE_FORM"] = 'show';
			}
		}
		break;
	case "activate":
		if($obSubscription->IsAuthorized($ID))
		{
			//activation
			if($obSubscription->Update($ID, array("ACTIVE"=>"Y")))
			{
				$arSubscr["ACTIVE"] = "Y";
				$iMsg = "ACTIVE";
			}
		}
		break;
	}
}
}

//if subscribe exist
if($arSubscr)
{
	$subscr_rub = $obSubscription->GetRubricList($ID);
	while($subscr_rub_arr = $subscr_rub->Fetch())
		$arInput[] = $subscr_rub_arr["ID"];
}

//getting list
$obCache = new CPHPCache;
$strCacheID = $componentPath.LANG.$arParams["SHOW_HIDDEN"];
if($obCache->StartDataCache($arParams["CACHE_TIME"], $strCacheID, $componentPath))
{
	$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
	if($arParams["SHOW_HIDDEN"]<>"Y")
		$arFilter["VISIBLE"]="Y";
	$rsRubric = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
	$arRubrics = array();
	while($arRubric = $rsRubric->GetNext())
	{
		$arRubrics[]=$arRubric;
	}
	$obCache->EndDataCache($arRubrics);
}
else
{
	$arRubrics = $obCache->GetVars();
}


//forming list of rubrics
foreach($arRubrics as $arRubric)
{
	$bChecked = in_array($arRubric["ID"], $arInput);

	if(strpos($arRubric["NAME"],'SMS') === false && strpos($arRubric["DESCRIPTION"],'SMS') === false)
		$ARN = 'EMAIL';
	else
		$ARN = 'SMS';

	if(($arResult["SHOW_POST_FORM"] && $ARN == 'EMAIL') || ($arResult["SHOW_SMS_FORM"] && $ARN == 'SMS'))

	$arResult["RUBRICS"][]=array(
		"ID"=>$arRubric["ID"],
		"NAME"=>$arRubric["NAME"],
		"DESCRIPTION"=>$arRubric["DESCRIPTION"],
		"CHECKED"=>$bChecked,
		"RUB_COUNT"=>CRubric::GetSubscriptionCount($arRubric["ID"]),
	);

}


$is_sms = preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$to);
if($to <> '' && $is_sms && $arResult["SHOW_SMS_FORM"] && !$arSubscr)
$arResult["SUBSCRIPTION"]["EMAIL"] = $to;

if($to <> '' && !$is_sms && $arResult["SHOW_POST_FORM"] && !$arSubscr)
$arResult["SUBSCRIPTION"]["EMAIL"] = $to;

//for registered, but if they have no subscribe
$is_sms = preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$arSubscr["EMAIL"]);
$arResult["ERROR"] = $arWarning;
if($arSubscr && $to <> '' && (($is_sms && $arResult["SHOW_SMS_FORM"]) || (!$is_sms && $arResult["SHOW_POST_FORM"])))
{
	$arResult["SUBSCRIPTION"]["EMAIL"] = $to;
	$arResult["SUBSCRIPTION"]["ID"] = $ID;
	$arResult["SUBSCRIPTION"]["CONFIRMED"] = $arSubscr["CONFIRMED"];
	$arResult["SUBSCRIPTION"]["ACTIVE"] = $arSubscr["ACTIVE"];
	
	$arResult["ID"] = $ID;
	$arResult["SUBSCRIPTION"] = $arSubscr;	
	$arResult["ALLOW_ANONYMOUS"] = $arParams["ALLOW_ANONYMOUS"];
	$arResult["SHOW_AUTH_LINKS"] = $arParams["SHOW_AUTH_LINKS"];
	$arResult["FORM_ACTION"] = $APPLICATION->GetCurPage();
	$arResult["ALLOW_REGISTER"] = $bAllowRegister ? "Y" : "N";


	
	$arResult["ACTIVE_FORM"] = $ACTIVE_FORM;
	$sRub = "";
	if(is_array($_REQUEST["RUB_ID"]))
		foreach($_REQUEST["RUB_ID"] as $strRub)
			$sRub .= "&RUB_ID[]=".urlencode($strRub);

	$arResult["REQUEST"]["EMAIL"] = $to;
	$arResult["REQUEST"]["RUBRICS_PARAM"] = htmlspecialchars($sRub);
	$arResult["REQUEST"]["CONFIRM_CODE"] = htmlspecialchars($_REQUEST["CONFIRM_CODE"]);
	$arResult["REQUEST"]["PASSWORD"] = htmlspecialchars($_REQUEST["PASSWORD"]);
	$arResult["REQUEST"]["LOGIN"] = htmlspecialchars((isset($_REQUEST["LOGIN"])? $_REQUEST["LOGIN"]:$sLastLogin));
	$arResult["REQUEST"]["NEW_LOGIN"] = htmlspecialchars($_REQUEST["NEW_LOGIN"]);
	$arResult["REQUEST"]["NEW_PASSWORD"] = htmlspecialchars($_REQUEST["NEW_PASSWORD"]);
	$arResult["REQUEST"]["CONFIRM_PASSWORD"] = htmlspecialchars($_REQUEST["CONFIRM_PASSWORD"]);
}

$this->IncludeComponentTemplate();
?>