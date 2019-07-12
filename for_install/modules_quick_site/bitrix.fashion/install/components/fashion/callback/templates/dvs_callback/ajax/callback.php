<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arEventFields = array(
	"NAME" => urldecode($_POST["user_name"]),
	"TELEPHONE" => urldecode($_POST["user_tel"]),
	"TIME_FROM" => urldecode($_POST["user_time_from"]),
	"TIME_TILL" => urldecode($_POST["user_time_till"]),
	"SUBJECT" => urldecode($_POST["user_subject"]),
	"EMAIL" => urldecode($_POST["user_email"])
);

CEvent::Send("PHONE_CALLBACK", SITE_ID, $arEventFields, "N");