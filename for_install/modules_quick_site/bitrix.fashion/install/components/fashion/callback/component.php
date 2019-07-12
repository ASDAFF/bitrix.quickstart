<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if(strlen($arParams["EMAIL_TO"]) <= 0)
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

$arParams["HREF_TEXT"] = trim($arParams["HREF_TEXT"]);
if(strlen($arParams["HREF_TEXT"]) <= 0)
	$arParams["HREF_TEXT"] = GetMessage("MF_HREF_MESSAGE");

$arParams["HEAD_TEXT"] = trim($arParams["HEAD_TEXT"]);
if(strlen($arParams["HEAD_TEXT"]) <= 0)
	$arParams["HEAD_TEXT"] = GetMessage("MF_HEAD_MESSAGE");

if($_SERVER["REQUEST_METHOD"] == "POST") {

        $arEventFields = array(
            "NAME" => $_POST["user_name"],
            "TELEPHONE" => $_POST["user_tel"],
            "TIME_FROM" => $_POST["user_time_from"],
            "TIME_TILL" => $_POST["user_time_till"],
            "SUBJECT" => $_POST["user_subject"],
            "EMAIL" => $_POST["user_email"]
        );

        CEvent::Send("PHONE_CALLBACK", SITE_ID, $arEventFields, "N");
			
		$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
		$_SESSION["MF_TEL"] = htmlspecialcharsEx($_POST["user_tel"]);

		
	$arResult["AUTHOR_TEL"] = htmlspecialcharsEx($_POST["user_tel"]);
	$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
}

if($USER->IsAuthorized()) {
	$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($USER->GetFullName());
} else {
	if(strlen($_SESSION["MF_NAME"]) > 0)
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
	if(strlen($_SESSION["MF_TEL"]) > 0)
		$arResult["AUTHOR_TEL"] = htmlspecialcharsEx($_SESSION["MF_TEL"]);
}

$this->IncludeComponentTemplate();
?>