<?
/**
 *  module
 *
 * @category
 * @package		MVC
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

/**
 * Контроллер для городов
 *
 * @category
 * @package		MVC
 */

namespace Site\Main\Mvc\Controller;

use Bitrix\Main\Application;
use Site\Main as Main;
use Site\Main\Mvc as Mvc;

class Cities extends Prototype
{
	/**
	 * Возвращает массив названий городов для автокомплита
	 *
	 * @return array
	 */
	public function getCitiesArrayAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$arReq = Application::getInstance()->getContext()->getRequest();
		$docRoot = Application::getInstance()->getDocumentRoot();
		$arCities = array();

		// База городов хранится в csv - /includes/cities.csv
		if (($handle = fopen($docRoot . "/includes/cities.csv", "r")) !== FALSE) {
			$rowNum = 0;
			$cityNameCol = 0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$arCity = str_getcsv($data[0], ';');
				
				if( $rowNum == 0 ){
					$cityNameCol = array_search('NAME', $arCity);
				}
				if( $rowNum > 0 && count($arCities) <= 5 && preg_match('/([a-z]-)*' . $arReq['term'] . '([a-z]-)*/ui', $arCity[$cityNameCol]) ){
					$arCities[] = $arCity[$cityNameCol];
				}

				$rowNum++;
			}
			fclose($handle);
		}
		sort($arCities);

		return $arCities;
	}
}