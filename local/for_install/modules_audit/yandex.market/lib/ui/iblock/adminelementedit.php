<?php

namespace Yandex\Market\Ui\Iblock;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class AdminElementEdit extends Market\Reference\Event\Regular
{
	const TABSET_ID = 'tab_yandex_market';
	const TAB_EXPORT_RESULT = 'export_result';

	public static function getHandlers()
	{
		return [
			[
				'module' => 'main',
				'event' => 'OnAdminIBlockElementEdit'
			]
		];
	}

	public static function OnAdminIBlockElementEdit($elementInfo)
	{
		return [
			'TABSET' => static::TABSET_ID,
		    'GetTabs' => [__CLASS__, 'getTabs'],
		    'ShowTab' => [__CLASS__, 'showTab']
		];
	}

	public static function getTabs($elementInfo)
	{
		$result = null;

		if (!empty($elementInfo['ID']) && static::hasExportOffer($elementInfo['ID']))
		{
			$result = [
				[
	                'DIV' => static::TAB_EXPORT_RESULT,
	                'SORT' => 1000,
	                'TAB' => Market\Config::getLang('UI_IBLOCK_ADMIN_ELEMENT_EDIT_TAB_EXPORT_RESULT'),
	                'TITLE' => Market\Config::getLang('UI_IBLOCK_ADMIN_ELEMENT_EDIT_TAB_EXPORT_RESULT_TITLE')
	            ]
			];
		}

		return $result;
	}

	public static function showTab($div, $elementInfo, $formData)
	{
		switch ($div)
		{
			case static::TAB_EXPORT_RESULT:
				static::showTabExportResult($elementInfo['ID'], true);
			break;
		}
	}

	public static function showTabExportResult($elementId, $isTab = false)
	{
		global $APPLICATION;

		if ($isTab) { echo '<tr><td>'; }

		$elementId = (int)$elementId;

		$APPLICATION->IncludeComponent('yandex.market:admin.grid.list', '', array(
			'GRID_ID' => 'YANDEX_MARKET_ELEMENT_TAB_EXPORT_RESULT',
			'ELEMENT_ID' => $elementId,
			'PROVIDER_TYPE' => 'ExportOffer',
			'DATA_CLASS_NAME' => Market\Export\Run\Storage\OfferTable::getClassName(),
			'USE_FILTER' => 'N',
			'SUBLIST' => 'Y',
			'SUBLIST_TARGET' => $isTab ? 'N' : 'Y',
			'AJAX_URL' => '/bitrix/admin/yamarket_element_export_result.php?dummy&id=' . $elementId, // dummy required for pager
	        'PAGE_TITLE' => Market\Config::getLang('UI_IBLOCK_ADMIN_ELEMENT_EDIT_TAB_EXPORT_RESULT_NAV_TITLE'),
			'LIST_FIELDS' => array(
				'SETUP',
				'ELEMENT_ID',
				'STATUS',
				'LOG'
			),
			'PRIMARY' => [
				'SETUP_ID',
				'ELEMENT_ID'
			],
			'ROW_ACTIONS' => array(
				'LOG' => array(
					'URL' => '/bitrix/admin/yamarket_log.php?find_offer_id=#ELEMENT_ID#&find_setup=#SETUP#&set_filter=Y&popup=Y',
					'TEXT' => Market\Config::getLang('UI_IBLOCK_ADMIN_ELEMENT_EDIT_TAB_EXPORT_RESULT_ROW_ACTION_LOG'),
					'WINDOW' => 'Y'
				),
				'XML_CONTENT' => array(
					'URL' => '/bitrix/admin/yamarket_xml_element.php?type=offer&id=#ELEMENT_ID#&setup=#SETUP#&popup=Y',
					'TEXT' => Market\Config::getLang('UI_IBLOCK_ADMIN_ELEMENT_EDIT_TAB_EXPORT_RESULT_ROW_XML_CONTENT'),
					'WINDOW' => 'Y'
				)
			)
		));

		if ($isTab) { echo '<tr><td>'; }
	}

	protected static function hasExportOffer($offerId)
	{
		$offerId = (int)$offerId;
		$result = false;

		$query = Market\Export\Run\Storage\OfferTable::getList([
			'filter' => [
				[
					'LOGIC' => 'OR',
					'=ELEMENT_ID' => $offerId,
					'=PARENT_ID' => $offerId
				]
			],
			'limit' => 1,
			'select' => [ 'ELEMENT_ID' ]
		]);

		if ($query->fetch())
		{
			$result = true;
		}

		return $result;
	}
}