<?php

namespace Rees46;

use Rees46\Bitrix\Data;

class Events
{
	/**
	 * push view event
	 *
	 * @param $item_id
	 */
	public static function view($item_id)
	{
		$item = Data::getItemArray($item_id, true);

		Functions::jsPushData('view', $item);
	}

	/**
	 * push add to cart event
	 *
	 * @see install/index.php
	 * @param $basket_id
	 */
	public static function cart($basket_id)
	{
		$item = Data::getBasketArray($basket_id);
		Functions::cookiePushData('cart', $item);
	}

	/**
	 * push remove from cart event
	 *
	 * @see install/index.php
	 * @param $basket_id
	 */
	public static function removeFromCart($basket_id)
	{
		$item = Data::getBasketArray($basket_id);
		Functions::cookiePushData('remove_from_cart', $item);
	}

	/**
	 * callback for purchase event
	 *
	 * @see install/index.php
	 * @param $order_id
	 */
	public static function purchase($order_id)
	{
		$items = array();

		foreach (Data::getOrderItems($order_id) as $item) {
			$items []= array(
				'item_id' => $item['PRODUCT_ID'],
				'amount'  => $item['QUANTITY']
			);
		}

		Functions::cookiePushPurchase($items, $order_id);
	}
} 
