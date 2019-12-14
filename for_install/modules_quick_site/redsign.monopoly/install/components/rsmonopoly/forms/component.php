<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;

$arParams["EVENT_TYPE"] = ($arParams['EVENT_TYPE']!='' ? $arParams['EVENT_TYPE'] : '');
$arParams["REQUEST_PARAM_NAME"] = "redsign_monopoly_form";
$arParams["SHOW_FIELDS"] = is_array($arParams["SHOW_FIELDS"]) ? $arParams["SHOW_FIELDS"] : array();
$arParams["EMAIL_TO"] = ( $arParams["EMAIL_TO"]!='' ? $arParams["EMAIL_TO"] : COption::GetOptionString('main','email_from','') );
$arEventFields = array();
$arEventFields["EMAIL_TO"] = $arParams["EMAIL_TO"];
$arResult["ACTION_URL"] = $APPLICATION->GetCurPage();
$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());
$arResult["MESSAGE_AGREE"] = ( $arParams["MESSAGE_AGREE"]!='' ? $arParams["MESSAGE_AGREE"] : GetMessage('MSG_MESSAGE_AGREE') );

$arFields = array(
	array(
		"CONTROL_NAME" => "RS_NAME",
		"CONTROL_ID" => "RS_NAME",
		"SHOW" => in_array("RS_NAME", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "AUTHOR_NAME",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_PHONE",
		"CONTROL_ID" => "RS_PHONE",
		"SHOW" => in_array("RS_PHONE", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "AUTHOR_PHONE",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_PERSONAL_SITE",
		"CONTROL_ID" => "RS_PERSONAL_SITE",
		"SHOW" => in_array("RS_PERSONAL_SITE", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "PERSONAL_SITE",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_ORGANISATION_NAME",
		"CONTROL_ID" => "RS_ORGANISATION_NAME",
		"SHOW" => in_array("RS_ORGANISATION_NAME", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "ORGANISATION_NAME",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
	array(
		"CONTROL_NAME" => "RS_EMAIL",
		"CONTROL_ID" => "RS_EMAIL",
		"SHOW" => in_array("RS_EMAIL", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
		"EVENT_FIELD_NAME" => "AUTHOR_EMAIL",
		"VALUE" => "",
		"HTML_VALUE" => "",
	),
);

$count = IntVal( $arParams['RS_MONOPOLY_EXT_FIELDS_COUNT'] );
if( $count>0 ) {
	$arResult['RS_MONOPOLY_EXT_FIELDS_COUNT'] = $count;
	for($i=0; $i<$count; $i++) {
		$arFields[] = array(
			"CONTROL_NAME" => "RS_EXT_FIELD_".$i,
			"CONTROL_ID" => "RS_EXT_FIELD_".$i,
			"SHOW" => "Y",
			"EVENT_FIELD_NAME" => "EXT_FIELD_".$i,
			"VALUE" => "",
			"HTML_VALUE" => "",
			"EXT" => "Y",
			"INDEX" => $i,
		);
	}
}
$arFields[] = array(
	"CONTROL_NAME" => "RS_TEXTAREA",
	"CONTROL_ID" => "RS_TEXTAREA",
	"SHOW" => in_array("RS_TEXTAREA", $arParams["SHOW_FIELDS"]) ? "Y" : "N",
	"EVENT_FIELD_NAME" => "COMMENT",
	"VALUE" => "",
	"HTML_VALUE" => "",
);
$arResult["FIELDS"] = $arFields;

if($USER->IsAuthorized()) $arParams["USE_CAPTCHA"] = "N";

if($arParams["USE_CAPTCHA"]=="Y") {
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
	$cpt = new CCaptcha();
	$captchaPass = COption::GetOptionString("main", "captcha_password", "");
	if(strlen($captchaPass) <= 0) {
		$captchaPass = randString(10);
		COption::SetOptionString("main", "captcha_password", $captchaPass);
	}
	$cpt->SetCodeCrypt($captchaPass);
	$arResult["CATPCHA_CODE"] = htmlspecialchars( $cpt->GetCodeCrypt() );
}

if($_REQUEST[$arParams['REQUEST_PARAM_NAME']]=='Y') {
	if( class_exists(CEventType) && class_exists(CEventMessage) && $arParams["EVENT_TYPE"]!='' ) {
		$eventIsset = true;
		$arFilter = array("TYPE_ID" => $arParams["EVENT_TYPE"], "LID" => "ru");
		$rsET = CEventType::GetList($arFilter);
		if(!$arET = $rsET->Fetch()) {
			$eventIsset = false;
		}
		$arResult["LAST_ERROR"] = "";
		$arResult["GOOD_SEND"] = "";
		
		if($eventIsset) {
			if(check_bitrix_sessid() && (!isset($_REQUEST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_REQUEST["PARAMS_HASH"])) {
				if($arParams["USE_CAPTCHA"] == "Y") {
					if(strlen($_POST["captcha_word"])<1 && strlen($_POST["captcha_sid"])<1) {
						$arResult["LAST_ERROR"] = GetMessage("MSG_CAPTCHA_EMPRTY");
					} elseif(!$APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_sid"])) {
						$arResult["LAST_ERROR"] = GetMessage("MSG_CAPTCHA_WRONG");
					}
				}
				if($arResult['LAST_ERROR']=='') {
					if( $arParams['EMAIL_TO']!='' ) {
						foreach($arResult["FIELDS"] as $key => $arField) {
							$arEventFields[$arField["EVENT_FIELD_NAME"]] = trim( ( $_REQUEST[$arField["CONTROL_NAME"]] ) );
							if((empty($arParams["REQUIRED_FIELDS"]) || in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])) && strlen($_REQUEST[$arField["CONTROL_NAME"]]) <= 1) {
								$arResult["LAST_ERROR"] = GetMessage("MSG_EMPTY_REQUIRED_FIELDS");
							}
						}
						if($arResult['LAST_ERROR']=='') {
							CEvent::Send($arParams['EVENT_TYPE'], SITE_ID, $arEventFields, 'N');
							$arResult['GOOD_SEND'] = 'Y';
						}
					} else {
						$arResult["LAST_ERROR"] = GetMessage("MSG_EMPTY_EMAIL_TO");
					}
				}
			} else {
				$arResult["LAST_ERROR"] = GetMessage("MSG_OLD_SESS");
			}
		} else {
			$arResult["LAST_ERROR"] = GetMessage("MSG_NO_EVENT_TYPE");
		}
	} else {
		$arResult["LAST_ERROR"] = GetMessage("MSG_NO_CLASSES_OR_EVENT_TYPE");
	}
	foreach($arResult["FIELDS"] as $key => $arField) {
		// set request
		$arResult["FIELDS"][$key]["HTML_VALUE"] = $_REQUEST[$arField["CONTROL_NAME"]];
	}
}

$this->IncludeComponentTemplate();