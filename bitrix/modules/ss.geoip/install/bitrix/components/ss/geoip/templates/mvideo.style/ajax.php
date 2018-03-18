<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Ss\Geoip\Geoip;
use Ss\Geoip\CityTable;
use Ss\Geoip\CountryTable;

CModule::IncludeModule("ss.geoip");

$request = trim($_REQUEST["SS_AJAX_SEARCH_REQUEST"]);

if (!defined('BX_UTF')) {
	$request = iconv('utf-8', 'windows-1251', $request);
}

if($_REQUEST["SS_AJAX"] == "Y" && !empty($_REQUEST["SS_AJAX_SITY_ID"]))
{
	$confirm = $APPLICATION->get_cookie("SS_GEO_IP_CONFIRM");
	if(empty($confirm))
	{
		$APPLICATION->set_cookie("SS_GEO_IP_CONFIRM", "Y");
	}

	$GeoIP = new Geoip;
	$setCookie = $GeoIP->SetCookie($_REQUEST["SS_AJAX_SITY_ID"]);
	if($setCookie)
	{
		$info = $GeoIP->GetInfo();
		echo '{"LONGITUDE":'.$info["LONGITUDE"].', "LATITUDE":'.$info["LATITUDE"].'}';
	}
}
elseif($_REQUEST["SS_AJAX"] == "Y" && !empty($request))
{
	if(strlen($_REQUEST["SS_AJAX_SEARCH_REQUEST"]) >= 2)
	{
		$obCache = new CPHPCache;
		if($obCache->InitCache(86400*7, md5(serialize($request)), "/ss_geoip/ajax"))
		{
			$vars = $obCache->GetVars();
   			extract($vars);
		}
		elseif($obCache->StartDataCache())
		{
			$obCity = CityTable::getList(array(
				'order' => array(
					'NAME' => 'ASC'
				),
				"filter" => array(
					"NAME" => $request."%"
				),
				"limit" => 10
			));

			$cityArray = "";
			while($arCity = $obCity->Fetch())
			{
				if(!empty($arCity["COUNTRY_ID"]))
				{
					$obCountry = CountryTable::getList(array(
						"filter" => array(
							"ID" => $arCity["COUNTRY_ID"],
						)
					));
					if($arCountry = $obCountry->Fetch())
					{
						$country = $arCountry["RU_NAME"];
					}
				}

				$GeoIP = new Geoip;
				$coordinates = $GeoIP->getYMapsCoords($country.", ".$arCity["NAME"]);

				$reqLen = strlen($request);
				$arCity["NAME"] = "<strong>".substr($arCity["NAME"], 0, $reqLen)."</strong>".substr($arCity["NAME"], $reqLen);

				$arCityResult = $arCity["NAME"].", ".$arCity["REGION"].", ".$country;
				$cityArray .= "<div class='sec-autocomplete-line' data-id='{$arCity["ID"]}' onclick='SetCoordinate({$coordinates["LONGITUDE"]}, {$coordinates["LATITUDE"]})'>$arCityResult</div>\r\n";
			}

			$obCache->EndDataCache(array('cityArray' => $cityArray));
		}

		echo $cityArray;
	}
}
?>