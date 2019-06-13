<?php

namespace Api\Mail;

use Bitrix\Main,
	 Bitrix\Main\SiteTable,
	 Bitrix\Main\Application,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SettingsTable extends Main\Entity\DataManager
{
	const CACHE_TTL = 31536000;

	public static function getTableName()
	{
		return 'api_mail_settings';
	}

	public static function getMap()
	{
		/**
		 * boolean (наследует ScalarField)
		 * date (наследует ScalarField)
		 * datetime (наследует DateField)
		 * enum (наследует ScalarField)
		 * float (наследует ScalarField)
		 * integer (наследует ScalarField)
		 * string (наследует ScalarField)
		 * text (наследует StringField)
		 */
		return array(
			 new Main\Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true,
					//'title'        => Loc::getMessage('ASM_OPTION_ID'),
			 )),
			 new Main\Entity\StringField('NAME'),
			 new Main\Entity\TextField('VALUE'),
			 new Main\Entity\StringField('SITE_ID'),
		);
	}

	public static function addEx($option)
	{
		$option = (array)$option;
		if(!$option)
			return false;

		$arExistOption = self::getList(array(
			 'select' => array('ID'),
			 'filter' => array('=NAME' => $option['NAME'], '=SITE_ID' => $option['SITE_ID']),
		))->fetch();

		if($arExistOption['ID'])
			self::update($arExistOption['ID'], $option);
		else
			$arExistOption['ID'] = self::add($option);

		return $arExistOption['ID'];
	}

	protected static function getCacheId()
	{
		if(LICENSE_KEY == '' || LICENSE_KEY == 'DEMO')
			$cachId = \CMain::GetServerUniqID();
		else
			$cachId = LICENSE_KEY;

		$cachId = md5($cachId . self::getTableName());

		return $cachId;
	}

	public static function saveToFile($arFields = array(), $bClean = false)
	{
		$cache   = Application::getInstance()->getManagedCache();
		$cacheId = self::getCacheId();

		if($bClean)
			$cache->clean($cacheId);

		$cache->set($cacheId, $arFields);
	}

	public static function getFromFile()
	{
		static $settings;

		if(!isset($settings)) {
			$cache = Application::getInstance()->getManagedCache();

			$cacheId = self::getCacheId();
			if($cache->read(self::CACHE_TTL, $cacheId)) {
				$settings = $cache->get($cacheId);
			}
			else {
				$rsSettings = self::getList();
				while($row = $rsSettings->fetch()) {
					if($row['SITE_ID'])
						$settings[ $row['SITE_ID'] ][ $row['NAME'] ] = Tools::isSerialize($row['VALUE']) ? unserialize($row['VALUE']) : $row['VALUE'];
				}

				self::saveToFile($settings);
			}
		}

		return $settings;
	}

	public static function getSiteList()
	{

		static $siteList;

		if(!isset($siteList)) {
			//Для функции custom_mail вернет сайты
			$siteIterator = SiteTable::getList(array(
				 'select' => array('LID', 'SERVER_NAME'),
				 'filter' => array('=ACTIVE' => 'Y'),
			));
			while($site = $siteIterator->fetch()) {
				$siteList[ trim($site['SERVER_NAME']) ] = trim($site['LID']);
			}
		}

		return $siteList;
	}
}