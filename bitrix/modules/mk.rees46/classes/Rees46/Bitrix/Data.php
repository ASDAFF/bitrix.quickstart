<?php

namespace Rees46\Bitrix;

use Rees46\Options;

\CModule::IncludeModule('iblock');
\CModule::IncludeModule('catalog');
\CModule::IncludeModule('sale');

class Data
{
	private static $itemArraysCache = array();
	private static $itemArraysMoreCache = array();

	/**
	 * get orders for the last 6 months for export
	 *
	 * @return bool|\CDBResult
	 */
	public static function getLatestOrders()
	{
		$libOrder = new \CSaleOrder;

		$orders = $libOrder->GetList(array(), array(
			'DATE_INSERT_FROM' => date('Y-m-d', strtotime('-6 months')),
			'STATUS' => 'F',
		));

		return $orders;
	}

	/**
	 * get item data for order or current cart
	 *
	 * @param int $order_id send null for current cart
	 * @param bool $item_more_data
	 * @return array
	 */
	public static function getOrderItems($order_id = null, $item_more_data = false)
	{
		$items = array();

		$libBasket = new \CSaleBasket();

		if ($order_id !== null) {
			$list = $libBasket->GetList(array(), array('ORDER_ID' => $order_id));
		} else {
			$list = $libBasket->GetList(array(),
				array(
					'FUSER_ID' => $libBasket->GetBasketUserID(),
					'LID' => SITE_ID,
					'ORDER_ID' => false,
				)
			);
		}

		while ($item = $list->Fetch()) {
			$itemData = self::getItemArray($item['PRODUCT_ID'], $item_more_data);
			$item['PRODUCT_ID'] = $itemData['item_id']; // fix ID for complex items
			$item['DATA'] = $itemData;
			$items []= $item;
		}

		return $items;
	}

	/**
	 * get item params for view push
	 *
	 * @param int $id
	 * @param bool $more
	 * @return array
	 */
	public static function getItemArray($id, $more = false)
	{
		if (isset(self::$itemArraysMoreCache[$id])) {
			return self::$itemArraysMoreCache[$id];
		}

		if (isset(self::$itemArraysCache[$id]) && !$more) {
			return self::$itemArraysCache[$id];
		}

		$libProduct    = new \CCatalogProduct();
		$libIBlockElem = new \CIBlockElement();
		$libPrice      = new \CPrice();

		$item = $libProduct->GetByID($id);

		// maybe we have complex item, let's find its first child entry
		if ($item === false) {
			$list = $libIBlockElem->GetList(
				array(
					'ID' => 'ASC',
				),
				array(
					'PROPERTY_CML2_LINK' => $id,
				));

			if ($itemBlock = $list->Fetch()) {
				$item = $libProduct->GetByID($itemBlock['ID']);
			} else {
				return null; // c'est la vie
			}
			// now $item points to the earliest child
		} else { // we have simple item or child
			$itemBlock = $libIBlockElem->GetByID($id)->Fetch();

			$itemFull = $libProduct->GetByIDEx($id);

			if (!empty($itemFull['PROPERTIES']['CML2_LINK']['VALUE'])) {
				$id = $itemFull['PROPERTIES']['CML2_LINK']['VALUE'];
			} // set id of the parent if we have child
		}

		$return = array(
			'item_id' => intval($id),
		);

		if (empty($item)) {
			return null;
		}

		$price = $libPrice->GetBasePrice($itemBlock['ID']);

		if (!empty($itemBlock['IBLOCK_SECTION_ID'])) {
			$return['category'] = $itemBlock['IBLOCK_SECTION_ID'];

		} else {
			$parentItemBlock = $libIBlockElem->GetByID($id)->Fetch();

			if (!empty($parentItemBlock['IBLOCK_SECTION_ID'])) {
				$return['category'] = $parentItemBlock['IBLOCK_SECTION_ID'];
			}
		}

		$has_price = false;
		if (!empty($price['PRICE'])) {
			$return['price'] = $price['PRICE'];
			$has_price = true;
		}

		if (isset($item['QUANTITY'])) {
			$quantity = $item['QUANTITY'] > 0;
			$return['is_available'] = ($quantity && $has_price) ? 1 : 0;
		}

		if (Options::getRecommendNonAvailable()) {
			$return['is_available'] = 1;
		}

		if ($more) {
			$libMain = new \CMain;
			$libFile = new \CFile();

			$itemFull = $libProduct->GetByIDEx($id);

			$host = ($libMain->IsHTTPS() ? 'https://' : 'http://') . SITE_SERVER_NAME;

			$return['name'] = $itemFull['NAME'];
			$return['url'] = $host . $itemFull['DETAIL_PAGE_URL'];

			$picture = $itemFull['DETAIL_PICTURE'] ?: $itemFull['PREVIEW_PICTURE'];

			if ($picture) {
				$return['image_url'] = $host . $libFile->GetPath($picture);
			}

			self::$itemArraysMoreCache[$id] = $return;
		} else {
			self::$itemArraysCache[$id] = $return;
		}

		return $return;
	}

	/**
	 * get item params for view or cart push from basket id
	 *
	 * @param $id
	 * @return array|bool
	 */
	public static function getBasketArray($id)
	{
		$libBasket = new \CSaleBasket();
		$item = $libBasket->GetByID($id);

		return Data::getItemArray($item['PRODUCT_ID']);
	}
}
