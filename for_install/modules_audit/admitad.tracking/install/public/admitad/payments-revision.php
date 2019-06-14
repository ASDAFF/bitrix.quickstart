<?php

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('main');
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('admitad.tracking');

$username = \Bitrix\Main\Config\Option::get('admitad.tracking', 'REVISION_LOGIN');
$secret = \Bitrix\Main\Config\Option::get('admitad.tracking', 'REVISION_PASSWORD');

if (!isset($_SERVER['PHP_AUTH_USER']) || isset($_SERVER['PHP_AUTH_PW']) && ($_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $secret)) {
	header('WWW-Authenticate: Basic realm="Developer zone"');
	header('HTTP/1.0 401 Unauthorized');
	die;
};

$uidProps = CSaleOrderPropsValue::GetList(array(), array('CODE' => \Admitad\Tracking\Admitad\AdmitadOrder::ORDER_PROP_CODE));

$query = \Bitrix\Sale\Internals\OrderTable::getList(array(
	'order' => array('ID' => 'DESC'),
	'filter' => array()
));

header('Content-type:text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
?>
	<Payments xmlns="http://admitad.com/payments-revision" >
		<?php
		while ($uidProp = $uidProps->Fetch()) {
			$order = \Bitrix\Sale\Internals\OrderTable::getList(array(
				'filter' => array('ID' => $uidProp['ORDER_ID'])
			))->fetch();

			$props = CSaleOrderProps::GetList(array(), array('CODE' => 'admitad_uid', 'ORDER_ID' => $order['ID']))->fetch();
			$price = $order['PRICE'];
			$comments = $order['COMMENTS'];

			$status = getActionStatus($order['STATUS_ID']);
			$statuses = \Bitrix\Sale\OrderStatus::getAllStatuses();

			$uid = $uidProp['VALUE'];
			if (!$status || !$uid) {
				continue;
			}

			?>
			<Payment>
				<OrderID><?=$order['ID']?></OrderID>
				<Status><?=$status?></Status>
			</Payment>
			<?php
		}
		?>
	</Payments>
<?php

function getActionStatus($orderStatus)
{
	$bitrixStatusApproved = \Bitrix\Main\Config\Option::get('admitad.tracking', 'REVISION_STATUS_APPROVED');
	$bitrixStatusDeclined = \Bitrix\Main\Config\Option::get('admitad.tracking', 'REVISION_STATUS_DECLINED');

	if ($orderStatus == $bitrixStatusDeclined) {
		return 2;
	}

	if ($orderStatus == $bitrixStatusApproved) {
		return 1;
	}

	return 0;
}