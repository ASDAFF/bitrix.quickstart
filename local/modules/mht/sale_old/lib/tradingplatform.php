<?php
namespace Bitrix\Sale;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class TradingPlatformTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> CODE string(20) mandatory
 * <li> NAME string(50) mandatory
 * <li> DESCRIPTION string(255) mandatory
 * <li> SETTINGS string mandatory
 * </ul>
 *
 * @package Bitrix\Sale
 **/

class TradingPlatformTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'b_sale_trading_platform';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_ID_FIELD'),
			),
			'CODE' => array(
				'required' => true,
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_CODE_FIELD'),
			),
			'ACTIVE' => array(
				'required' => true,
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateActive'),
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_ACTIVE_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_NAME_FIELD'),
			),
			'DESCRIPTION' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateDescription'),
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_DESCRIPTION_FIELD'),
			),
			'SETTINGS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('TRADING_PLATFORMS_ENTITY_SETTINGS_FIELD'),
			),
		);
	}
	public static function validateCode()
	{
		return array(
			new Entity\Validator\Length(null, 20),
		);
	}
	public static function validateActive()
	{
		return array(
			new Entity\Validator\Length(null, 1),
		);
	}
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 50),
		);
	}
	public static function validateDescription()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
}