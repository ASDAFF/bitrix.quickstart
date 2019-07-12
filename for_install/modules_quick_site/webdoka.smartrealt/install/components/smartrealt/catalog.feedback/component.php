<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('webdoka.smartrealt'))
{
    ShowError(GetMessage('SMARTREALT_MODULE_NOT_INSTALL'));
    return;
}

$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if(strlen($arParams["EVENT_NAME"]) <= 0)
	$arParams["EVENT_NAME"] = "SMARTREALT_FEEDBACK_FORM";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if(strlen($arParams["EMAIL_TO"]) <= 0)
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if(strlen($arParams["OK_TEXT"]) <= 0)
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");  

if($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["submit"]) > 0)
{
	if(check_bitrix_sessid())
	{
		if(empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"]))
		{
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["NAME"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_NAME");        
            if((empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["EMAIL"]) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_EMAIL");        
            if((empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["PHONE"]) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_TELEPHONE");		
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("OBJECT_NUMBER", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["OBJECT_NUMBER"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_OBJECT_NUMBER");
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MESSAGE"]) <= 3)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_MESSAGE");
		}
		if(strlen($_POST["EMAIL"]) > 1 && !check_email($_POST["EMAIL"]))
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_EMAIL_NOT_VALID");
            
        if (strlen($_POST["OBJECT_NUMBER"]) > 0)
        {
            $oCatalogElement = new SmartRealt_CatalogElement();
            $rsElement = $oCatalogElement->GetList(array('Number' => $_POST["OBJECT_NUMBER"]));
            
            if ($rsElement->SelectedRowsCount() != 1)
            {
                $arResult["ERROR_MESSAGE"][] = GetMessage("MF_OBJECT_NOT_FOUND");
            }
        }
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
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

		}			
		if(empty($arResult))
		{
            if(strlen($arParams["OBJECT_NUMBER_DEFAULT"]) > 0)
            {
                $_POST["OBJECT_NUMBER"] = $arParams["OBJECT_NUMBER_DEFAULT"];
            }
            
            $arFields = Array(
                "NAME" => $_POST["NAME"],
                "EMAIL" => $_POST["EMAIL"],
                "PHONE" => $_POST["PHONE"],
                "OBJECT_NUMBER" => $_POST["OBJECT_NUMBER"],
                "MESSAGE" => $_POST["MESSAGE"],
            );
            
            if (strlen($_POST["OBJECT_NUMBER"]) > 0)
            {
                $oCatalogElement = new SmartRealt_CatalogElement();
                $rsElement = $oCatalogElement->GetList(array('Number' => $_POST["OBJECT_NUMBER"]));
                
                if ($arElement = $rsElement->Fetch())
                {
                    $arFields['OBJECT_SECTION_NAME'] = $arElement['SectionFullNameSign'];
                    $arFields['OBJECT_ADDRESS'] = SmartRealt_CatalogElement::GetAddress($arElement); 
                    $arFields['OBJECT_PRICE'] = SmartRealt_CatalogElement::FormatPrice($arElement);
                }
            }
            
			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
                    {
                        $res = CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
                    }                                                                               
			}
			else
            {
				CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
            }

			$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["NAME"]);
			$_SESSION["MF_EMAIL"] = htmlspecialcharsEx($_POST["EMAIL"]);
            
            if (strlen($arParams['MOBILEPHONE_TO']) > 0 && CModule::IncludeModule('rarus.sms4b'))
            {
                global $SMS4B;
                $message = $_POST["MESSAGE"].'. '.$_POST["NAME"].' '.$_POST["PHONE"];
                //CSMSKontakt::Send($message, $arParams['MOBILEPHONE_TO'], "UTF-8");
                $SMS4B->SendSMS($message,$arParams['MOBILEPHONE_TO']);
            }
            
			LocalRedirect($APPLICATION->GetCurPageParam("success=Y", Array("success")));
		}
		
		$arResult["MESSAGE"] = htmlspecialcharsEx($_POST["MESSAGE"]);
		$arResult["NAME"] = htmlspecialcharsEx($_POST["NAME"]);
        $arResult["EMAIL"] = htmlspecialcharsEx($_POST["EMAIL"]);
        $arResult["OBJECT_NUMBER"] = htmlspecialcharsEx($_POST["OBJECT_NUMBER"]);
		$arResult["PHONE"] = htmlspecialcharsEx($_POST["PHONE"]);
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
}
elseif($_REQUEST["success"] == "Y")
{
    $arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];                           
}

if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["NAME"] = htmlspecialcharsEx($USER->GetFullName());
		$arResult["EMAIL"] = htmlspecialcharsEx($USER->GetEmail());
	}
	else
	{
		if(strlen($_SESSION["MF_NAME"]) > 0)
			$arResult["NAME"] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
		if(strlen($_SESSION["MF_EMAIL"]) > 0)
			$arResult["EMAIL"] = htmlspecialcharsEx($_SESSION["MF_EMAIL"]);
	}
}

if (strlen($arParams['MESSAGE_DEFAULT']) >0 && !isset($_POST["MESSAGE"]))
{
    $arResult["MESSAGE"] = $arParams['MESSAGE_DEFAULT'];
}

if (strlen($arParams['OBJECT_NUMBER_DEFAULT']) >0)
{
    $arResult["OBJECT_NUMBER"] = $arParams["OBJECT_NUMBER_DEFAULT"];
}

if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
?>