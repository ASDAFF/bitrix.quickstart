<?php

namespace Redsign\DevFunc\Sale\Location;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Service\GeoIp;
use \Bitrix\Main\Web\Cookie;
use \Bitrix\Sale;

class Location
{
    const COOKIE_NAME = 'RS_MY_LOCATION';

    public static $myCity = null;

    public static function getMyCity()
    {
        if (self::isBot()) {
            return [];
        }

        if (!self::$myCity)
		{
            $sCity = Application::getInstance()->getContext()->getRequest()->getCookie(self::COOKIE_NAME);

            if ($sCity)
			{
                self::$myCity = unserialize($sCity);
            }
			else
			{
                $result = GeoIp\Manager::getDataResult('', LANGUAGE_ID);
                if ($result) {
                    $geoData = $result->getGeoData();

                    self::$myCity = (!empty($geoData->cityName) ? self::getCityDataByName($geoData->cityName) : null);
                }

                if (!self::$myCity)
                {
                    self::$myCity = self::getDefaultCity();
                }
                self::setMyCity($myCity['CODE']);
            }
        }
        return self::$myCity;
    }

    public static function getDefaultCity($siteId = SITE_ID)
    {
        $moduleId = 'redsign.devfunc';
        $defaultCityCode = \Bitrix\Main\Config\Option::get($moduleId, 'default_location_code', '', $siteId);
        return self::getCityDataByCode($defaultCityCode);
    }

    public static function getCityDataByName($name)
    {

        if (!Loader::includeModule('sale')) {
            return;
        }

        $arFilter = [
            'NAME.LANGUAGE_ID' => 'ru',
            'TYPE.CODE' => 'CITY',
            'PARENTS.NAME.LANGUAGE_ID' => 'ru',
            'LOCATION_NAME' => $name
        ];
        $arSelect = [
          'ID',
          'CODE',
          'LOCATION_NAME' => 'NAME.NAME',
        ];

        $arLocation = Sale\Location\LocationTable::getList(array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'limit' => 1,
            'cache' => array(
                'ttl' => 3600,
                'cache_joins' => true,
            )
        ))->fetch();

        return $arLocation ? self::getCityDataById($arLocation['ID']) : null;
    }

    public static function getCityDataByCode($code)
    {
        return self::getCityData(['CODE' => $code]);
    }

    public static function getCityDataById($id)
    {
        return self::getCityData(['ID' => $id]);
    }

    public static function getCityData(array $arFilter, $arOrder = ['PARENT_CODE' => 'desc'])
    {
        if (!Loader::includeModule('sale')) {
            return;
        }

        $arSelect = [
          'ID',
          'CODE',
          'LATITUDE',
          'LONGITUDE',
          'LOCATION_NAME' => 'NAME.NAME',
          'LOCATION_TYPE' => 'TYPE.CODE',
          'PARENT_NAME' => 'PARENTS.NAME.NAME',
          'PARENT_TYPE' => 'PARENTS.TYPE.CODE',
          'PARENT_CODE' => 'PARENTS.CODE',
        ];

        $arFilter = array_merge($arFilter, [
            'NAME.LANGUAGE_ID' => LANGUAGE_ID,
            'TYPE.CODE' => 'CITY',
            'PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
        ]);

        $locationIterator = Sale\Location\LocationTable::getList([
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => $arOrder,
            'cache' => array(
                'ttl' => 3600,
                'cache_joins' => true,
            ),
        ]);

        $arCityData = [];
        while ($arLocation = $locationIterator->fetch()) {
            if (!$arLocation['PARENT_TYPE']) {
                continue;
            }

            switch ($arLocation['PARENT_TYPE']) {
                case 'CITY':
                    $arCityData['ID'] = $arLocation['ID'];
                    $arCityData['NAME'] = $arLocation['LOCATION_NAME'];
                    $arCityData['CODE'] = $arLocation['CODE'];
                    $arCityData['LATITUDE'] = $arLocation['LATITUDE'];
                    $arCityData['LONGITUDE'] = $arLocation['LONGITUDE'];

                    break;
                default:
                    $arCityData[$arLocation['PARENT_TYPE'].'_NAME'] = $arLocation['PARENT_NAME'];
                    $arCityData[$arLocation['PARENT_TYPE'].'_CODE'] = $arLocation['PARENT_CODE'];

                    break;
            }
        }

        return $arCityData;
    }

    public static function getMyCityName()
    {
        $myRegion = self::getMyGeoData();

        return $myRegion->cityName;
    }

    public static function setMyCity($id)
    {
        if (!Loader::includeModule('sale'))
		{
            return;
        }

        $arCityData = self::getCityDataById($id);
        if ($arCityData)
		{
            $cookie = new Cookie(self::COOKIE_NAME, serialize($arCityData));
            Application::getInstance()->getContext()->getResponse()->addCookie($cookie);

            if (self::$myCity != $arCityData)
			{
                self::$myCity = $arCityData;
				
				// static $eventOnResultExists = null;

				// if ($eventOnGetExists === true || $eventOnGetExists === null)
				// {
					// foreach (GetModuleEvents('redsign.devfunc', 'OnSiteLocationSelected', true) as $arEvent)
					// {
						// $eventOnGetExists = true;
						// $mxResult = ExecuteModuleEventEx(
							// $arEvent,
							// array(
								// $arCityData,
							// )
						// );
					// }
					// if ($eventOnGetExists === null)
						// $eventOnGetExists = false;
				// }
            }
        }
    }
    
    public static function isBot() {
        return preg_match(
            "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", 
            $_SERVER['HTTP_USER_AGENT']
        );
    }

    public static function OnSaleComponentOrderSetLocation(&$arUserResult, $request, &$arParams, &$arResult)
    {
        $moduleId = 'redsign.devfunc';
        $bReplaceLocationRegion = \Bitrix\Main\Config\Option::get($moduleId, 'replace_location_region', 'N', SITE_ID);

        if ($bReplaceLocationRegion != 'Y')
            return;

        $arCityData = self::getMyCity();

        $orderPropIterator = \Bitrix\Sale\Internals\OrderPropsTable::getList([
            'select' => ['ID', 'DEFAULT_VALUE'],
            'filter' => [
                'LOGIC' => 'OR',
                'IS_LOCATION' => 'Y',
                'IS_LOCATION4TAX' => 'Y',
            ],
            'cache' => array(
                'ttl' => 60,
                'cache_joins' => true,
            ),
        ]);

        while ($arOrderProp = $orderPropIterator->fetch()) {
            $iPropId = $arOrderProp['ID'];

            if (empty($arUserResult['ORDER_PROP'][$iPropId])) {
                $arUserResult['ORDER_PROP'][$iPropId] = $arCityData['CODE'];
            }
        }
    }
    
    public static function onGeoIpHandlersBuildList() {
        
        $arPathIPGeoBaseHandler = [
            '/local/modules/redsign.devfunc/lib/geoip/RedsignIPGeoBaseHandler.php',
            '/bitrix/modules/redsign.devfunc/lib/geoip/RedsignIPGeoBaseHandler.php',
        ];
        
        $sPathIPGeoBaseHandler = false;
        
        foreach ($arPathIPGeoBaseHandler as $sPath) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'].$sPath)) {
                $sPathIPGeoBaseHandler = $sPath;
                break;
            }
        }
        
        if ($sPathIPGeoBaseHandler) {            
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::SUCCESS,
                array(
                    '\RedsignIPGeoBaseHandler' => $sPathIPGeoBaseHandler,
                )
            );
        } else {
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::ERROR
            );
        }
    }
}
