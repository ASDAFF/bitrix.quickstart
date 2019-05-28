<?php
namespace Bitrix\Sale;

/**
 * Class SalesZone
 * @package Bitrix\Sale *
 */
class SalesZone
{
	const CONN_ENTITY_NAME = 		'Bitrix\Sale\Location\SiteLocation';
	const LOCATION_ENTITY_NAME = 	'Bitrix\Sale\Location\Location';

	/**
	 * @param string $lang - language Id
	 * @return array - list of all regions
	 */
	public static function getAllRegions($lang)
	{
		static $result = null;

		if($result === null)
		{
			$result = array();
			$dbRegionList = \CSaleLocation::GetRegionList(array(), array(), $lang);

			while ($arRegion = $dbRegionList->fetch())
				$result[$arRegion["ID"]] = $arRegion["NAME_LANG"];
		}

		return $result;
	}

	/**
	 * @param string $lang - language Id
	 * @return array - list of all cities
	 */
	public static function getAllCities($lang)
	{
		static $result = null;

		if($result === null)
		{
			$result = array();
			$dbCityList = \CSaleLocation::GetCityList(array(), array(), $lang);
			while($arCity = $dbCityList->fetch())
				$result[$arCity["ID"]] = $arCity["NAME_LANG"];
		}

		return $result;
	}

	/**
	 * Checks if country Id is in list of sales zone countries Ids.
	 * @param int $countryId
	 * @param string $siteId
	 * @return bool
	 */
	public static function checkCountryId($countryId, $siteId)
	{

		if(\CSaleLocation::isLocationProMigrated())
		{
			if(!intval($countryId) || !strlen($siteId))
				return false;

			return self::checkLocationIsInLinkedPart($countryId, $siteId);
		}
		else
		{

			$cIds = static::getCountriesIds($siteId);
			return in_array($countryId, $cIds) || in_array("", $cIds);

		}
	}

	/**
	 * Checks if regionId is in list of sales zone regions Ids
	 * @param int $regionId
	 * @param string $siteId
	 * @return bool
	 */
	public static function checkRegionId($regionId, $siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
		{
			if(!intval($regionId) || !strlen($siteId))
				return false;

			return self::checkLocationIsInLinkedPart($regionId, $siteId);
		}
		else
		{

			$rIds = static::getRegionsIds($siteId);
			return in_array($regionId, $rIds) || in_array("", $rIds);

		}
	}

	/**
	 * Checks if citiy Id is in list of sales zone cities Ids
	 * @param int $cityId
	 * @param string $siteId
	 * @return bool
	 */
	public static function checkCityId($cityId, $siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
		{
			if(!strlen($siteId))
				return false;

			if(!strlen($cityId) || $cityId == 0)
				return in_array("", static::getCitiesIds($siteId));

			return self::checkLocationIsInLinkedPart($cityId, $siteId);
		}
		else
		{

			$cIds = static::getCitiesIds($siteId);
			return in_array($cityId, $cIds) || in_array("", $cIds);

		}
	}

	/**
	 * Checks if location id is in sales zone
	 * @param int $locationId
	 * @param string $siteId
	 * @return bool
	 */
	public static function checkLocationId($locationId, $siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
		{
			if(!intval($locationId) || !strlen($siteId))
				return false;

			return Location\SiteLocationTable::checkConnectionExists($siteId, $locationId);
		}
		else
		{

			$result = false;

			$arLocation = \CSaleLocation::GetByID($locationId);

			if(static::checkCountryId($arLocation["COUNTRY_ID"], $siteId)
				&& static::checkRegionId($arLocation["REGION_ID"], $siteId)
				&& static::checkCityId($arLocation["CITY_ID"], $siteId)
			)
			{
				$result = true;
			}

			return $result;

		}
	}

	private static function checkLocationIsInLinkedPart($locationId, $siteId)
	{
		$types = \CSaleLocation::getTypes();
		$class = self::CONN_ENTITY_NAME.'Table';

		if(!$class::checkLinkUsageAny($siteId))
			return true;

		if((string) $locationId == '')
			return false;

		$node = \Bitrix\Sale\Location\LocationTable::getList(array(
			'filter' => array('=ID' => $locationId),
			'select' => array('ID', 'LEFT_MARGIN', 'RIGHT_MARGIN')
		))->fetch();
		if(!is_array($node))
			return false;

		$stat = $class::getLinkStatusForMultipleNodes(array($node), $siteId);

		return $stat[$node['ID']] !== $class::LSTAT_IN_NOT_CONNECTED_BRANCH;
	}

	// returns a list of IDs of locations that are linked with $siteId and have type of $type
	private static function getSelectedTypeIds($type, $siteId)
	{
		static $index; // this function is called hell number of times outside, so a little cache is provided

		if(!strlen($siteId))
			return array(''); // means "all"

		if($index == null)
			$index = array();

		if(!isset($index[$type][$siteId]))
		{
			$result = array();
			$class = self::CONN_ENTITY_NAME.'Table';

			$index[$type][$siteId] = array();

			// check if link even used
			if(!$class::checkLinkUsageAny($siteId))
				$index[$type][$siteId] = array('');
			else
			{
				$types = \CSaleLocation::getTypes();

				$query = new \Bitrix\Main\Entity\Query(self::LOCATION_ENTITY_NAME);

				$query
					->registerRuntimeField(
						'L',
						array(
							'data_type' => self::LOCATION_ENTITY_NAME,
							'reference' => array(
								'=ref.TYPE_ID' => array('?', $types[$type]),
								array(
									'LOGIC' => 'OR',
									array(
										'<=ref.LEFT_MARGIN' => 'this.LEFT_MARGIN',
										'>=ref.RIGHT_MARGIN' => 'this.RIGHT_MARGIN'
									),
									array(
										'>=ref.LEFT_MARGIN' => 'this.LEFT_MARGIN',
										'<=ref.RIGHT_MARGIN' => 'this.RIGHT_MARGIN'
									)
								)
							),
							'join_type' => 'inner'
						)
					);

				$query->setSelect(array('ID__' => 'L.ID', /*'L.CODE', 'L.NAME.NAME'*/));

				// get all connection points of interested types (if any)
				$sql = $class::getConnectedLocationsQuery(
					$siteId, 
					array(/*'filter' => array('TYPE_ID' => \CSaleLocation::getTypeFilterCondition()), */'select' => array('ID')),
					array('GET_LINKED_THROUGH_GROUPS' => true)
				);

				if($sql)
					$query->setFilter(array('@ID' => new \Bitrix\Main\DB\SqlExpression($sql)/*, '=L.NAME.LANGUAGE_ID' => LANGUAGE_ID*/));

				$res = $query->exec();
				while($item = $res->fetch())
				{
					$index[$type][$siteId][] = $item['ID__'];
				}

				// special case: when all types are actually selected, an empty string ('') SHOULD be present among $index[$type][$siteId]

				$res = Location\LocationTable::getList(array(
					'filter' => array(
						'TYPE_ID' => $types[$type]
					),
					'runtime' => array(
						'CNT' => array(
							'data_type' => 'integer',
							'expression' => array('COUNT(*)')
						)
					),
					'select' => array(
						'CNT'
					)
				))->fetch();

				if($res['CNT'] == count($index[$type][$siteId]))
					$index[$type][$siteId][] = '';
			}
		}

		return $index[$type][$siteId];
	}

	/**
	 * @param string $siteId
	 * @return array - sales zones cities Ids
	 */
	public static function getCitiesIds($siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
			return self::getSelectedTypeIds('CITY', $siteId);

		return explode(":" , \COption::GetOptionString('sale', 'sales_zone_cities', '', $siteId));
	}

	/**
	 * @param string $siteId
	 * @return array - sales zones regions Ids
	 */
	public static function getRegionsIds($siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
			return self::getSelectedTypeIds('REGION', $siteId);

		return explode(":" , \COption::GetOptionString('sale', 'sales_zone_regions', '', $siteId));
	}

	/**
	 * @param string $siteId
	 * @return array - sales zones countries Ids
	 */
	public static function getCountriesIds($siteId)
	{
		if(\CSaleLocation::isLocationProMigrated())
			return self::getSelectedTypeIds('COUNTRY', $siteId);

		return explode(":" , \COption::GetOptionString('sale', 'sales_zone_countries', '', $siteId));
	}

	/**
	 * A very important function. Here we decide what locations we need to take, 
	 * making a descision based on $_REQUEST from sales zone selector.
	 * 
	 * Then we normalize the selection and store to database.
	 * 
	 * Also this function is used in data migrator.
	 */
	public static function saveSelectedTypes($typeList, $siteId)
	{
		$types = \CSaleLocation::getTypes();

		$locations = array(
			Location\Connector::DB_LOCATION_FLAG => array(),
			Location\Connector::DB_GROUP_FLAG => array()
		);
		if(is_array($typeList['COUNTRY']) && !empty($typeList['COUNTRY']))
			$typeList['COUNTRY'] = array_flip($typeList['COUNTRY']);

		if(is_array($typeList['REGION']) && !empty($typeList['REGION']))
			$typeList['REGION'] = array_flip($typeList['REGION']);

		if(is_array($typeList['CITY']) && !empty($typeList['CITY']))
			$typeList['CITY'] = array_flip($typeList['CITY']);

		$allCountries = isset($typeList['COUNTRY']['']);
		$allRegions = isset($typeList['REGION']['']);
		$allCities = isset($typeList['CITY']['']);

		// no countries
		$noCountry = isset($typeList['COUNTRY']['NULL']);
		$noRegion = isset($typeList['REGION']['NULL']);

		// make up list of ids
		$res = Location\LocationTable::getList(array('select' => array(
			'ID', 
			'COUNTRY_ID', 
			'REGION_ID', 
			'CITY_ID',
			'TYPE_ID',
			//'LNAME' => 'NAME.NAME'
		), 'filter' => array(
			//'=NAME.LANGUAGE_ID' => LANGUAGE_ID
		)));
		while($item = $res->fetch())
		{
			$id = $item['ID'];
			$countryId = intval($item['COUNTRY_ID']);
			$regionId = intval($item['REGION_ID']);
			$cityId = intval($item['CITY_ID']);
			$typeId = intval($item['TYPE_ID']);

			$take = false;
			$countryTaken = false;
			$regionTaken = false;

			if($typeId == $types['COUNTRY']) // it is a country
			{
				if($allCountries // we take all countries
					|| // or..
					isset($typeList['COUNTRY'][$countryId]) // we manually selected this country
				)
				{
					$take = true;
					$countryTaken = true;
				}
			}

			if($typeId == $types['REGION']) // it is a region
			{
				if((
						$allRegions // we take all regions (of selected countries)
						&& // and
						$countryTaken // country is selected already
					) 
					|| // or ..
					isset($typeList['REGION'][$regionId]) // we manually selected this region
					|| // or ..
					($noCountry && !$countryId) // we also accept regions without countries, and this region actually dont have a country
				)
				{
					$take = true;
					$regionTaken = true;
				}
			}

			if($typeId == $types['CITY']) // it is a city
			{
				if((
						$allCities // we take all cities (of selected regions of selected countries)
						&& // and
						$regionTaken // region is selected already
					) 
					|| // or..
					isset($typeList['REGION'][$regionId]) // we manually selected this city
					|| // or ..
					($noRegion && !$regionId) // we also accept cities without regions, and this city actually dont have a region
					// it seems that cities without a country is not supported with the current logic
				)
				{
					$take = true;
				}
			}

			if(isset($typeList['CITY'][$cityId]) && $typeId == $types['CITY']) // this is a city and it is in list - take it
				$take = true;

			if($take)
				$locations[Location\Connector::DB_LOCATION_FLAG][$id] = true;
		}

		// normalize
		$class = self::CONN_ENTITY_NAME.'Table';
		$locations[Location\Connector::DB_LOCATION_FLAG] = array_keys($locations[Location\Connector::DB_LOCATION_FLAG]);

		$locations[Location\Connector::DB_LOCATION_FLAG] = $class::normalizeLocationList($locations[Location\Connector::DB_LOCATION_FLAG]);

		// store to database
		$class::resetMultipleForOwner(strlen($siteId) ? $siteId : $class::ALL_SITES, $locations);
	}

	/**
	 * Returns filter for using in queries such as in bitrix/modules/sale/install/components/bitrix/sale.ajax.locations/search.php
	 * @param string $object (city|region|country)
	 * @param string $siteId
	 * @return array
	 */
	public static function makeSearchFilter($object, $siteId)
	{
		$result = array();

		$countries = static::getCountriesIds($siteId);
		$regions = static::getRegionsIds($siteId);
		$cities = static::getCitiesIds($siteId);

		if(!in_array("", $cities) && $object == "city")
			$result = array("CITY_ID" => $cities);
		elseif(!in_array("", $regions) && ($object == "city"  || $object == "region"))
			$result = array("REGION_ID" => $regions);
		elseif(!in_array("", $countries))
			$result = array("COUNTRY_ID" => $countries);

		return $result;
	}

	/**
	 * @param array $countriesIds
	 * @param string $lang
	 * @return array - regions from sales zone
	 */
	public static function getRegions($countriesIds = array(), $lang = LANGUAGE_ID)
	{

		$regions = array();
		$regionsList = static::getAllRegions($lang);
		$getCountryNull = in_array("NULL", $countriesIds) ? true : false;
		$filter = in_array("", $countriesIds) ? array() : array(($getCountryNull ? "+" : "")."COUNTRY_ID" => $countriesIds);

		$dbLocationsList = \CSaleLocation::GetList(
			array("SORT"=>"ASC", "REGION_NAME_LANG"=>"ASC"),
			$filter,
			array("REGION_ID", "COUNTRY_ID")
		);

		while($arRegion = $dbLocationsList->GetNext())
		{
			if(strlen($arRegion["REGION_ID"]) > 0 && $arRegion["REGION_ID"] != "0")
				$regions[$arRegion["REGION_ID"]] = $regionsList[$arRegion["REGION_ID"]];
		}

		return $regions;
	}

	/**
	 * @param array $countriesIds
	 * @param array $regionsIds
	 * @param string $lang
	 * @return array cities list from sales zone
	 */
	public static function getCities($countriesIds = array(), $regionsIds = array(), $lang )
	{
		$cities = array();
		$citiesList = static::getAllCities($lang);
		$getRegionNull = in_array("NULL", $regionsIds) ? true : false;
		$getRegionAll = in_array("", $regionsIds) ? true : false;
		$getCountryNull = in_array("NULL", $countriesIds) ? true : false;
		$getCountryAll = in_array("", $countriesIds) ? true : false;

		$filter = in_array("", $regionsIds) ? array() : array(($getRegionNull ? "+" : "")."REGION_ID" => $regionsIds);

		foreach($countriesIds as $countryId)
		{
			if(($getRegionNull || $getRegionAll) && !$getCountryAll)
				$filter[($getCountryNull ? "+" : "")."COUNTRY_ID"] = $countryId;

			$dbLocationsList = \CSaleLocation::GetList(
				array("SORT"=>"ASC", "CITY_NAME_LANG"=>"ASC"),
				$filter,
				array("CITY_ID")
			);

			while($arCity = $dbLocationsList->GetNext())
				if(strlen($arCity["CITY_ID"]) > 0)
					$cities[$arCity["CITY_ID"]] =  $citiesList[$arCity["CITY_ID"]];
		}

		return  $cities;
	}
}