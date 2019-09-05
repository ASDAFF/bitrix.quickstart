<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$_REQUEST["ORDER_ID"] = $arParams["ORDER_ID"];?>

<?$APPLICATION->IncludeComponent(
	"site:sale.order.payment",
	"",
	Array(
	),
false
);?>
<?//exit();?>