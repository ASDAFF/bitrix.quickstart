<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent("bitrix:sale.order.payment.receive","",Array("PAY_SYSTEM_ID" => "7","PERSON_TYPE_ID" => "1") );?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>