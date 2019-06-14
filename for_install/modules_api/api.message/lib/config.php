<?php
namespace Api\Message;

use \Bitrix\Main;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

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
class ConfigTable extends Main\Entity\DataManager
{
	protected static function getCacheDir($siteId = 's1')
	{
		return '/'. $siteId .'/api/message/';
	}

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_message_config';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID'      => new Main\Entity\IntegerField('ID', array(
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage('ASM_OPTION_ID'),
			)),
			'NAME'    => new Main\Entity\StringField('NAME', array(
				'title' => Loc::getMessage('ASM_OPTION_NAME'),
			)),
			'VALUE'   => new Main\Entity\TextField('VALUE', array(
				'title' => Loc::getMessage('ASM_OPTION_VALUE'),
			)),
			'SITE_ID' => new Main\Entity\StringField('SITE_ID', array(
				'title' => Loc::getMessage('ASM_OPTION_SITE_ID'),
			)),
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

	public static function getData($siteId)
	{
		if(!$siteId)
			return false;

		$cfg = array();

		$connection = Application::getConnection();
		$sqlHelper  = $connection->getSqlHelper();

		//---------- Cahe settings ----------//
		$cacheTime = 604800; //7 days
		$cacheId   = 'asm_config_'. $siteId;
		$cacheDir  = self::getCacheDir($siteId);

		$obCache = new \CPHPCache();
		if($obCache->InitCache($cacheTime, $cacheId, $cacheDir))
		{
			$cfg = $obCache->GetVars();
		}
		elseif($obCache->StartDataCache())
		{
			if(defined('BX_COMP_MANAGED_CACHE'))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache($cacheDir);
			}

			$strSql = "SELECT `NAME`, `VALUE`, `SITE_ID`
							FROM " . self::getTableName() . "
							WHERE `SITE_ID` = '" . $sqlHelper->forSql($siteId) . "'";

			$result = $connection->query($strSql);
			while($option = $result->fetch())
			{
				$cfg[ $option['NAME'] ] = $option['VALUE'];
			}

			if(defined('BX_COMP_MANAGED_CACHE'))
			{
				$CACHE_MANAGER->RegisterTag($cacheId);
				$CACHE_MANAGER->EndTagCache();
			}

			$obCache->EndDataCache($cfg);
		}

		return $cfg;
	}

	public static function clearCache()
	{
		$arSites = SiteTable::getList(array(
			'select' => array('LID'),
			'filter' => array('ACTIVE' => 'Y'),
		))->fetchAll();

		if($arSites)
		{
			foreach($arSites as $site)
			{
				BXClearCache(true, self::getCacheDir($site['LID']));
			}
		}
	}
}