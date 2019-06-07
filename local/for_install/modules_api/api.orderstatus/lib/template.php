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
class TemplateTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_orderstatus_tpl';
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
				'title'        => Loc::getMessage('AOS_LT_ID'),
			),
			'ACTIVE'      => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LT_ACTIVE'),
			),
			'NAME'      => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LT_NAME'),
			),
			'STATUS_ID'   => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LT_STATUS_ID'),
			),
			'DESCRIPTION' => array(
				'data_type' => 'text',
				'title'     => Loc::getMessage('AOS_LT_DESCRIPTION'),
			),
			'DESCRIPTION_TYPE' => array(
				'data_type' => 'string',
				'title'     => Loc::getMessage('AOS_LT_DESCRIPTION_TYPE'),
			),
			'DATE_MODIFY'  => array(
				'data_type' => 'datetime',
			   'title'     => Loc::getMessage('AOS_LT_DATE_MODIFY'),
			),
			'MODIFIED_BY'  => array(
				'data_type' => 'integer',
			   'title'     => Loc::getMessage('AOS_LT_MODIFIED_BY'),
			),
		);
	}

	/*public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer'
			),
			'SALE_PRODUCT' => array(
				'data_type' => 'Product',
				'reference' => array('=this.PRODUCT_ID' => 'ref.ID')
			),
			'AMOUNT' => array(
				'data_type' => 'float'
			),
			'STORE_ID' => array(
				'data_type' => 'integer',
			),
			'STORE' => array(
				'data_type' => 'Bitrix\Catalog\Store',
				'reference' => array('=this.STORE_ID' => 'ref.ID')
			)
		);

		return $fieldsMap;
	}*/
}