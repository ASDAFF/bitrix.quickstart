<?php

namespace Admitad\Tracking;

use Admitad\Tracking\Admitad\Admitad;
use Admitad\Tracking\Admitad\AdmitadOrder;
use Bitrix\Main\Application;
use Bitrix\Sale\Order;

class Event
{

	public static function OnBeforeProlog()
	{
		global $USER, $USER_FIELD_MANAGER;
		$request = Application::getInstance()->getContext()->getRequest();
		$lifeTime = 90 * 60 * 60 * 24 + time();
		if ($cookieParam = $request->get(Admitad::getParamName())) {
			setcookie(Admitad::ADMITAD_COOKIE_KEY, $cookieParam, $lifeTime, '/', SITE_SERVER_NAME);
		}

		if ($request->getCookieRaw(Admitad::ADMITAD_COOKIE_KEY)) {
			if ($USER && $USER->GetID() && $propID = AdmitadOrder::getUserUidProperty() && $propLifeTimeID = AdmitadOrder::getUserUidLifeTimeProperty()) {
				$USER_FIELD_MANAGER->Update('USER', $USER->GetID(), array(
					AdmitadOrder::USER_PROP_CODE           => $request->getCookieRaw(Admitad::ADMITAD_COOKIE_KEY),
					AdmitadOrder::USER_PROP_LIFE_TIME_CODE => $lifeTime,
				));
			}
		}
	}

	public static function OnSaleOrderSaved(\Bitrix\Main\Event $event)
	{
		global $USER;
		/** @var Order $order */
		$order = $event->getParameter("ENTITY");
		$isNew = $event->getParameter("IS_NEW");
		$uid = AdmitadOrder::getUid($order);
		if (AdmitadOrder::getStrongCheck()) {
			if (!$isNew || !$uid || $USER->IsAdmin()) {
				return;
			}
		} else {
			if (!$uid) {
				return;
			}
		}

		$campaignCode = Admitad::getCampaignCode();
		$postbackKey = Admitad::getPostbackKey();

		if (!$campaignCode || !$postbackKey) {
			return;
		}

		$orderId = $order->getId();

		AdmitadOrder::setOrderUid($order, $uid);

		$positions = array();

		/** @var \Bitrix\Sale\BasketItem $item */
		foreach ($order->getBasket()->getBasketItems() as $item) {
			$tariffData = AdmitadOrder::getTariffData($item->getProductId());
			if (!$tariffData) {
				continue;
			}

			array_push($positions, array_merge(array(
				'product_id' => $item->getProductId(),
				'price'      => $item->getPrice(),
				'quantity'   => $item->getQuantity(),
			), $tariffData));
		}

		if (!empty($positions)) {
			AdmitadOrder::admitadPostback($campaignCode, $postbackKey, $orderId, $positions, array(), $uid);
		}
	}
}