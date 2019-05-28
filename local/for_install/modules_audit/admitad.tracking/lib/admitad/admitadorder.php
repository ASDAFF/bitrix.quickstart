<?php

namespace Admitad\Tracking\Admitad;

use Admitad\Api\Exception\Exception;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\OrderPropsValueTable;
use Bitrix\Sale\Internals\OrderTable;
use Bitrix\Sale\Internals\PersonTypeTable;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

Loc::loadMessages(__FILE__);

class AdmitadOrder
{
	const ORDER_PROP_CODE = 'aid';
	const USER_PROP_CODE = 'UF_AID_FIELD';
	const USER_PROP_LIFE_TIME_CODE = 'UF_AID_LIFE_FIELD';

	const PAYMENT_TYPE_SALE = 'sale';
	const ORDER_TYPE_FIRST_ORDER = 1;
	const ORDER_TYPE_SECOND_ORDER = 2;

	/**
	 * @return bool|null|string
	 */
	public static function getSecondOrderEnabled()
	{
		return Option::get(Admitad::MODULE_ID, 'SECOND_ORDER_ENABLED', false);
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 */
	public function setSecondOrderEnabled($value)
	{
		Option::set(Admitad::MODULE_ID, 'SECOND_ORDER_ENABLED', $value);

		return $this;
	}

	public static function enableSecondOrder()
	{
		Option::set(Admitad::MODULE_ID, 'SECOND_ORDER_ENABLED', 'Y');
	}

	public static function disableSecondOrder()
	{
		Option::set(Admitad::MODULE_ID, 'SECOND_ORDER_ENABLED', false);
	}

	/**
	 * @return bool|null|string
	 */
	public static function getStrongCheck()
	{
		return Option::get(Admitad::MODULE_ID, 'ADMITAD_STRONG_CHECK', 'Y');
	}

	public static function enableStrongCheck()
	{
		Option::set(Admitad::MODULE_ID, 'ADMITAD_STRONG_CHECK', 'Y');
	}

	public static function disableStrongCheck()
	{
		Option::set(Admitad::MODULE_ID, 'ADMITAD_STRONG_CHECK', false);
	}

	/**
	 * Get admitad uid
	 *
	 * @param Order|null $order
	 *
	 * @return bool|null|string
	 */
	public static function getUid($order = null)
	{
		global $USER;

		// ≈сли у заказа уже есть uid возвращаем его
		if ($order && ($propUid = static::getOrderUidProperty($order))) {
			$orderId = $order->getId();
			if ($prop = OrderPropsValueTable::getList(array(
				"filter" => array(
					"ORDER_PROPS_ID" => $propUid,
					"ORDER_ID"       => $orderId,
				),
			))->fetch()
			) {
				return $prop["VALUE"];
			}
		}

		$request = Application::getInstance()->getContext()->getRequest();
		if ($request->getCookieRaw(Admitad::ADMITAD_COOKIE_KEY)) {
			return $request->getCookieRaw(Admitad::ADMITAD_COOKIE_KEY);
		}

		if ($USER && $USER->GetID()) {
			$rsUser = \CUser::GetByID($USER->GetID());
			$arUser = $rsUser->Fetch();
			if (isset($arUser[static::USER_PROP_CODE])
				&& isset($arUser[static::USER_PROP_LIFE_TIME_CODE])
				&& (time() < $arUser[static::USER_PROP_LIFE_TIME_CODE])
			) {
				return $arUser[static::USER_PROP_CODE];
			}
		}

		return false;
	}

	/**
	 * Set admitad property to order
	 *
	 * @param Order $order
	 * @param       $value
	 */
	public static function setOrderUid($order, $value)
	{
		if (!$propUid = static::getOrderUidProperty($order)) {
			return;
		}

		$orderId = $order->getId();

		if ($prop = OrderPropsValueTable::getList(array(
			"filter" => array(
				"ORDER_PROPS_ID" => $propUid,
				"ORDER_ID"       => $orderId,
			),
		))->fetch()
		) {
			OrderPropsValueTable::Update($prop['ID'], array('VALUE' => $value));

			return;
		}

		OrderPropsValueTable::add(array(
			"ORDER_ID"       => $orderId,
			"ORDER_PROPS_ID" => $propUid,
			"NAME"           => static::ORDER_PROP_CODE,
			"CODE"           => static::ORDER_PROP_CODE,
			"VALUE"          => $value,
		));
	}

	/**
	 * Add admitad property ID to order property table
	 *
	 * @return bool
	 */
	public static function addOrderUidProperty()
	{
		if (static::getOrderUidProperty()) {
			return true;
		}

		if (!$personType = PersonTypeTable::getList()->fetch()) {
			return false;
		}

		foreach (PersonTypeTable::getList()->fetchAll() as $personType) {
			$fields = array(
				"PERSON_TYPE_ID" => $personType['ID'],
				"PROPS_GROUP_ID" => 1,
				"DEFAULT_VALUE"  => '',
				"NAME"           => static::ORDER_PROP_CODE,
				"TYPE"           => "STRING",
				"REQUIRED"       => "N",
				"SORT"           => 100,
				"CODE"           => static::ORDER_PROP_CODE,
				"UTIL"           => "Y",
			);

			OrderPropsTable::Add($fields);
		}

		return true;
	}

	/**
	 * Get admitad property ID
	 *
	 * @param Order|null $order
	 *
	 * @return bool|integer
	 */
	public static function getOrderUidProperty($order = null)
	{
		Loader::includeModule('sale');
		$filter = array(
			'CODE' => static::ORDER_PROP_CODE,
		);
		if ($order and $order->getPersonTypeId()) {
			$filter['PERSON_TYPE_ID'] = $order->getPersonTypeId();
		}

		if ($prop = OrderPropsTable::getList(array(
				'filter' => $filter,
			)
		)->fetch()
		) {
			return $prop['ID'];
		}

		return false;
	}

	public static function getUserUidLifeTimeProperty()
	{
		if ($prop = \CUserTypeEntity::GetList(array(), array(
			'FIELD_NAME' => static::USER_PROP_LIFE_TIME_CODE,
		))->Fetch()
		) {
			return $prop['ID'];
		}

		return false;
	}

	/**
	 * Add user admitad id life time property
	 *
	 * @return bool|int
	 */
	public static function addUserUidLifeTimeProperty()
	{
		if (static::getUserUidLifeTimeProperty()) {
			return true;
		}

		$oUserTypeEntity = new \CUserTypeEntity();

		$aUserFields = array(
			'ENTITY_ID'         => 'USER',
			'FIELD_NAME'        => static::USER_PROP_LIFE_TIME_CODE,
			'USER_TYPE_ID'      => 'string',
			'XML_ID'            => 'XML_ID_' . static::USER_PROP_LIFE_TIME_CODE,
			'MULTIPLE'          => 'N',
			'MANDATORY'         => 'N',
			'SHOW_FILTER'       => 'N',
			'SHOW_IN_LIST'      => '',
			'EDIT_IN_LIST'      => '',
			'IS_SEARCHABLE'     => 'N',
			'SETTINGS'          => array(
				'DEFAULT_VALUE' => '',
				'SIZE'          => '20',
				'ROWS'          => '1',
				'MIN_LENGTH'    => '0',
				'MAX_LENGTH'    => '0',
				'REGEXP'        => '',
			),
			'EDIT_FORM_LABEL'   => array(
				'ru' => 'Admitad UID Life Time',
				'en' => 'Admitad UID Life Time',
			),
			'LIST_COLUMN_LABEL' => array(
				'ru' => 'Admitad UID Life Time',
				'en' => 'Admitad UID Life Time',
			),
			'LIST_FILTER_LABEL' => array(
				'ru' => 'Admitad UID Life Time',
				'en' => 'Admitad UID Life Time',
			),
		);

		return $oUserTypeEntity->Add($aUserFields);
	}

	public static function getUserUidProperty()
	{
		if ($prop = \CUserTypeEntity::GetList(array(), array(
			'FIELD_NAME' => static::USER_PROP_CODE,
		))->Fetch()
		) {
			return $prop['ID'];
		}

		return false;
	}

	/**
	 * Add user admitad id property
	 *
	 * @return bool|int
	 */
	public static function addUserUidProperty()
	{
		if (static::getUserUidProperty()) {
			return true;
		}

		$oUserTypeEntity = new \CUserTypeEntity();

		$aUserFields = array(
			'ENTITY_ID'         => 'USER',
			'FIELD_NAME'        => static::USER_PROP_CODE,
			'USER_TYPE_ID'      => 'string',
			'XML_ID'            => 'XML_ID_' . static::USER_PROP_CODE,
			'MULTIPLE'          => 'N',
			'MANDATORY'         => 'N',
			'SHOW_FILTER'       => 'N',
			'SHOW_IN_LIST'      => '',
			'EDIT_IN_LIST'      => '',
			'IS_SEARCHABLE'     => 'N',
			'SETTINGS'          => array(
				'DEFAULT_VALUE' => '',
				'SIZE'          => '20',
				'ROWS'          => '1',
				'MIN_LENGTH'    => '0',
				'MAX_LENGTH'    => '0',
				'REGEXP'        => '',
			),
			'EDIT_FORM_LABEL'   => array(
				'ru' => 'Admitad UID',
				'en' => 'Admitad UID',
			),
			'LIST_COLUMN_LABEL' => array(
				'ru' => 'Admitad UID',
				'en' => 'Admitad UID',
			),
			'LIST_FILTER_LABEL' => array(
				'ru' => 'Admitad UID',
				'en' => 'Admitad UID',
			),
		);

		return $oUserTypeEntity->Add($aUserFields);
	}

	public static function getOrderTypes()
	{
		$types = array(
			static::ORDER_TYPE_FIRST_ORDER => Loc::getMessage('ADMITAD_TRACKING_ORDER_TYPE_FIRST_ORDER'),
		);

		if (static::getSecondOrderEnabled()) {
			$types[static::ORDER_TYPE_SECOND_ORDER] = Loc::getMessage('ADMITAD_TRACKING_ORDER_TYPE_SECOND_ORDER');
		}

		return $types;
	}

	public static function getOrderType($configuration)
	{
		$orderCount = static::getOrderCount();

		if (static::getSecondOrderEnabled() && $orderCount > 0 && $configuration[static::ORDER_TYPE_SECOND_ORDER]) {
			foreach ($configuration[static::ORDER_TYPE_SECOND_ORDER] as $actionCode => $actionData) {
				if ($actionData['type']) {
					return $configuration[static::ORDER_TYPE_SECOND_ORDER];
				}
			}
		}

		return $configuration[static::ORDER_TYPE_FIRST_ORDER];
	}

	public static function getIBlockSections()
	{
		Loader::includeModule("iblock");
		$items = array();
		$result = \CIBlockSection::GetTreeList(Array(), array("ID", "NAME", "DEPTH_LEVEL"));
		while ($item = $result->Fetch()) {
			$items[$item['ID']] = str_repeat(' . ', $item['DEPTH_LEVEL']) . $item['NAME'];
		}

		return $items;
	}

	public static function getOrderCount()
	{
		global $USER;

		if ($USER && $USER->GetID()) {
			return OrderTable::getCount(array(
				"USER_ID" => $USER->GetID(),
				"PAYED"   => "Y",
			));
		}

		return 0;
	}

	public static function getProductSections($id)
	{
		$query = \Bitrix\Iblock\SectionElementTable::getList(array(
			'select' => array('IBLOCK_SECTION_ID'),
			'filter' => array('=IBLOCK_ELEMENT_ID' => $id['ID']),
		));

		$result = array_map(function ($data) {
			return $data['IBLOCK_SECTION_ID'];
		}, $query->fetchAll());

		return $result;
	}

	public static function getTariffData($productId)
	{
		$configuration = \Bitrix\Main\Web\Json::decode(Admitad::getConfiguration());

		$id = \CCatalogSku::GetProductInfo($productId);
		$sections = self::getProductSections($id);
		$defaultData = array();
		$configuration = static::getOrderType($configuration);
		foreach ($configuration as $actionCode => $actionData) {
			if (!$actionData['type']) {
				continue;
			}

			foreach ($actionData['tariffs'] as $tariffCode => $data) {
				if (empty($defaultData) && !empty($data['categories'])) {
					$defaultData = array(
						'action_code' => $actionCode,
						'tariff_code' => $tariffCode,
					);
				}

				$tariffSections = array_values($data['categories']);

				if (array_intersect($sections, $tariffSections)) {
					return array(
						'action_code' => $actionCode,
						'tariff_code' => $tariffCode,
					);
				}
			}
		}

		return $defaultData;
	}

	public static function admitadPostback($campaignCode, $postbackKey, $orderId, array $positions, array $parameters = array(), $uid = null)
	{

		$positions = array_values($positions);

		if (!$uid) {
			return;
		}

		$defaults = array(
			'currency_code' => 'RUB',
			'payment_type'  => 'sale',
			'tariff_code'   => 1,
		);

		$global = array_merge(array(
			'campaign_code' => $campaignCode,
			'postback'      => true,
			'postback_key'  => $postbackKey,
			'response_type' => 'img',
			'action_code'   => '1',
		), $parameters);

		$admitadPositions = static::generateAdmitadPositions($uid, $orderId, $positions, array_merge($global, $defaults));

		foreach ($admitadPositions as $position) {
			$parts = array();
			foreach ($position as $key => $value) {
				array_push($parts, $key . '=' . $value);
			}

			$url = 'https://ad.admitad.com/r?' . implode('&', $parts);

			if (!function_exists('curl_init')) {
				file_get_contents($url);
				continue;
			}

			$cl = curl_init($url);

			curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);

			curl_exec($cl);
		}
	}

	public static function generateAdmitadPositions($uid, $orderId, array $positions, array $parameters = array())
	{
		$config = array_merge(array(
			'uid'            => $uid,
			'order_id'       => $orderId,
			'position_count' => count($positions),
		), $parameters);

		foreach ($positions as $index => &$position) {
			$position = array_merge($config, array(
				'position_id' => $index + 1,
			), $position);
		}

		return $positions;
	}
}