<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Компонент мини-подписки");
?>
<br />
<?$APPLICATION->IncludeComponent("rarus.sms4b:subscribe.minisub", ".default", array(
	"PAGE_POST" => "#SITE_DIR#sms4b_demo/subscr_edit.php",
	"PAGE_SMS" => "#SITE_DIR#sms4b_demo/subscr_edit_sms.php",
	"SHOW_POST_SUB" => "Y",
	"SHOW_RSS_SUB" => "Y",
	"URL_FOR_FEEDBURNER" => "http://feeds.feedburner.com/",
	"FEED_NAME" => "retaildotru",
	"SHOW_SMS_SUB" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>