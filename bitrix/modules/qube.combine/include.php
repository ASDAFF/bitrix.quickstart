<?
global $APPLICATION;
IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule("sale"))
{
	$APPLICATION->ThrowException(GetMessage('QC_ERROR_SALE_NOT_INSTALLED'));
	return false;
}
if (!CModule::IncludeModule('catalog'))
{
	$APPLICATION->ThrowException(GetMessage('QC_ERROR_CATALOG_NOT_INSTALLED'));
	return false;
}
define("QUBE_COMBINE_MODULE_ID", 'qube.combine');

CModule::AddAutoloadClasses(
	"qube.combine",
	array(
		"CQubeCombine" => "classes/general/main.php",
	)
);
?>
