<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форма для рассылки");
?>

<?$APPLICATION->IncludeComponent("rarus.sms4b:sendSmsPublic", ".default", array(
	"ALLOW_SEND_ANY_NUM" => "N",
	"SET_TITLE" => "Y"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>