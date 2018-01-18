<?if(isset($_REQUEST["database"]) && $_REQUEST["database"] == "MaxMind")
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geobase/admin/loadmm.php");
else
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geobase/admin/loadfile.php");
?>