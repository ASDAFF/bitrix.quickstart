<?
/**
 * GeoIP
 * @package 6sec.ru
 * @copyright 20012-2015 Web Lab 6sec.ru
 */
namespace Ss\Geoip;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Geoip
{
	public $ip_addr = "";
	public $country_short_name = "";
	public $country_full_name = "";
	public $country_full_ru_name = "";
	public $region_name = "";
	public $city_name = "";
	public $city_id = "";
	public $country_code = "";

	/**
	 * Module ID
	 */
	const moduldeID = 'ss.geoip';

	/**
	 * Initialization parameters ip address
	 */
	public function __construct()
	{
		$ipInit = \SSIPInit();

		$this->ip_addr = $ipInit["IP"];
		$this->city_id = $ipInit["CITY_ID"];
		$this->country_code = $ipInit["COUNTRY_ID"];

		$this->GetCity();
		$this->GetCountry();
	}

	/**
	 * Get the city and region
	 */
	public function GetCity()
	{
		if(!empty($this->city_id) && !empty($this->country_code))
		{
			$obCityInfo = \Ss\Geoip\CityTable::getList(array(
				"filter" => array(
					"ID" => $this->city_id,
					"COUNTRY_ID" => $this->country_code
				)
			));
			if($arCityInfo = $obCityInfo->Fetch())
			{
				$this->city_name = $arCityInfo["NAME"];
				$this->region_name = $arCityInfo["REGION"];
			}
		}
	}

	/**
	 * Get country
	 */
	public function GetCountry()
	{
		if(!empty($this->country_code))
		{
			$obCountryInfo = \Ss\Geoip\CountryTable::getList(array(
				"filter" => array(
					"ID" => $this->country_code,
				)
			));
			if($arCountryInfo = $obCountryInfo->Fetch())
			{
				$this->country_short_name = $arCountryInfo["SHORT_NAME"];
				$this->country_full_name = $arCountryInfo["NAME"];
				$this->country_full_ru_name = $arCountryInfo["RU_NAME"];
			}
		}
	}

	/**
	 * Sets the value of the cookie SS_GEO_IP based or automatically transferred to city
	 * @param string $cityID
	 * @return boolean
	 */
	public function SetCookie($cityID = "")
	{
		global $APPLICATION;

		if(!empty($cityID))
		{
			$obCity = \Ss\Geoip\CityTable::getById($cityID);
			if($arCity = $obCity->Fetch())
			{
				$this->city_name = $arCity["NAME"];
				$this->region_name = $arCity["REGION"];
				$this->country_code = $arCity["COUNTRY_ID"];

				$this->GetCountry();
			}
		}

		$cookie = $this->country_full_ru_name."|".$this->region_name."|".$this->city_name;
		$APPLICATION->set_cookie("SS_GEO_IP", $cookie);

		return true;
	}

	/**
	 * Gets cookies and splits it into an array
	 * Country, region, city
	 *
	 * @param unknown $cookie
	 * @return array|boolean
	 */
	public function GetInfoByCookie($cookie)
	{
		if(!empty($cookie))
		{
			$keys = array("COUNTRY", "REGION", "CITY");
			$values = explode('|', $cookie);
			$arItems = array_combine($keys ,$values);

			return $arItems;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns the coordinates of a city
	 * @param unknown $sAddress
	 * @return array
	 */
	public function getYMapsCoords($sAddress)
	{
		if(empty($sAddress))
			return false;

		$xml = simplexml_load_file('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($sAddress));
		if($xml)
		{
			$sCoords = (string)$xml->GeoObjectCollection->featureMember->GeoObject->Point->pos;

			if(empty($sCoords))
				return false;

			$arCoords = explode(" ", $sCoords);

			return array(
				"LATITUDE" => $arCoords[0],
				"LONGITUDE" => $arCoords[1]
			);
		}
	}

	/**
	 * Returns an array with a full description of the location
	 *
	 * use Ss\Geoip\Geoip;
	 *
	 * if(CModule::IncludeModule("ss.geoip")
	 * {
	 * 		$user = new Geoip;
	 * 		$info = $user->GetInfo();
	 *
	 * 		echo "<pre>$info</pre>";
	 * }
	 *
	 * Array
	 * (
	 *   [IP] => 195.208.48.195 // IP адрес пользователя
	 *   [COUNTRY_CODE] => RU // Двузначный код страны
	 *   [COUNTRY_SHORT_NAME] => RUS // Трехзначный код страны
	 *   [COUNTRY_FULL_NAME] => RUSSIAN FEDERATION // Полное название страны на Английском
	 *   [COUNTRY_FULL_RU_NAME] => Россия // Полное название страны на Русском
	 *   [REGION] => Москва // Название региона
	 *   [CITY_ID] => 845 // ID города
	 *   [CITY_NAME] => Москва // Название города
	 *   [LATITUDE] => 37.619899 // Широта города
     *   [LONGITUDE] => 55.753676 // Долгота города
	 * )
	 * @return multitype:string
	 */
	public function GetInfo()
	{
		global $APPLICATION;

		$cookie = $APPLICATION->get_cookie("SS_GEO_IP");
		$cookieInfo = $this->GetInfoByCookie($cookie);

		if(!empty($cookieInfo))
		{
			$obCityInfo = \Ss\Geoip\CityTable::getList(array(
				"filter" => array(
					"NAME" => $cookieInfo["CITY"],
				)
			));
			if($arCityInfo = $obCityInfo->Fetch())
			{
				$this->city_id = $arCityInfo["ID"];
				$this->city_name = $arCityInfo["NAME"];
				$this->region_name = $arCityInfo["REGION"];
				$this->country_code = $arCityInfo["COUNTRY_ID"];

				$this->GetCountry();
			}
		}

		$arInfo = array(
			"IP" => $this->ip_addr,
			"COUNTRY_CODE" => $this->country_code,
			"COUNTRY_SHORT_NAME" => $this->country_short_name,
			"COUNTRY_FULL_NAME" => $this->country_full_name,
			"COUNTRY_FULL_RU_NAME" => $this->country_full_ru_name,
			"REGION" => $this->region_name,
			"CITY_ID" => $this->city_id,
			"CITY_NAME" => $this->city_name,
		);

		$coordinates = $this->getYMapsCoords($this->country_full_ru_name.", ".$this->city_name);

		if(!empty($coordinates))
		{
			$arInfo = array_merge($arInfo, $coordinates);
		}

		return $arInfo;
	}
}
?>