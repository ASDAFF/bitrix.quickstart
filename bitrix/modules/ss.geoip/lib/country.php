<?php
namespace Ss\Geoip;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class CountryTable
 *
 * Fields:
 * <ul>
 * <li> ID string(2) mandatory
 * <li> SHORT_NAME string(3) optional
 * <li> NAME string(50) optional
 * </ul>
 *
 * @package Bitrix\Geoip
 **/

class CountryTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ss_geoip_country';
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
				'data_type' => 'string',
				'primary' => true,
				'validation' => array(__CLASS__, 'validateId'),
				'title' => Loc::getMessage('COUNTRY_ENTITY_ID_FIELD'),
			),
			'SHORT_NAME' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateShortName'),
				'title' => Loc::getMessage('COUNTRY_ENTITY_SHORT_NAME_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('COUNTRY_ENTITY_NAME_FIELD'),
			),
			'RU_NAME' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateRuName'),
				'title' => Loc::getMessage('COUNTRY_ENTITY_RU_NAME_FIELD'),
			),
		);
	}
	/**
	 * Returns validators for ID field.
	 *
	 * @return array
	 */
	public static function validateId()
	{
		return array(
			new Entity\Validator\Length(null, 2),
		);
	}
	/**
	 * Returns validators for SHORT_NAME field.
	 *
	 * @return array
	 */
	public static function validateShortName()
	{
		return array(
			new Entity\Validator\Length(null, 3),
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
			new Entity\Validator\Length(null, 50),
		);
	}
	/**
	 * Returns validators for RU_NAME field.
	 *
	 * @return array
	 */
	public static function validateRuName()
	{
		return array(
				new Entity\Validator\Length(null, 50),
		);
	}
}
?>