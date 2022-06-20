<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

/**
 * Умная обратная связь на базе Технической поддержки
 */
Class MDTicket
{
    function CheckUser($email)
    {
        $rsUser = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL"=>$email))->Fetch();
        if($rsUser["ID"]>0)
        {
            return false;
        }
        return true;
    }


}

$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

$arParams["SUCCESS_TEXT"] = trim($arParams["SUCCESS_TEXT"]);
if(strlen($arParams["SUCCESS_TEXT"]) <= 0)
	$arParams["SUCCESS_TEXT"] = GetMessage("MD_OK_MESSAGE");

$arParams['NEW_EXT_FIELDS'] = Array();

if (is_array($arParams['EXT_FIELDS']))
	foreach($arParams["EXT_FIELDS"] as $ext_field)
		if (strlen($ext_field) > 0)
			$arParams['NEW_EXT_FIELDS'][str_replace(" ", "_", $ext_field)] = $ext_field;

//Gather CTicket information 
CModule::IncludeModule("support");
$arResult=array();
$by = "s_c_sort"; $order = "asc";
$dbSupport = CTicketDictionary::GetList($by, $order, array("TYPE"=>"C || K"), $is_filtered);
while($arSupport = $dbSupport->GetNext())
{
	if($arSupport["C_TYPE"]=="C")
	{
		
		$arResult["CATEGORY"][]=$arSupport;
	}
	elseif($arSupport["C_TYPE"]=="K")
	{
		$arResult["CRITICAL"][]=$arSupport;
	}
}



///POST EXECUTE
if($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["submit"]) > 0 && $_POST["MD_POST"]=="Y")
{
	if(check_bitrix_sessid())
	{
		
		//REQIERED FIELDS CHECK
		if(empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"]))
		{
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MD_NAME"]) <= 1)
				$arResult["ERROR_MESSAGE"]["NAME"] = GetMessage("MD_REQ_NAME");		
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MD_EMAIL"]) <= 1)
				$arResult["ERROR_MESSAGE"]["EMAIL"] = GetMessage("MD_REQ_EMAIL");
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MD_MESSAGE"]) <= 3)
				$arResult["ERROR_MESSAGE"]["MESSAGE"] = GetMessage("MD_REQ_MESSAGE");
			
			///EXT FIELDS
			foreach($arParams['NEW_EXT_FIELDS'] as $fid=>$field)
			{
				if((empty($arParams["REQUIRED_FIELDS"]) || in_array($fid, $arParams["REQUIRED_FIELDS"])) && strlen($_POST[$fid])<=1)
					$arResult["ERROR_MESSAGE"][$fid] = GetMessage("MD_REQ_FIELD", array("#FIELD#"=>$field));
			}
		}
		if(strlen($_POST["MD_EMAIL"]) > 1 && !check_email($_POST["MD_EMAIL"]))
			$arResult["ERROR_MESSAGE"]["EMAIL"] = GetMessage("MD_EMAIL_NOT_VALID");
		if($arParams["USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"]["CAPTCHA"] = GetMessage("MD_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"]["CAPTCHA"] = GetMessage("MD_CAPTCHA_EMPTY");

		}
		///check user
		if($arParams["AUTO_REGISTER"]=="Y" && !$USER->isAuthorized() && $_POST["MD_EMAIL"])
		{
			//$rsUser = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL"=>$_POST['MD_EMAIL']))->Fetch();
			if(!MDTicket::CheckUser($_POST["MD_EMAIL"]))
			{
				$arResult["ERROR_MESSAGE"]["CUSTOM"][] = GetMessage("MD_EMAIL_HAS");
			}
		}
		
/*################################################################################################################*/		
		/// IF HAS NO ERROR, MAIN EXECUTE(CREATE TP TICKET) AND SEND MESSAGE
		if(empty($arResult["ERROR_MESSAGE"]))
		{
			
			//@$arParams["AUTO_REGISTER"]
			if($arParams["AUTO_REGISTER"]=="Y" && !$USER->isAuthorized())
			{
				$user = new CUser;
				$password = randString(7);
				$arFields = Array(
						"NAME"              => $_POST['MD_NAME'],
						"EMAIL"             => $_POST['MD_EMAIL'],
						"LOGIN"             => $_POST["MD_EMAIL"],
						"ACTIVE"            => "Y",
						"PASSWORD"          => $password,
						"CONFIRM_PASSWORD"  => $password,
				);
				$ID = $user->Add($arFields);//create user
				if (intval($ID) > 0)
				{
					CUser::SendUserInfo($ID, SITE_ID, GetMessage("WELCOME"));//send user info
					$NEW_USER_ID=$ID;
				}
				else
				{
					$arResult["ERROR_MESSAGE"]["CUSTOM"][]=$user->LAST_ERROR;
				}
			}
			
			//create TICKET 
			$ticket=new CTicket();
			if(empty($arResult["ERROR_MESSAGE"])){
				//@if category and status are not shown
				if($arParams["SHOW_CATEGORY"]!=="Y")
					$_POST["MD_CATEGORY"]=$arResult["CATEGORY"][0]["ID"];
				if($arParams["SHOW_STATUS"]!=="Y")
					$_POST["CRITICAL"]=$arResult["CRITICAL"][0]["ID"];
				//@ POST HANDLER
				/*##*/	
				$GENERAL_MESSAGE ="<b>E-Mail:</b> ".$_POST['MD_EMAIL']."\n";
				foreach($arParams['NEW_EXT_FIELDS'] as $fid=>$field)
				{
					$GENERAL_MESSAGE.="<b>".$field.":</b> ".$_POST[$fid]."\n";
				}
				if($arParams["ADD_VAR"])
				{
					$GENERAL_MESSAGE.="<b>".GetMessage("ELEMENTS").":</b>\n" ;
					$count=1;
					foreach($arParams["ADD_VAR"] as $id=>$items)
					{
						$GENERAL_MESSAGE.=$count.". ".$items." (".$id.") \n";
						$count++;
					}
				}
				$GENERAL_MESSAGE.="<b>".GetMessage("MESSAGES").":</b> ".$_POST['MD_MESSAGE'];
				/*##*/
				if($arParams["AUTO_REGISTER"]=="Y" && !$USER->isAuthorized())//if auto register
					{
						$arTicketFields = array(
								"TITLE"                     => GetMessage("MICROS_REQUEST_ZAAVKA_OT")." ".$_POST['MD_NAME'],
								"MESSAGE"                   => $GENERAL_MESSAGE,
								"OWNER_SID"                 => $NEW_USER_ID,
								"OWNER_USER_ID"				=> $NEW_USER_ID,
								"MESSAGE_AUTHOR_USER_ID"	=> $NEW_USER_ID,
								"MESSAGE_SOURCE_SID"        => $_POST["MD_EMAIL"],
								"CATEGORY_ID"				=> $_POST["MD_CATEGORY"],
								"CRITICALITY_ID"          => $_POST["CRITICAL"]
						);
					}
					elseif($arParams["AUTO_REGISTER"]!=="Y" && !$USER->isAuthorized())//if auto register is off, connect with email 
					{
						$arTicketFields = array(
								"TITLE"                     => GetMessage("MICROS_REQUEST_ZAAVKA_OT")." ".$_POST['MD_NAME'],
								"MESSAGE"                   => $GENERAL_MESSAGE,
								"OWNER_SID"                 => $_POST["MD_EMAIL"],
								"SOURCE_ID"				=> 14,
								"MESSAGE_AUTHOR_USER_ID"	=> $_POST["MD_EMAIL"],
								"MESSAGE_SOURCE_SID"        => $_POST["MD_EMAIL"],
								"CATEGORY_ID"				=> $_POST["MD_CATEGORY"],
								"CRITICALITY_ID"          => $_POST["CRITICAL"]
						);
					}else///if user is authorized
					{
						$arTicketFields = array(
								"TITLE"                     => GetMessage("MICROS_REQUEST_ZAAVKA_OT")." ".$_POST['MD_NAME'],
								"OWNER_SID"                 => $USER->GetEmail(),
								"OWNER_USER_ID"				=> $USER->GetID(),
								"MESSAGE_AUTHOR_USER_ID"	=> $USER->GetID(),
								"MESSAGE_SOURCE_SID"        => $_POST["MD_EMAIL"],
								"CATEGORY_ID"				=> $_POST["MD_CATEGORY"],
								"CRITICALITY_ID"          	=>5,
								"MESSAGE"                   => $GENERAL_MESSAGE,
									
						);
					}
				
				$NEW_TICKET_ID = $ticket->SetTicket($arTicketFields,  $TICKET_ID, "N");
				if($NEW_TICKET_ID)
				{
					$_SESSION["MD_NAME"] = htmlspecialcharsEx($_POST["MD_NAME"]);
					$_SESSION["MD_EMAIL"] = htmlspecialcharsEx($_POST["MD_EMAIL"]);
					LocalRedirect($APPLICATION->GetCurPageParam("success=ok", Array("success")));
				}
			}//if(empty($arResult["ERROR_MESSAGE"]))
		}
		
/*######################################################################################################################*/		
		///FOR AUTO COMPLETE (UNLESS ERRORS)
		$arResult["MESSAGE"] = htmlspecialcharsEx($_POST["MD_MESSAGE"]);
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_POST["MD_NAME"]);
		$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_POST["MD_EMAIL"]);
		foreach($arParams['NEW_EXT_FIELDS'] as $field)
		{
			$arResult["CUSTOM"][$field] = htmlspecialcharsEx($_POST[$field]);
		}
	}
	else
		$arResult["ERROR_MESSAGE"]["CUSTOM"][] = GetMessage("MD_SESS_EXP");
}///SUCCESS EXECUTE
elseif($_REQUEST["success"] == "ok")
{
	$arResult["OK_MESSAGE"] = $arParams["SUCCESS_TEXT"];
}

///AUTO COMPLETE
if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($USER->GetFullName());
		$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($USER->GetEmail());
	}
	else
	{
		if(strlen($_SESSION["MD_NAME"]) > 0)
			$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_SESSION["MD_NAME"]);
		if(strlen($_SESSION["MD_EMAIL"]) > 0)
			$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_SESSION["MD_EMAIL"]);
	}
}

if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());

if($arParams["ALLOW_NONUSER"]!=="Y" && !$USER->isAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("DO_AUTH"));
	
}
$this->IncludeComponentTemplate();
?>