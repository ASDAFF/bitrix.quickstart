<?php

namespace Rees46\Service;

use Rees46\Bitrix\Data;
use Rees46\Functions;
use Rees46\Options;

class Export
{
	const STATUS_NOT_PERFORMED  = 0;
	const STATUS_FAIL           = 1;
	const STATUS_SUCCESS        = 2;

	/**
	 * @return bool|int false on error, count of the orders on success (can be 0)
	 */
	public static function exportOrders()
	{
		set_time_limit(0);

		$data = array(
			'shop_id' => Options::getShopID(),
			'shop_secret' => Options::getShopSecret(),
			'orders' => self::getOrdersForExport(),
		);

		if (count($data['orders']) > 0) {
			self::sendData($data);
		}

		return count($data['orders']);
	}

	private static function getOrdersForExport()
	{
		$dbOrders = Data::getLatestOrders();

		$orders = array();

		while ($dbOrder = $dbOrders->Fetch()) {
			$order = array(
				'id' => $dbOrder['ID'],
				'date' => strtotime($dbOrder['DATE_INSERT']),
			);

			if (!empty($dbOrder['USER_ID'])) {
				$order['user_id'] = $dbOrder['USER_ID'];
			}

			$dbItems = Data::getOrderItems($dbOrder['ID']);

			$items = array();

			foreach ($dbItems as $dbItem) {
				$item = $dbItem['DATA'];
				$item['amount'] = $dbItem['QUANTITY'];
				$item['id'] = strval($item['item_id']);
				unset($item['item_id']);

				$items []= $item;
			}

			$order['items'] = $items;

			$orders []= $order;
		}

		return $orders;
	}

	private static function sendData($data)
	{
		$pest = new \PestJSON(Functions::BASE_URL);

		try {
			$pest->post('/import/orders.json', $data);
		} catch (\Pest_Json_Decode $e) {
			// can be safely ignored
		}
	}
}
