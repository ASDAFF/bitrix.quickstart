<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?$APPLICATION->IncludeComponent("bitrix:sale.order.payment.receive", "", array("PAY_SYSTEM_ID" => "8","PERSON_TYPE_ID" => "1"),false);?>

