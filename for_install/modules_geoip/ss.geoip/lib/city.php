<?php
namespace Ss\Geoip;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class CityTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> COUNTRY_ID string(2) mandatory
 * <li> REGION string(200) optional
 * <li> NAME string(255) optional
 * <li> XML_ID string(255) optional
 * </ul>
 *
 * @package Bitrix\Geoip
 **/

class CityTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ss_geoip_city';
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
				'title' => Loc::getMessage('CITY_ENTITY_ID_FIELD'),
			),
			'COUNTRY_ID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCountryId'),
				'title' => Loc::getMessage('CITY_ENTITY_COUNTRY_ID_FIELD'),
			),
			'REGION' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateRegion'),
				'title' => Loc::getMessage('CITY_ENTITY_REGION_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('CITY_ENTITY_NAME_FIELD'),
			),
			'XML_ID' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateXmlId'),
				'title' => Loc::getMessage('CITY_ENTITY_XML_ID_FIELD'),
			),
		);
	}
	/**
	 * Returns validators for COUNTRY_ID field.
	 *
	 * @return array
	 */
	public static function validateCountryId()
	{
		return array(
			new Entity\Validator\Length(null, 2),
		);
	}
	/**
	 * Returns validators for REGION field.
	 *
	 * @return array
	 */
	public static function validateRegion()
	{
		return array(
			new Entity\Validator\Length(null, 200),
		);
	}
	/**
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	/**
	 * Returns validators for XML_ID field.
	 *
	 * @return array
	 */
	public static function validateXmlId()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
}
?>