<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

CModule::IncludeModule('sale');

$APPLICATION->SetPageProperty("title", "Информация о заказе");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Оплата заказа");

$orderID = $_REQUEST['OrderId'];
$order = CSaleOrder::GetByID($orderID);

if (!$order) {

	$arFilter = array(
		"ACCOUNT_NUMBER" => $orderID,
	);
	$accountNumberList = CSaleOrder::GetList(array("ACCOUNT_NUMBER" => "ASC"), $arFilter);
	$order = $accountNumberList->arResult[0];
}

if($order){
	$statusPageURL = sprintf('%s?ID=%s', GetPagePath('personal/order'), $orderID);
}

$status = $_REQUEST['Success'] == 'true' ? 'успешно' : 'не успешно';

$arOrder = CSaleOrder::GetByID($orderID);

if (!$arOrder){
	$arFilter = array(
		"ACCOUNT_NUMBER" => $orderID,
	);
	$accountNumberList = CSaleOrder::GetList(array("ACCOUNT_NUMBER" => "ASC"), $arFilter);
	$arOrder = $accountNumberList->arResult[0];
}
?>



<?php if (!$arOrder): ?>
	Заказ с номером <?php echo $orderID ?> не найден
<?php else: ?>
	Заказ с номером <?php echo $orderID ?> оплачен <?php echo $status; ?><br/>
	Состояние заказа можно узнать на <a href="<?php echo $statusPageURL; ?>">странице заказа</a>
<?php endif; ?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>