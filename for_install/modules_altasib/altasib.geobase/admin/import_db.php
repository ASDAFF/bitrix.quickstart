<? define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '3')
	return false;


$_REQUEST['sessid'] = $sessid;
if(!check_bitrix_sessid('sessid') && !IsIE())
	return false;

if($_REQUEST["action"] == "import_csv" || $action == "import_csv")
{
	if($_REQUEST["dst"] == "kladr" || $dst == "kladr")
	{
		if ($incMod == '0') {
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geobase/classes/".$DBType."/import.php");
			if (class_exists('CAltasibGeoBaseImport'))
				CAltasibGeoBaseImport::InitImportKladr();
		} else
			if (class_exists('CAltasibGeoBaseImport'))
				echo CAltasibGeoBaseImport::InitImportKladr();
	}
	else if($_REQUEST["dst"] == "maxmind" || $dst == "maxmind")
	{
		if ($incMod == '0') {
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.geobase/classes/".$DBType."/import.php");
			if (class_exists('CAltasibGeoBaseImport'))
				CAltasibGeoBaseImport::InitImportMM();
		} else
			if (class_exists('CAltasibGeoBaseImport'))
				echo CAltasibGeoBaseImport::InitImportMM();
	}
} else
	echo "Error";
?>