<?php
namespace Bitrix\KdaExportexcel;

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
 * @package Bitrix\KdaExportexcel
 **/

class ProfileTable extends Entity\DataManager
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
		return 'b_kdaexportexcel_profile';
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
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_ID_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_ACTIVE_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_NAME_FIELD'),
			),
			'PARAMS' => array(
				'data_type' => 'text',
				'default_value' => '',
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_PARAMS_FIELD'),
			),
			'DATE_START' => array(
				'data_type' => 'datetime',
				'default_value' => '',
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_DATE_START_FIELD'),
			),
			'DATE_FINISH' => array(
				'data_type' => 'datetime',
				'default_value' => '',
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_DATE_FINISH_FIELD'),
			),
			'SORT' => array(
				'data_type' => 'integer',
				'default_value' => 500,
				'title' => Loc::getMessage('KDA_EE_PROFILE_ENTITY_SORT_FIELD'),
			)
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
}