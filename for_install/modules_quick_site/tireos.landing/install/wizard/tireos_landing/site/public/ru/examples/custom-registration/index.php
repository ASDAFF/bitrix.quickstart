<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Управляемая регистрация");
?><?$APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"",
	Array(
		"SEF_MODE" => "N", 
		"SHOW_FIELDS" => Array("NAME","SECOND_NAME","LAST_NAME","PERSONAL_PROFESSION","PERSONAL_WWW","PERSONAL_ICQ","PERSONAL_GENDER","PERSONAL_BIRTHDAY","PERSONAL_PHOTO"), 
		"REQUIRED_FIELDS" => Array("NAME","SECOND_NAME","LAST_NAME","PERSONAL_BIRTHDAY"), 
		"AUTH" => "Y", 
		"USE_BACKURL" => "Y", 
		"SUCCESS_PAGE" => "", 
		"SET_TITLE" => "Y", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "3600" 
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>