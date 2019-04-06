<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование подписки");
?><?$APPLICATION->IncludeComponent("rarus.sms4b:subscribe.edit", ".default", Array(
	"SHOW_HIDDEN"	=>	"Y",
	"SHOW_POST_FORM"	=>	"N",
	"SHOW_SMS_FORM"	=>	"Y",
	"SHOW_RUBS"	=>	array(
	),
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"ALLOW_ANONYMOUS"	=>	"Y",
	"SHOW_AUTH_LINKS"	=>	"Y",
	"TEMPLATE_ID"	=>	"",
	"SET_TITLE"	=>	"Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>