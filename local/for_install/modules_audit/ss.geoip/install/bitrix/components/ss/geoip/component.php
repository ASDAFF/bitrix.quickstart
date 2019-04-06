<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Ss\Geoip\Geoip;
use Ss\Geoip\CityTable;
use Ss\Geoip\CountryTable;

if (!isset($arParams['CACHE_TIME'])) {
	$arParams['CACHE_TIME'] = 3600;
}

$arResult = array();

if(CModule::IncludeModule('ss.geoip'))
{
	$cookie = $APPLICATION->get_cookie("SS_GEO_IP");
	$confirm = $APPLICATION->get_cookie("SS_GEO_IP_CONFIRM");

	if(!empty($cookie))
	{
		$cookieField = Geoip::GetInfoByCookie($cookie);
		$obMainCity = CityTable::getList(array(
			"filter" => array(
				"NAME" => $cookieField["CITY"]
			)
		));
		if($arMainCity = $obMainCity->Fetch())
		{
			$GeoIP = new Geoip;
			$info = $GeoIP->GetInfo();

			$arMainCityProp = array(
				"MAIN" => "Y",
				"COUNTRY" => $cookieField["COUNTRY"],
				"LATITUDE" => $info["LATITUDE"],
				"LONGITUDE" => $info["LONGITUDE"]
			);

			$arResult["CITY_MAIN"] = array_merge($arMainCity, $arMainCityProp);
		}

		if(empty($confirm))
		{
			$APPLICATION->set_cookie("SS_GEO_IP_CONFIRM", "Y");
		}
	}
	else
	{
		$GeoIP = new Geoip;
		$info = $GeoIP->SetCookie();

		if($info)
		{
			LocalRedirect($APPLICATION->GetCurPageParam());
		}
	}

	$obCache = new CPHPCache;
	if($obCache->InitCache(86400*7, md5(serialize(array($arParams, $arResult["CITY_MAIN"]["ID"]))), "/ss_geoip"))
	{
		$arResult["ITEMS"] = $obCache->GetVars();
	}
	elseif($obCache->StartDataCache())
	{
		for($i = 1; $i < 16; $i++)
		{
			if(!empty($arParams["CITY_$i"]))
			{
				$obCity = CityTable::getById($arParams["CITY_$i"]);
				if($arCity = $obCity->Fetch())
				{
					if($arCity["ID"] != $arResult["CITY_MAIN"]["ID"])
					{
						$arCountry = CountryTable::getById($arCity["COUNTRY_ID"])->Fetch();

						$GeoIP = new Geoip;
						$coordinates = $GeoIP->getYMapsCoords($arCountry["RU_NAME"].", ".$arCity["NAME"]);

						if(!empty($coordinates))
						{
							$arCity["LATITUDE"] = $coordinates["LATITUDE"];
							$arCity["LONGITUDE"] = $coordinates["LONGITUDE"];
						}

						$arResult["ITEMS"][$i-1] = $arCity;
					}
				}
			}
		}

		$obCache->EndDataCache($arResult["ITEMS"]);
	}

	if(!empty($arResult["CITY_MAIN"]))
	{

		array_unshift($arResult["ITEMS"], $arResult["CITY_MAIN"]);

		if(count($arResult["ITEMS"]) > 15)
		{
			unset($arResult["ITEMS"][count($arResult["ITEMS"])-1]);
		}
	}

	if($arParams["FIRST_HIT_POPUP"] == "Y" && empty($confirm))
	{
		$arResult["CONFIRM_POPUP"] = "Y";
	}

	$this->IncludeComponentTemplate();
}
?>