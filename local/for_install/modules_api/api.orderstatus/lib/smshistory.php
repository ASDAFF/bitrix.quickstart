<?php
namespace Api\OrderStatus;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class HistoryTable
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ID int mandatory
 * <li> YANDEX_EXPORT bool optional default 'N'
 * <li> SUBSCRIPTION bool optional default 'N'
 * <li> VAT_ID int optional
 * <li> PRODUCT_IBLOCK_ID int mandatory
 * <li> SKU_PROPERTY_ID int mandatory
 * </ul>
 *
 * @package Api\OrderStatus
 **/
class SmsHistoryTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_orderstatus_sms_history';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		/*
			boolean (наследует ScalarField)
			date (наследует ScalarField)
			datetime (наследует DateField)
			enum (наследует ScalarField)
			float (наследует ScalarField)
			integer (наследует ScalarField)
			string (наследует ScalarField)
			text (наследует StringField)
		 */
		return array(
			'ID'          => array(
				'data_type'    => 'integer',
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage('AOS_LSH_ID'),
			),
			'ORDER_ID'    => array(
				'data_type' => 'integer',
				'title'     => Loc::getMessage('AOS_LSH_ORDER_ID'),
			),
			'USER_ID'     => array(
				'data_type' => 'integer',
				'title'     => Loc::getMessage('AOS_LSH_USER_ID'),
			),
			'SITE_ID'     => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LSH_SITE_ID'),
			),
			'STATUS_ID'   => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LSH_STATUS_ID'),
			),
			'DATE_CREATE' => array(
				'data_type' => 'datetime',
				'title'     => Loc::getMessage('AOS_LSH_DATE_CREATE'),
			),
			'GATEWAY_ID'     => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LSH_GATEWAY_ID'),
			),
			'SMS_ID'     => array(
				'data_type' => 'text',
				'title'     => Loc::getMessage('AOS_LSH_SMS_ID'),
			),
			'SMS_TEXT'     => array(
				'data_type' => 'text',
				'title'     => Loc::getMessage('AOS_LSH_SMS_TEXT'),
			),
			'SMS_ERROR'       => array(
				'data_type' => 'text',
				'title'     => Loc::getMessage('AOS_LSH_SMS_ERROR'),
			),
		);
	}
}