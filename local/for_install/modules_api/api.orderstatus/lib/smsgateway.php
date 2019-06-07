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
class SmsGatewayTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_orderstatus_sms_gateway';
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
				'title'        => Loc::getMessage('AOS_SMSGW_ID'),
			),
			'ACTIVE'      => array(
				'data_type' => 'boolean',
				'values'    => array('N', 'Y'),
				'title'     => Loc::getMessage('AOS_SMSGW_ACTIVE'),
			),
			'NAME'        => array(
				'data_type' => 'string',
				'required'  => true,
				'title'     => Loc::getMessage('AOS_SMSGW_NAME'),
			),
			'SORT'        => array(
				'data_type'     => 'integer',
				'default_value' => 500,
				'format'        => '/^[0-9]{1,11}$/',
				'title'         => Loc::getMessage('AOS_SMSGW_SORT'),
			),
			'PARAMS'       => array(
				'data_type' => 'text',
				'required'  => true,
				'title'     => Loc::getMessage('AOS_SMSGW_PARAMS'),
			),
			'DATE_MODIFY' => array(
				'data_type' => 'datetime',
				'title'     => Loc::getMessage('AOS_SMSGW_DATE_MODIFY'),
			),
			'MODIFIED_BY' => array(
				'data_type' => 'integer',
				'title'     => Loc::getMessage('AOS_SMSGW_MODIFIED_BY'),
			),
		);
	}
}