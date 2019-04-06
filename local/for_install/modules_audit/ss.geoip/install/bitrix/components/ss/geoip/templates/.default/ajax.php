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
		echo "SetCookie";
	}
}
elseif($_REQUEST["SS_AJAX"] == "Y" && !empty($request))
{
	if(strlen($_REQUEST["SS_AJAX_SEARCH_REQUEST"]) >= 2)
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

			$reqLen = strlen($request);
			$arCity["NAME"] = "<strong>".substr($arCity["NAME"], 0, $reqLen)."</strong>".substr($arCity["NAME"], $reqLen);

			$arCityResult = $arCity["NAME"].", ".$arCity["REGION"].", ".$country;
			$cityArray .= "<div data-id='{$arCity["ID"]}' class='sec-autocomplete-line'>$arCityResult</div>\r\n";
		}

		echo $cityArray;
	}
}
?>