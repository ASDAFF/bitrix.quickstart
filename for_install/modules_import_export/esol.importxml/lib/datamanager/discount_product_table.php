<?php
namespace Bitrix\EsolImportxml\DataManager;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ProfileTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> PARAMS string optional
 * <li> DATE_START datetime optional
 * <li> SORT int optional default 500
 * </ul>
 *
 * @package Bitrix\EsolImportxml
 **/

class DiscountProductTable extends Entity\DataManager
{
	/**
	 * Returns path to the file which contains definition of the class.
	 *
	 * @return string
	 */
	public static function getFilePath()
	{
		return __FILE__;
	}

	/**
	 * Returns DB table name for entity
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_kdaimportexcel_discount_product';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('ESOL_IX_DP_ENTITY_ID_FIELD'),
			),
			'DISCOUNT_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('ESOL_IX_DP_ENTITY_DISCOUNT_ID_FIELD'),
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('ESOL_IX_DP_ENTITY_PRODUCT_ID_FIELD'),
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('ESOL_IX_DP_ENTITY_SITE_ID_FIELD'),
			)
		);
	}
}