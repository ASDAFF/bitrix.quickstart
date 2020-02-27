<?

use Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use Site\Main\Cache;
use Site\Main\Hlblock\Packets;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class UsersMap extends CBitrixComponent
{

	/**
	* подключает языковые файлы
	*/

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}   

	/**
	* Обработка входных параметров
	* 
	* @param mixed[] $arParams
	* @return mixed[] $arParams
	*/ 

	public function onPrepareComponentParams($arParams)
	{
		// время кэширования

		$arParams["CACHE_TIME"] = (int) $arParams["CACHE_TIME"];

		return $arParams;
	}



	/**
	* получение результатов
	* 
	* @return void
	*/

	protected function getResult()
	{
		$arResult = $arCoords = array();

		$arCountries = \GetCountryArray();
		$arPacketsStatuses = Packets::getInstance()->getPacketsStatuses();
		$arStatuses = Packets::getPStatusesValId();
		$arResult['PROGRAMS_COUNT'] = $arPacketsStatuses['STATUSES'][$arStatuses['Принят']];
		$docRoot = Application::getInstance()->getDocumentRoot();

		/*Получаем пользователей*/
		$rsUsers = \CUser::GetList(($by = 'ID'), ($order = 'ASC'), array('ACTIVE' => 'Y'), array('FIELDS' => array('ID', 'PERSONAL_COUNTRY', 'PERSONAL_CITY', 'PERSONAL_PHOTO'), 'SELECT' => array('UF_*')));

		 $arResult['USERS_COUNT'] = 0;

		while($arUser = $rsUsers->fetch()){
			$arResult['USERS_COUNT']++;
			
			$arResult['ITEMS'][$arUser['ID']] = $arUser;
			$countryRefId = array_search($arUser['PERSONAL_COUNTRY'], $arCountries['reference_id']);

			$arCity = array('POSITION' => $arUser['PERSONAL_CITY'] . ', ' . $arCountries['reference'][$countryRefId]);
			$arPhoto = \CFile::GetFileArray($arUser['PERSONAL_PHOTO']);
			/*Формируем список городов для запроса координат.*/
			if( !empty($arPhoto['SRC']) && file_exists($docRoot . $arPhoto['SRC']) ){
				$arCity['IMAGE'] = \CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 44, 'height' => 44), BX_RESIZE_IMAGE_EXACT);
			}
			else{
				$arCity['IMAGE'][] = '';
			}
			$arResult['CITIES'][] = $arCity;

			/*
			 * Формируем масиив адресов для дальнейщего геокодирования.
			 * Убираем дубли адресов
			*/
			$addr = urlencode($arUser['PERSONAL_CITY'] . ', ' . $arCountries['reference'][$countryRefId]);
			if( !array_key_exists($addr, $arCoords) ){
				$arCoords[$addr] = array();
			}
		}

		/*
		 * Геокодирование.
		 * Кешируем результаты геокодирования на 30 дней.
		 * На дольше нельзя по политике Яндекс.
		 * */
		foreach (array_keys($arCoords) as $addr) {
			$cache = new Cache($addr, '/geocoder/', 86400 * 30);
			if( $cache->start() ){
				$obResponse = json_decode(file_get_contents('https://geocode-maps.yandex.ru/1.x/?results=1&key=ADX7EVgBAAAANefeIgMAkeViC5kqVvhXTh56wcky4IGqfjwAAAAAAAAAAACqoBcdQqjZQ5MIkHa432GMTtigkA==&format=json&geocode=' . $addr));
				$coords = $obResponse->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
				$cache->end($coords);
			}
			else{
				$coords = $cache->getVars();
			}

			$arCoords[$addr] = $coords;
		}

		foreach($arResult['CITIES'] as $k => &$arCity){
			$arCity['COORDS'] = $arCoords[urlencode($arCity['POSITION'])];
			if( empty($arCoords[urlencode($arCity['POSITION'])]) ){
				unset($arResult['CITIES'][$k]);
			}
		}
		unset($arCity);

		$this->arResult = $arResult;
	}

	/**
	* выполняет логику работы компонента
	* 
	* @return void
	*/

	public function executeComponent()
	{
		try
		{    
			$this->getResult();
			$this->includeComponentTemplate($this->page);

		}
		catch (Exception $e)
		{   
			ShowError($e->getMessage());
		}
	}
}