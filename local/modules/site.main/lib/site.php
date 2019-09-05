<?php
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main;

/**
 * Класс для работы с сайтами
 */
class Site
{
	/**
	 * Префикс констант для хранения доменов, соответствующих кодам сайтов
	 */
	const DOMAIN_CONSTANTS_PREFIX = '\DOMAIN_';
	
	/**
	 * Константы были определены
	 *
	 * @var boolean
	 */
	protected static $constantsDefined = false;
	
	/**
	 * Возвращает домен сайта по его ID
	 *
	 * @param string $id ID сайта
	 * @return string
	 */
	public static function getDomainByID($id)
	{
		if (!$id) {
			throw new Exception('Site ID is undefined');
		}
		
		self::defineConstants();
		
		$const = __NAMESPACE__ . self::DOMAIN_CONSTANTS_PREFIX . $id;
		if (!defined($const)) {
			throw new Exception('Site related id constant is undefined');
		}
		
		return constant($const);
	}
	
	/**
	 * Определяет константы соответствия доменов кодам сайта
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return void
	 */
	public static function defineConstants($cacheTime = 3600)
	{
		if (self::$constantsDefined) {
			return;
		}
		
		$cache = new Cache(__METHOD__, __CLASS__, $cacheTime);
		if ($cache->start()) {
			$sites = \Bitrix\Main\SiteDomainTable::query()
				->setSelect(array(
					'LID',
					'DOMAIN',
					'SITE_SERVER_NAME' => 'SITE.SERVER_NAME',
				))
				->setOrder(array(
					'SITE.SORT' => 'DESC',
				))
				->exec();
			$data = array();
			while ($site = $sites->fetch()) {
				$data[] = $site;
			}
			
			$cache->end($data);
		} else {
			$data = $cache->getVars();
		}
		
		foreach ($data as $site) {
			$const = __NAMESPACE__ . self::DOMAIN_CONSTANTS_PREFIX . $site['LID'];
			if (!defined($const)) {
				$value = trim($site['SITE_SERVER_NAME']);
				if (!$value) {
					$value = trim($site['DOMAIN']);
				}
				/**
				 * @ignore
				 */
				define($const, $value);
			}
		}
		
		self::$constantsDefined = true;
	}
}