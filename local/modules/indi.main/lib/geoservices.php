<?php
/**
 * Individ module
 *
 * @category     Individ
 * @copyright    2014 Individ LTD
 * @link         http://individ.ru
 */

namespace Indi\Main;

/**
 * Работа с гео-данными постетителей
 */
class GeoServices
{
	// ID инфоблока с городами
	const CITIES_IBLOCK_ID = 49;
	const COUNTRIES_IBLOCK_ID = 8;

	// ID элемента города по умолчанию
	const DEFAUL_CITY_CODE = 'EXAMPLE_CITY_CODE';
	const DEFAUL_COUNTRY_CODE = 'RU';

	/**
	 * Данные о текущем местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @var array|null
	 */
	protected static $currentLocation = NULL;

	/**
	 * Возвращает данные о местоположении из модуля статистики
	 *
	 * @return array
	 */
	public static function getCurrentInfo()
	{
		if (!\Bitrix\Main\Loader::includeModule('statistic')) {
			throw new Exception("Statistic module isn't installed.");
		}

		$city = new \CCity();

		return $city->GetFullInfo();
	}

	/**
	 * Возвращает данные о местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @return array
	 */
	public static function getCurrentLocation()
	{
		if (self::$currentLocation !== NULL) {
			return self::$currentLocation;
		}

		if (!\Bitrix\Main\Loader::includeModule('sale')) {
			throw new Exception("Sale module is't installed.");
		}

		self::$currentLocation = array();
		$info = self::getCurrentInfo();

		foreach (array(
			         'CITY_NAME' => 'CITY_NAME',
			         'REGION_NAME' => 'REGION_NAME',
			         'COUNTRY_NAME' => 'COUNTRY_NAME',
		         ) as $infoKey => $locationKey) {
			if (!$info[$infoKey]['VALUE']) {
				continue;
			}
			foreach (array('en', 'ru') as $lid) {
				self::$currentLocation = self::getLocationRecordset(
					array(
						'LID' => $lid,
						$locationKey => $info[$infoKey]['VALUE'],
					)
				)->Fetch();

				if (self::$currentLocation) {
					break;
				}
			}

			if (self::$currentLocation) {
				break;
			}
		}

		if (self::$currentLocation) {
			self::$currentLocation = \CSaleLocation::GetByID(self::$currentLocation['ID']);
		} else {
			self::$currentLocation = array();
		}

		return self::$currentLocation;
	}

	/**
	 * Возвращает данные о местоположении из таблицы местоположений модуля интернет-магазина
	 *
	 * @return array
	 */
	protected static function getLocationRecordset($filter)
	{
		return \CSaleLocation::GetList(
			array(
				'SORT' => 'ASC',
			),
			$filter,
			false,
			false,
			array()
		);
	}

	/**
	 * Получаем ID страны
	 * Сначала из кук и, если не задана, по IP.
	 * Если и такого нет, получаем страну по умолчанию.
	 *
	 */
	function GetCountryID()
	{
		$arCountry = self::GetCountry();

		return $arCountry["ID"];
	}

	/**
	 * Получаем страну
	 * Сначала из кук и, если не задана, по IP.
	 * Если и такого нет, получаем страну по умолчанию.
	 *
	 */
	function GetCountry()
	{
		$arCountry = self::GetCountryFromCookies();

		if (!is_array($arCountry)) {
			$arCountry = self::GetCountryByIP();
		}

		if (!is_array($arCountry)) {
			//			$arCountry = self::GetCountryElementByID(self::DEFAUL_COUNTRY_ID);
			$arCountry = self::GetCountryElementByCode(self::DEFAUL_COUNTRY_CODE);
		}

		return $arCountry;
	}

	/**
	 * Получаем страну из кук
	 *
	 */

	function GetCountryFromCookies()
	{
		global $APPLICATION;
		$country_id = $APPLICATION->get_cookie("COUNTRY_ID");

		if ($country_id > 0) {
			$arCountry = self::GetCountryElementByID($country_id);
		}

		if (is_array($arCountry)) {
			return $arCountry;
		} else {
			return false;
		}
	}

	/**
	 * Получаем страну по IP
	 *
	 */

	function GetCountryByIP()
	{
		$code = self::GetCountryCodeByIP();

		if (strlen($code) > 0) {
			return self::GetCountryElementByCode($code);
		} else {
			return false;
		}
	}

	/**
	 * Получает код страны по IP
	 *
	 */

	function GetCountryCodeByIP()
	{
		$obCity = new \CCity();
		$countryCode = $obCity->GetCountryCode();

		return $countryCode;
	}

	/**
	 * Устанавливает в куки страну с указаным ID
	 *
	 * @param int $country_id
	 * @param int $cookie_time
	 */

	function SetCountryToCookie($country_id, $cookie_time)
	{
		$country_id = intval($country_id);
		$cookie_time = intval($cookie_time);
		if (!$country_id > 0) {
			return false;
		}

		if (!$cookie_time > 0) {
			$cookie_time = time() + 3600 * 24 * 7;
		}

		global $APPLICATION;
		$APPLICATION->set_cookie("COUNTRY_ID", $country_id, $cookie_time, '/', $_SERVER["HTTP_HOST"]);
	}

	/**
	 * Получаем элемент страны по символьному коду (регистронезависимо)
	 *
	 * @param string $code
	 *
	 * @return {array|mixed[]}
	 */

	function GetCountryElementByCode($code)
	{
		$code = trim($code);

		if (!strlen($code) > 0) {
			return false;
		}

		$CacheId = serialize(array($code, self::COUNTRIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCountryElementByCode';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::COUNTRIES_IBLOCK_ID,
				"ACTIVE" => "Y",
				"=CODE" => $code    // регистронезависимый поиск по коду
			);
			$arSelect = array(
				"IBLOCK_ID",
				"ID",
				"NAME",
				"CODE",
				"PROPERTY_NAME_EN",
				"PREVIEW_PICTURE",
			);
			$dbEl = \CIBlockElement::GetList(
				array(),
				$arFilter,
				false,
				array('nTopCount' => 1),
				$arSelect
			);
			if ($arItem = $dbEl->GetNext()) {
				if ($arItem['PREVIEW_PICTURE']) {
					$arItem['PREVIEW_PICTURE'] = \CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
				}
				if (LANGUAGE_ID != 'ru') {
					$arItem['NAME'] = $arItem['PROPERTY_NAME_EN_VALUE'];
				}
				$arResult = $arItem;
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

	/**
	 * Получает элемент страны по его ID
	 *
	 * @param int $country_id
	 *
	 * @return {array|mixed[]}
	 */

	function GetCountryElementByID($country_id)
	{
		$country_id = intval($country_id);

		if (!strlen($country_id) > 0) {
			return false;
		}

		$CacheId = serialize(array($country_id, self::COUNTRIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCountryElementByID';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::COUNTRIES_IBLOCK_ID,
				"ACTIVE" => "Y",
				"ID" => $country_id,
			);
			$arSelect = array(
				"IBLOCK_ID",
				"ID",
				"NAME",
				"CODE",
				"PROPERTY_NAME_EN",
				"PREVIEW_PICTURE",
			);
			$dbEl = \CIBlockElement::GetList(
				array(),
				$arFilter,
				false,
				array('nTopCount' => 1),
				$arSelect
			);
			if ($arItem = $dbEl->GetNext()) {
				if ($arItem['PREVIEW_PICTURE']) {
					$arItem['PREVIEW_PICTURE'] = \CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
				}
				if (LANGUAGE_ID != 'ru') {
					$arItem['NAME'] = $arItem['PROPERTY_NAME_EN_VALUE'];
				}
				$arResult = $arItem;
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

	/**
	 * Получает список элементов стран
	 *
	 * @return {array|mixed[]}
	 */

	function GetCountryList()
	{
		$curCountry = self::GetCountry();
		$arResult = array();
		$CacheId = serialize(array(self::COUNTRIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCountryList';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::COUNTRIES_IBLOCK_ID,
				"ACTIVE" => "Y",
			);
			$arSelect = array(
				"IBLOCK_ID",
				"ID",
				"NAME",
				"CODE",
				"PROPERTY_NAME_EN",
				"PREVIEW_PICTURE",
			);
			$dbEl = \CIBlockElement::GetList(
				array('SORT' => 'ASC'),
				$arFilter,
				false,
				false,
				$arSelect
			);
			while ($arItem = $dbEl->GetNext()) {
				if ($arItem['PREVIEW_PICTURE']) {
					$arItem['PREVIEW_PICTURE'] = \CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
				}
				if (LANGUAGE_ID != 'ru') {
					$arItem['NAME'] = $arItem['PROPERTY_NAME_EN_VALUE'];
				}
				if ($curCountry['ID'] == $arItem['ID']) {
					$arItem['SELECTED'] = true;
				} else {
					$arItem['SELECTED'] = false;
				}
				$arResult[$arItem['ID']] = $arItem;
			}
			if (count($arResult) > 0) {
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

	/**
	 * Получаем город
	 * Сначала из кук и, если не задан, по IP.
	 * Если и такого нет, получаем Москву.
	 *
	 */
	function GetCity()
	{
		$arCity = self::GetCityFromCookies();

		if (!is_array($arCity)) {
			$arCity = self::GetCityByIP();
		}

		if (!is_array($arCity)) {
			$arCity = self::GetCityElementByCode(self::DEFAUL_CITY_CODE);
		}

		return $arCity;
	}

	/**
	 * Получаем город из кук
	 *
	 */
	function GetCityFromCookies()
	{
		global $APPLICATION;
		$city_id = $APPLICATION->get_cookie("CITY_ID");

		if ($city_id > 0) {
			$arCity = self::GetCityElementByID($city_id);
		}

		if (is_array($arCity)) {
			return $arCity;
		} else {
			return false;
		}
	}

	/**
	 * Получаем город по IP
	 *
	 */
	function GetCityByIP()
	{
		$code = self::GetCityCodeByIP();

		if (strlen($code) > 0) {
			return self::GetCityElementByCode($code);
		} else {
			return false;
		}
	}

	/**
	 * Получает код города по IP
	 *
	 */
	function GetCityCodeByIP()
	{
		$obCity = new \CCity();
		$arCity = $obCity->GetFullInfo();

		if (strlen($arCity["CITY_NAME"]["VALUE"]) > 0) {
			return $arCity["CITY_NAME"]["VALUE"];
		} else {
			return false;
		}

	}

	/**
	 * Устанавливает в куки город с указаным ID
	 *
	 * @param int $city_id
	 * @param int $cookie_time
	 */
	function SetCityToCookie($city_id, $cookie_time)
	{
		$city_id = intval($city_id);
		$cookie_time = intval($cookie_time);
		if (!$city_id > 0) {
			return false;
		}

		if (!$cookie_time > 0) {
			$cookie_time = time() + 3600 * 24 * 7;
		}

		global $APPLICATION;
		$APPLICATION->set_cookie("CITY_ID", $city_id, $cookie_time, '/', $_SERVER["HTTP_HOST"]);
	}

	/**
	 * Получаем элемент города по символьному коду (регистронезависимо)
	 *
	 * @param string $code
	 *
	 * @return {array|mixed[]}
	 */
	function GetCityElementByCode($code)
	{
		$code = trim($code);

		if (!strlen($code) > 0) {
			return false;
		}

		$CacheId = serialize(array($code, self::CITIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCityElementByCode';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::CITIES_IBLOCK_ID,
				"ACTIVE" => "Y",
				"=CODE" => $code    // регистронезависимый поиск по коду
			);
			$arSelect = array(
				"ID", "IBLOCK_ID", "NAME", "CODE",
			);
			$dbEl = \CIBlockElement::GetList(array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
			if ($arItem = $dbEl->GetNext()) {
				$arResult = $arItem;
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

	/**
	 * Получает элемент города по его ID
	 *
	 * @param int $city_id
	 *
	 * @return {array|mixed[]}
	 */
	function GetCityElementByID($city_id)
	{
		$city_id = intval($city_id);

		if (!strlen($city_id) > 0) {
			return false;
		}

		$CacheId = serialize(array($city_id, self::CITIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCityElementByID';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::CITIES_IBLOCK_ID,
				"ACTIVE" => "Y",
				"ID" => $city_id,
			);
			$arSelect = array(
				"ID", "IBLOCK_ID", "NAME", "CODE",
			);
			$dbEl = \CIBlockElement::GetList(array(), $arFilter, false, array('nTopCount' => 1), $arSelect);
			if ($arItem = $dbEl->GetNext()) {
				$arResult = $arItem;
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

	/**
	 * Получает список элементов стран
	 *
	 * @return {array|mixed[]}
	 */

	function GetCityList()
	{
		$curCity = self::GetCity();
		$arResult = array();
		$CacheId = serialize(array(self::CITIES_IBLOCK_ID));
		if (\COption::getOptionString("main", "component_cache_on") == "Y") {
			$CacheTime = 60 * 60;
		} else {
			$CacheTime = 0;
		}
		$CacheFolder = '/Indi/Main/GeoServices/GetCityList';
		$obCache = new \CPHPCache;
		$arResult = false;
		if ($obCache->InitCache($CacheTime, $CacheId, $CacheFolder)) {
			$arResult = $obCache->GetVars();
		} elseif ($obCache->StartDataCache($CacheTime, $CacheId, $CacheFolder)) {
			$arFilter = array(
				"IBLOCK_ID" => self::CITIES_IBLOCK_ID,
				"ACTIVE" => "Y",
			);
			$arSelect = array(
				"IBLOCK_ID",
				"ID",
				"NAME",
				"CODE",
				"PROPERTY_NAME_EN",
				"PREVIEW_PICTURE",
			);
			$dbEl = \CIBlockElement::GetList(
				array('SORT' => 'ASC'),
				$arFilter,
				false,
				false,
				$arSelect
			);
			while ($arItem = $dbEl->GetNext()) {
				if ($arItem['PREVIEW_PICTURE']) {
					$arItem['PREVIEW_PICTURE'] = \CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
				}
				if (LANGUAGE_ID != 'ru') {
					$arItem['NAME'] = $arItem['PROPERTY_NAME_EN_VALUE'];
				}
				if ($curCity['ID'] == $arItem['ID']) {
					$arItem['SELECTED'] = true;
				} else {
					$arItem['SELECTED'] = false;
				}
				$arResult[$arItem['ID']] = $arItem;
			}
			if (count($arResult) > 0) {
				$obCache->EndDataCache($arResult);
			} else {
				$obCache->AbortDataCache();
			}
		}

		return $arResult;
	}

}