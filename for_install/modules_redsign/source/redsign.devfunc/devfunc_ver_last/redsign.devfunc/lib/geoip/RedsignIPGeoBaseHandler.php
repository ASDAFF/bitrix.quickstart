<?php

use \Bitrix\Main\Service\GeoIp;
use \Bitrix\Main\Localization\Loc;

include(dirname(__FILE__).'/IPGeoBase/ipgeobase.php');

Loc::loadMessages(__FILE__);

class RedsignIPGeoBaseHandler extends GeoIp\Base 
{
	public function getTitle()
	{
		return Loc::getMessage('RS_DF_GEOIP_IPGEOBASE_TITLE');
	}
	
	public function getDescription()
	{
		return Loc::getMessage('RS_DF_GEOIP_IPGEOBASE_DESCRIPTION');
	}
	
	public function getDataResult($ip, $lang = '') 
	{
		$dataResult = new GeoIp\Result;
		$geoData = new GeoIp\Data();
		
		$geoData->ip = $ip;
		$geoData->lang = $lang = strlen($lang) > 0 ? $lang : 'ru';
		
		$ipGeoBase = new \Redsign\DevFunc\GeoIp\IPGeoBase('utf-8' == strtolower(SITE_CHARSET));
		$res = $ipGeoBase->getRecord($ip);
		
		if ($res) {
			$geoData->countryName = $res['cc'] == 'UA' ? Loc::getMessage('RS_DF_GEOIP_UA') : Loc::getMessage('RS_DF_GEOIP_RU');
			$geoData->countryCode = $res['cc'];
			$geoData->regionName = $res['region'];
			$geoData->cityName = $res['city'];
			$geoData->latitude = $res['lat'];
			$geoData->longitude = $res['lng'];
		}
		
		$dataResult->setGeoData($geoData);
		return $dataResult;
	}
	
	public function getSupportedLanguages()
	{
		return array('ru');
	}
	
	public function getProvidingData()
	{
		$result = new GeoIp\ProvidingData();
		$result->countryName = true;
		$result->countryCode = true;
		$result->regionName = true;
		$result->cityName = true;
		$result->latitude = true;
		$result->longitude = true;
		return $result;
	}
}
