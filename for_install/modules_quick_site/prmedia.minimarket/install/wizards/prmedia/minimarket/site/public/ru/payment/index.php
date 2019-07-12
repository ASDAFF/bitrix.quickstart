<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? $APPLICATION->SetTitle("Оплата заказа"); ?>
<?

$APPLICATION->IncludeComponent(
        "bitrix:sale.order.payment", "", Array(
        )
);
?>