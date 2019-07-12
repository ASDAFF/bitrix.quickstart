<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?><?$APPLICATION->IncludeComponent(
	"mlife:asz.basket.full", 
	"", 
	array(
		"FINDUSER" => "Y",
		"FINDEMAIL" => "N",
		"NOEMAIL" => "USER",
		"LOGIN" => "PREFIX",
		"LOGIN_PREFIX" => "user_",
		"PROP_NAME" => "FIO",
		"PROP_EMAIL" => "EMAIL",
		"PROP_LOCATION" => "REGION",
		"GROUP_ADMIN" => array(
			0 => "1",
		),
		"NOEMAIL_USER" => "1",
		"FINDEMAIL_NOAUT" => "Y",
		"ORDERPRIV" => "Y",
		"ORDERPRIV_USERID" => "1",
		"ORDERPRIV_GROUP" => array(
		),
		"GROUP_ADDUSER" => array(
		),
		"QUANT" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>