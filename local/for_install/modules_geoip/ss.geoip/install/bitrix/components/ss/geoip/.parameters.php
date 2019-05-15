<?
use Ss\Geoip\CityTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(CModule::IncludeModule('ss.geoip'))
{
	$obCity = CityTable::getList(array(
		'order' => array('NAME' => 'ASC'),
		"filter" => array("!NAME" => NULL)
	));
	$arCityList = array();
	while($arCity = $obCity->Fetch())
	{
		$arCityList[$arCity["ID"]] = $arCity["NAME"];
	}

	for($i = 1; $i < 16; $i++)
	{
		$obCityDefault = CityTable::getList(array(
			"filter" => array("NAME" => GetMessage("SS_GEOIP_CITY_{$i}_DEFAULT"))
		));
		if($arCityDefault = $obCityDefault->Fetch())
		{
			$arCityDefaultList[$i] = $arCityDefault["ID"];
		}
	}
}

$arComponentParameters = array(
	"GROUPS" => array(
		"GEOIP" => array(
			"NAME" => GetMessage("SS_GEOIP_GROUP_NAME"),
			"SORT" => "200"
		),
		"GEOIP_CITY" => array(
			"NAME" => GetMessage("SS_GEOIP_CITY_GROUP_NAME"),
			"SORT" => "300"
		)
	),
	"PARAMETERS" => array(
		"SCHEME_COLOR" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SS_GEOIP_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "#00A650",
		),
		"FIRST_HIT_POPUP" => Array(
			"PARENT" => "GEOIP",
			"NAME" => GetMessage("SS_GEOIP_FIRST_HIT_POPUP"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"CONNECT_JQUERY" => Array(
			"PARENT" => "GEOIP",
			"NAME" => GetMessage("SS_GEOIP_CONNECT_JQUERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		),
		"CONNECT_YMAP_API" => Array(
			"PARENT" => "GEOIP",
			"NAME" => GetMessage("SS_GEOIP_CONNECT_YMAP_API"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y"
		)
	)
);

for($i = 1; $i < 16; $i++)
{
	$arComponentParameters["PARAMETERS"]["CITY_$i"] = array(
		"PARENT" => "GEOIP_CITY",
		"NAME" => GetMessage("SS_GEOIP_CITY_$i").GetMessage("SS_GEOIP_CITY_TEXT"),
		"TYPE" => "LIST",
		"DEFAULT" => $arCityDefaultList[$i],
		"VALUES" => $arCityList
	);
}
?>