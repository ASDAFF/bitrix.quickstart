<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// Default params
$arParams["SUCCESS_MESSAGE"] = trim($arParams["SUCCESS_MESSAGE"])!="" ? trim($arParams["SUCCESS_MESSAGE"]) : GetMessage("WEBDEBUG_REVIEWS_SUCCESS_MESSAGE");
if (!isset($arParams["DISPLAY_FIELDS"]) || !is_array($arParams["DISPLAY_FIELDS"]) || empty($arParams["DISPLAY_FIELDS"])) $arParams["DISPLAY_FIELDS"] = array("NAME", "EMAIL", "TEXT_PLUS", "TEXT_MINUS", "TEXT_COMMENTS", "VOTE_0", "VOTE_1", "VOTE_2");
if (!isset($arParams["REQUIRED_FIELDS"]) || !is_array($arParams["REQUIRED_FIELDS"]) || empty($arParams["REQUIRED_FIELDS"])) $arParams["REQUIRED_FIELDS"] = array("NAME","TEXT_COMMENTS");
$arParams["USE_MODERATE"] = $arParams["USE_MODERATE"]=="N" ? "N" : "Y";

// Fill $arResult
$arResult = array(
	"USER_AUTHORIZED" => CUser::IsAuthorized(),
	"USER_ID" => IntVal(CUser::GetID()),
	"NAME" => $_REQUEST["name"],
	"EMAIL" => $_REQUEST["email"],
	"EMAIL_PUBLIC" => htmlspecialchars($_REQUEST["email_public"]),
	"WWW" => $_REQUEST["www"],
	"TEXT_PLUS" => $_REQUEST["text_plus"],
	"TEXT_MINUS" => $_REQUEST["text_minus"],
	"TEXT_COMMENTS" => $_REQUEST["text_comments"],
	"VOTE_0" => htmlspecialchars($_REQUEST["vote_0"]),
	"VOTE_1" => htmlspecialchars($_REQUEST["vote_1"]),
	"VOTE_2" => htmlspecialchars($_REQUEST["vote_2"]),
	"VOTE_3" => htmlspecialchars($_REQUEST["vote_3"]),
	"VOTE_4" => htmlspecialchars($_REQUEST["vote_4"]),
	"VOTE_5" => htmlspecialchars($_REQUEST["vote_5"]),
	"VOTE_6" => htmlspecialchars($_REQUEST["vote_6"]),
	"VOTE_7" => htmlspecialchars($_REQUEST["vote_7"]),
	"VOTE_8" => htmlspecialchars($_REQUEST["vote_8"]),
	"VOTE_9" => htmlspecialchars($_REQUEST["vote_9"]),
);
if (trim($arResult["NAME"])=="" && $arResult["USER_AUTHORIZED"]) $arResult["NAME"] = CUser::GetFirstName();
if (trim($arResult["EMAIL"])=="" && $arResult["USER_AUTHORIZED"]) $arResult["EMAIL"] = CUser::GetEmail();

$arResult["USE_CAPTCHA"] = $arParams["USE_CAPTCHA"]=="N" ? false : true;
if ($arResult["USER_AUTHORIZED"]) $arResult["USE_CAPTCHA"] = false;
$arResult["CAPTCHA_ID"] = "";
if ($arResult["USE_CAPTCHA"]) {
	$arResult["CAPTCHA_ID"] = $APPLICATION->CaptchaGetCode();
}

$IBlockID = IntVal($arParams["IBLOCK_ID"]);
if ($IBlockID<=0) return;

$ElementID = IntVal($arParams["ELEMENT_ID"]);
if ($ElementID==0 && trim($arParams["ELEMENT_CODE"])!="" && CModule::IncludeModule("iblock")) {
	$resElement = CIBlockElement::GetList(false,array("IBLOCK_ID"=>$IBlockID,"CODE"=>trim($arParams["ELEMENT_CODE"]),"ACTIVE"=>"Y"), false, false, array("IBLOCK_ID","ID"));
	if ($arElement = $resElement->GetNext(false,false)) {
		$ElementID = $arElement["ID"];
	}
}
if ($ElementID<=0) return;

$arResult["ERROR_MESSAGES"] = array();
$arResult["SUCCESS"]=false;
if (isset($_GET["success"]) && $_GET["success"]=="Y") {
	$arResult["SUCCESS"] = "Y";
}

for ($i=0; $i<10; $i++) {
	$arResult["VOTE_NAME_".$i] = COption::GetOptionString("webdebug.reviews", "vote_name_".$i);
}

if (isset($_REQUEST["sent"]) && ToLower($_REQUEST["sent"])=="y") {
	unset($arResult["SUCCESS"]);
	// Check fields
	$Captcha_OK = !$arResult["USE_CAPTCHA"] || $APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_id"]);
	$Name_OK = !(in_array("NAME", $arParams["DISPLAY_FIELDS"]) && in_array("NAME", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["NAME"]))<3);
	$Email_OK = !(in_array("EMAIL", $arParams["DISPLAY_FIELDS"]) && in_array("EMAIL", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["EMAIL"]))<3);
	$Www_OK = !(in_array("WWW", $arParams["DISPLAY_FIELDS"]) && in_array("WWW", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["WWW"]))<3);
	$TextPlus_OK = !(in_array("TEXT_PLUS", $arParams["DISPLAY_FIELDS"]) && in_array("TEXT_PLUS", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["TEXT_PLUS"]))<1);
	$TextMinus_OK = !(in_array("TEXT_MINUS", $arParams["DISPLAY_FIELDS"]) && in_array("TEXT_MINUS", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["TEXT_MINUS"]))<1);
	$TextComments_OK = !(in_array("TEXT_COMMENTS", $arParams["DISPLAY_FIELDS"]) && in_array("TEXT_COMMENTS", $arParams["REQUIRED_FIELDS"]) && strlen(trim($arResult["TEXT_COMMENTS"]))<1);
	$Vote0_OK = !(in_array("VOTE_0", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_0", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_0"])<=0);
	$Vote1_OK = !(in_array("VOTE_1", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_1", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_1"])<=0);
	$Vote2_OK = !(in_array("VOTE_2", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_2", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_2"])<=0);
	$Vote3_OK = !(in_array("VOTE_3", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_3", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_3"])<=0);
	$Vote4_OK = !(in_array("VOTE_4", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_4", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_4"])<=0);
	$Vote5_OK = !(in_array("VOTE_5", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_5", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_5"])<=0);
	$Vote6_OK = !(in_array("VOTE_6", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_6", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_6"])<=0);
	$Vote7_OK = !(in_array("VOTE_7", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_7", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_7"])<=0);
	$Vote8_OK = !(in_array("VOTE_8", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_8", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_8"])<=0);
	$Vote9_OK = !(in_array("VOTE_9", $arParams["DISPLAY_FIELDS"]) && in_array("VOTE_9", $arParams["REQUIRED_FIELDS"]) && IntVal($arResult["VOTE_9"])<=0);

	if ($Captcha_OK && $Name_OK && $Email_OK && $Www_OK && $TextPlus_OK && $TextMinus_OK && $TextComments_OK && $Vote0_OK && $Vote1_OK && $Vote2_OK && $Vote3_OK && $Vote4_OK && $Vote5_OK && $Vote6_OK && $Vote7_OK && $Vote8_OK && $Vote9_OK) {
		// Set fields
		$arFields = array(
			"IBLOCK_ID" => $IBlockID,
			"ELEMENT_ID" => $ElementID,
			"MODERATED" => $arParams["USE_MODERATE"]=="N" ? "Y" : "N",
			"SITE_ID" => SITE_ID,
			"USER_ID" => $arResult["USER_ID"],
			"NAME" => $arResult["NAME"],
			"EMAIL" => $arResult["EMAIL"],
			"EMAIL_PUBLIC" => $arResult["EMAIL_PUBLIC"],
			"WWW" => $arResult["WWW"],
			"DATETIME" => date(CDatabase::DateFormatToPHP(CSite::GetDateFormat("FULL"))),
			"TEXT_PLUS" => $arResult["TEXT_PLUS"],
			"TEXT_MINUS" => $arResult["TEXT_MINUS"],
			"TEXT_COMMENTS" => $arResult["TEXT_COMMENTS"],
			"VOTE_0" => $arResult["VOTE_0"],
			"VOTE_1" => $arResult["VOTE_1"],
			"VOTE_2" => $arResult["VOTE_2"],
			"VOTE_3" => $arResult["VOTE_3"],
			"VOTE_4" => $arResult["VOTE_4"],
			"VOTE_5" => $arResult["VOTE_5"],
			"VOTE_6" => $arResult["VOTE_6"],
			"VOTE_7" => $arResult["VOTE_7"],
			"VOTE_8" => $arResult["VOTE_8"],
			"VOTE_9" => $arResult["VOTE_9"],
		);
		// Save
		if (CModule::IncludeModule("webdebug.reviews")) {
			$WebdebugReviews = new CWebdebugReviews;
			$SaveResult = $WebdebugReviews->Add($arFields);
			if ($SaveResult) {
				// Send email
				if (isset($arParams["EVENT_TEMPLATES"]) && is_array($arParams["EVENT_TEMPLATES"])) {
					// get element name
					$arFields["ELEMENT_NAME"] = "";
					if (CModule::IncludeModule("iblock")) {
						$resElement = CIBlockElement::GetList(false,array("IBLOCK_ID"=>$IBlockID,"ID"=>$ElementID),false,false,array("IBLOCK_ID","NAME"));
						if ($arElement = $resElement->GetNext(false,false)) {
							$arFields["ELEMENT_NAME"] = $arElement["NAME"];
						}
					}
					foreach($arParams["EVENT_TEMPLATES"] as $EventMessageID) {
						$EventMessageID = IntVal($EventMessageID);
						if($EventMessageID > 0) {
							CEvent::SendImmediate("WEBDEBUG_REVIEWS", SITE_ID, $arFields, "Y", $EventMessageID);
						}
					}
				}
				if (isset($_REQUEST["AJAX_CALL"])) {
					$arResult["SUCCESS"]="Y";
				} else {
					$arDeleteParameters = explode(",", $arParams["DELETE_PARAMETERS"]);
					$arDeleteParameters = array_filter($arDeleteParameters);
					foreach($arDeleteParameters as $Key => $Value) {
						$arDeleteParameters[$Key] = trim($Value);
					}
					$arDeleteParameters[] = 'success';
					$arDeleteParameters = array_unique($arDeleteParameters);
					LocalRedirect($APPLICATION->GetCurPageParam('success=Y',$arDeleteParameters));
				}
			}
		} else {
			$arResult["SUCCESS"]="N";
		}
	} else {
		$arResult["SUCCESS"]="N";
		// Write errors
		if (!$Captcha_OK && trim($_REQUEST["captcha_word"])=="") $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_CAPTCHA_EMPTY");
		elseif (!$Captcha_OK && trim($_REQUEST["captcha_word"])!="") $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_CAPTCHA_WRONG");
		elseif (!$Name_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_NAME");
		elseif (!$Email_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_EMAIL");
		elseif (!$Www_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_WWW");
		elseif (!$TextPlus_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_TEXT_PLUS");
		elseif (!$TextMinus_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_TEXT_MINUS");
		elseif (!$TextComments_OK) $arResult["ERROR_MESSAGES"][] = GetMessage("WEBDEBUG_REVIEWS_ERROR_TEXT_COMMENTS");
	}
}

$this->IncludeComponentTemplate();

?>