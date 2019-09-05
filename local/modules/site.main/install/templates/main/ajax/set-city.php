<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$CITY_ID = intval($_REQUEST["ID"]);

// устанавливаем город
if($CITY_ID > 0)
	\Site\Main\GeoServices::SetCityToCookie($CITY_ID);
?>