<?php
namespace Ss\Geoip;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class IpTable
 *
 * Fields:
 * <ul>
 * <li> START_IP int mandatory
 * <li> END_IP int mandatory
 * <li> COUNTRY_ID string(2) mandatory
 * <li> CITY_ID int mandatory
 * </ul>
 *
 * @package Bitrix\Geoip
 **/

class IpTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ss_geoip_ip';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'START_IP' => array(
				'data_type' => 'integer',
				'primary' => true,
				'title' => Loc::getMessage('IP_ENTITY_START_IP_FIELD'),
			),
			'END_IP' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IP_ENTITY_END_IP_FIELD'),
			),
			'COUNTRY_ID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCountryId'),
				'title' => Loc::getMessage('IP_ENTITY_COUNTRY_ID_FIELD'),
			),
			'CITY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('IP_ENTITY_CITY_ID_FIELD'),
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
}
?>