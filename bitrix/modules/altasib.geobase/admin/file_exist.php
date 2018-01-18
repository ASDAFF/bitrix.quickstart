<? define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$response = array("MAXMIND" => 0, "IPGEOBASE" => 0);

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0')
	return false;
elseif ($incMod == '3')
	return false;
else{
	$response["IPGEOBASE"] =
		(CAltasibGeoBaseIP::CheckServiceAccess('http://ipgeobase.ru/files/db/Main/geo_files.tar.gz') ? 1 : 0);
	$response["MAXMIND"] = (CAltasibGeoBaseIP::CheckServiceAccess(
		'http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz') ? 1 : 0);

	echo json_encode($response, JSON_FORCE_OBJECT);
}
?>