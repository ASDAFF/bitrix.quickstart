<?
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
 * Модель кэша
 */
class Amazon
{
	/*Конфиг для подключения*/
	public static function getConfig(){
		return array(
			'region' => \COption::GetOptionString('site.main', 'amazon_region'),
			'version' => 'latest',
			'credentials' => array(
				'key'    => \COption::GetOptionString('site.main', 'amazon_key'),
				'secret' => \COption::GetOptionString('site.main', 'amazon_secret_key')
			)
		);
	}
}
