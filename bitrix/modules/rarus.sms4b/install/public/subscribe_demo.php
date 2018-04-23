<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Компонент подписки");
?>
<h2>Почтовая подписка</h2>
<?$APPLICATION->IncludeComponent("rarus.sms4b:subscribe.index", ".default", Array(
	"SHOW_COUNT"	=>	"N",
	"SHOW_HIDDEN"	=>	"Y",
	"SHOW_POST_FORM"	=>	"Y",
	"SHOW_SMS_FORM"	=>	"N",
	"SHOW_RUBS"	=>	array(
	),
	"PAGE"	=>	"#SITE_DIR#sms4b_demo/subscr_edit.php",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"SET_TITLE"	=>	"Y"
	)
);?>
<hr />
<h2>SMS подписка</h2> 
<?$APPLICATION->IncludeComponent("rarus.sms4b:subscribe.index", ".default", Array(
	"SHOW_COUNT"	=>	"Y",
	"SHOW_HIDDEN"	=>	"Y",
	"SHOW_POST_FORM"	=>	"N",
	"SHOW_SMS_FORM"	=>	"Y",
	"SHOW_RUBS"	=>	array(
	),
	"PAGE"	=>	"#SITE_DIR#sms4b_demo/subscr_edit_sms.php",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"SET_TITLE"	=>	"N"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>