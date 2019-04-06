<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
$APPLICATION->IncludeComponent("bitrix:sale.order.payment.receive", "", array(
	"PAY_SYSTEM_ID" => intval($_REQUEST['ps']),
	"PERSON_TYPE_ID" => intval($_REQUEST['pt'])
	),
	false
);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
