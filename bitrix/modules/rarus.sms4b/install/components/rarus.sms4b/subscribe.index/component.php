<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();  
require_once "postfix_functions.php";

if(!CModule::IncludeModule("subscribe"))
{
	ShowError(GetMessage("SUBSCR_MODULE_NOT_INSTALLED"));
	return;
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if(!isset($arParams["PAGE"]) || strlen($arParams["PAGE"])<=0)
	$arParams["PAGE"] = COption::GetOptionString("subscribe", "subscribe_section")."subscr_edit.php";

$arParams["SHOW_HIDDEN"] = $arParams["SHOW_HIDDEN"]=="Y";
$arParams["SHOW_COUNT"] = $arParams["SHOW_COUNT"]=="Y";
$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";

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

//get parameters for showing rubrics
if (empty($arParams["SHOW_RUBS"]))
{
	$arResult["SHOW_ALL"] = "Y";
}
else
{
	$arResult["SHOWED_RUBS"] = $arParams["SHOW_RUBS"];
}

//get current user subscription from cookies 
$postfix = $arResult["SHOW_SMS_FORM"] ? "_M_SUBSCR_SMS" : "_M_SUBSCR_EMAIL"; 
global $USER;
$email_cookie = COption::GetOptionString("main", "cookie_name", "BITRIX_SM").$postfix;

$subscr_EMAIL = (strlen($_COOKIE[$email_cookie]) > 0? $_COOKIE[$email_cookie] : $USER->GetParam("EMAIL"));
if($subscr_EMAIL <> "")
{
    $subscr = CSubscription::GetByEmail($subscr_EMAIL);
    if(($subscr_arr = $subscr->Fetch()))
        $arSubscription = $subscr_arr;
}
else
    $arSubscription = array("ID"=>0, "EMAIL"=>"");

//get user's newsletter categories
$arSubscriptionRubrics = CSubscription::GetRubricArray(intval($aSubscr["ID"]));
//get site's newsletter categories
$obCache = new CPHPCache;
$strCacheID = $componentPath.LANG.$arParams["SHOW_HIDDEN"];
if($obCache->StartDataCache($arParams["CACHE_TIME"], $strCacheID, $componentPath))
{
	$arFilter = array("ACTIVE"=>"Y", "LID"=>LANG);
	if(!$arParams["SHOW_HIDDEN"])
		$arFilter["VISIBLE"]="Y";
	$rsRubric = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), $arFilter);
	$arRubrics = array();
	while($arRubric = $rsRubric->GetNext())
	{
		$arRubric["SUBSCRIBER_COUNT"]=$arParams["SHOW_COUNT"] ? CRubric::GetSubscriptionCount($arRubric["ID"]):0;
		$arRubrics[]=$arRubric;
	}
	$obCache->EndDataCache($arRubrics);
}
else
{
	$arRubrics = $obCache->GetVars();
}

if(count($arRubrics)<=0)
{
	ShowError(GetMessage("SUBSCR_NO_RUBRIC_FOUND"));
	return;
}

$arResult["FORM_ACTION"] = htmlspecialchars(str_replace("#SITE_DIR#", LANG_DIR, $arParams["PAGE"]));
$arResult["SHOW_COUNT"] = $arParams["SHOW_COUNT"];

if(strlen($arSubscription["EMAIL"])>0)
	$arResult["EMAIL"] = htmlspecialchars($arSubscription["EMAIL"]);
else
	$arResult["EMAIL"] = htmlspecialchars($USER->GetParam("EMAIL"));

//check whether already authorized
$arResult["SHOW_PASS"] = true;
if($arSubscription["ID"] > 0)
{
	//try to authorize user account's subscription
	if($arSubscription["USER_ID"]>0 && !CSubscription::IsAuthorized($arSubscription["ID"]))
		CSubscription::Authorize($arSubscription["ID"], "");
	//check authorization
	if(CSubscription::IsAuthorized($arSubscription["ID"]))
		$arResult["SHOW_PASS"] = false;
}

//checking what we have:email or telephone
if ($USER->IsAuthorized())
{
	//получаем подписку пользователя
	$ar_user_subs = CSubscription::GetList(array("ID"=>"asc"),array("USER_ID"=>$USER->GetID()));
	$post_sub = '';
	$sms_sub = '';
	while($an_sub = $ar_user_subs->Fetch())
	{
		if (preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$an_sub["EMAIL"]))
		{
			$sms_sub = $an_sub;
		}
		elseif(check_email($an_sub["EMAIL"]))
		{
			$post_sub = $an_sub;
		}
	}
	
//if there is no subscribtion, we fill it with emails and telephones
	if($post_sub["EMAIL"] == '')   
		$post_sub["EMAIL"] = $USER->GetEmail();

	if($sms_sub["EMAIL"] == '')
	{  
		
		$rsU = CUser::GetList($by = "ID", $sort = "desc", array("ID" => $USER->GetID()));
		if($arU = $rsU->Fetch())
			$sms_sub["EMAIL"] =$arU["PERSONAL_MOBILE"];
	}
	

	$arResult["SMS_SUB"] = $sms_sub;
	$arResult["POST_SUB"] = $post_sub;

	//get rubrics for post
	$checked_rub_post = CSubscription::GetRubricList($post_sub["ID"]);
	while($rub = $checked_rub_post->Fetch())
	{
		$arResult["RUBRICS_POST"][] = $rub["ID"];      
	}
	//get rubrics for sms
	$checked_rub_sms = CSubscription::GetRubricList($sms_sub["ID"]);
	while($rub_sms = $checked_rub_sms->Fetch())
	{
		$arResult["RUBRICS_SMS"][] =  $rub_sms["ID"];
	}

	//what label on button 'll be
	if ($arResult['SMS_SUB']['ID'] > 0 && $USER->IsAuthorized() || CSubscription::IsAuthorized($arResult['SMS_SUB']['ID']))
	{
		$arResult["SUBMIT_SMS_BUTTON_NAME"] = "UPD";
	}
	if ($arResult['POST_SUB']['ID'] > 0 && $USER->IsAuthorized() || CSubscription::IsAuthorized($arResult['POST_SUB']['ID']))
	{
		$arResult["SUBMIT_POST_BUTTON_NAME"] = "UPD";
	} 		
}
else
{
	//anonymous subscribe
	$getted_cdb = CSubscription::GetByEmail($arResult["EMAIL"]);
	$getted_fetch = $getted_cdb->Fetch();

	if (preg_match("/^[ \+\-\(\)0-9]+?@phone.sms$/",$getted_fetch["EMAIL"]))
	{
		$sms_sub = $getted_fetch;
	}
	elseif(check_email($getted_fetch["EMAIL"]))
	{
		$post_sub = $getted_fetch;
	}

	$arResult["SMS_SUB"] = $sms_sub;
	$arResult["POST_SUB"] = $post_sub;

	$checked_rub_post = CSubscription::GetRubricList($post_sub["ID"]);
	while($rub = $checked_rub_post->Fetch())
	{
		$arResult["RUBRICS_POST"][] = $rub["ID"];
	}

	$checked_rub_sms = CSubscription::GetRubricList($sms_sub["ID"]);
	while($rub = $checked_rub_sms->Fetch())
	{
		$arResult["RUBRICS_SMS"][] =  $rub["ID"];
	}

	if ($arResult['SMS_SUB']['ID'] > 0 && $USER->IsAuthorized() || CSubscription::IsAuthorized($arResult['SMS_SUB']['ID']))
	{
		$arResult["SUBMIT_SMS_BUTTON_NAME"] = "UPD";
	}
	if ($arResult['POST_SUB']['ID'] > 0 && $USER->IsAuthorized() || CSubscription::IsAuthorized($arResult['POST_SUB']['ID']))
	{
		$arResult["SUBMIT_POST_BUTTON_NAME"] = "UPD";
	}
}

$arResult["ALL_RUBRICS"] = array();
foreach($arRubrics as $arRubric)
{
//filtration on subscribe type
	if(strpos($arRubric["NAME"],'SMS') === false && strpos($arRubric["DESCRIPTION"],'SMS') === false)
		$ARN = 'EMAIL';
	else
		$ARN = 'SMS';
//end
		
	$arResult["ALL_RUBRICS"][$ARN][]=array(
		"ID"=>$arRubric["ID"],
		"NAME"=>$arRubric["NAME"],
		"DESCRIPTION"=>$arRubric["DESCRIPTION"],
		"SUBSCRIBER_COUNT"=>$arRubric["SUBSCRIBER_COUNT"],
	);

}

if($arParams["SET_TITLE"]=="Y")
	$APPLICATION->SetTitle(GetMessage("SUBSCR_PAGE_TITLE"));

$this->IncludeComponentTemplate();
?>