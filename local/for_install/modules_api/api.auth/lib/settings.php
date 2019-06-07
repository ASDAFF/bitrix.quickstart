<?php

namespace Api\Auth;

use \Bitrix\Main,
	 \Bitrix\Main\SiteTable,
	 \Bitrix\Main\Application,
	 \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SettingsTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_auth_settings';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			 new Main\Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true,
			 )),
			 new Main\Entity\StringField('NAME'),
			 new Main\Entity\TextField('VALUE'),
		);
	}

	public static function setOption($option = array())
	{
		if(!$option['NAME'])
			return false;

		$arExistOption = self::getList(array(
			 'select' => array('ID'),
			 'filter' => array('=NAME' => $option['NAME']),
		))->fetch();

		if($arExistOption['ID'])
			self::update($arExistOption['ID'], $option);
		else
			$arExistOption['ID'] = self::add($option);

		return true;
	}

	public static function getAll()
	{
		static $result = array();

		if(empty($result)) {
			$rsSettings = parent::getList();
			while($row = $rsSettings->fetch()) {
				$key   = $row['NAME'];
				$value = $row['VALUE'];

				if(Tools::isSerialize($row['VALUE'])) {
					$value = unserialize($row['VALUE']);
				}
				/*elseif(is_string($value)) {
					$value = htmlspecialcharsbx($value);
				}*/

				$result[ $key ] = $value;
			}
			unset($key, $value, $row);
		}

		return $result;
	}
}