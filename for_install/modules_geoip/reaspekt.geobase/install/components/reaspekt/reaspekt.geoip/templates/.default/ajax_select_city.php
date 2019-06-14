<?
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$module_id = "reaspekt.geobase";

if (!CModule::IncludeModule($module_id)) {
    ShowError("Error! Module no install");
	return;
}

$request = Application::getInstance()->getContext()->getRequest();

$strCITY_ID = htmlspecialchars(trim($request->getPost("CITY_ID")));

$resultCity["STATUS"] = "N";

if(strlen($strCITY_ID) >= 2){
    $resultCity = ReaspGeoIP::SetCityManual($strCITY_ID);
}

echo json_encode($resultCity);