<?
$reaspekt_geobase_default_option = array(
	"reaspekt_set_local_sql" => "not_using",
	"reaspekt_set_timeout" => "3",
	"reaspekt_get_update" => "N",
	"reaspekt_city_manual_default" => "",
	"reaspekt_enable_jquery" => "Y",
    "reaspekt_elib_site_code" => "",
);

$arDefaultCity = array(
    "1283",
    "2097",
    "2287",
    "2732",
    "2012",
    "1956",
    "2910",
    "2096",
    "2284",
    "1235",
    "2644",
    "908",
);


if(CModule::IncludeModule("reaspekt.geobase")) {
    $statusDB = ReaspGeoIP::StatusTabelDB();
    
    if ($statusDB) {
        $reaspekt_geobase_default_option["reaspekt_set_local_sql"] = "local_db";
        $reaspekt_geobase_default_option["reaspekt_get_update"] = "Y";
        
        $arCityData = ReaspGeoIP::SelectCityXmlIdArray($arDefaultCity);
        
        $arCity = array();
        foreach ($arCityData as $arField) {
            $arCity[] = $arField["ID"];
        }
        
        if (!empty($arCity)) {
            $serializeCity = serialize($arCity);
            
            $reaspekt_geobase_default_option["reaspekt_city_manual_default"] = $serializeCity;
        }
    }
}