<?
IncludeModuleLangFile(__FILE__);
global $APPLICATION;
$module_id = $sModuleID = "tcsbank.kupivkredit";
if (!CModule::IncludeModule("sale"))
{
	$GLOBALS["APPLICATION"]->ThrowException(GetMessage("NO_SALE_MODULE"));
	return false;
}
if (!CModule::IncludeModule("catalog"))
{
	$GLOBALS["APPLICATION"]->ThrowException(GetMessage("NO_CATALOG_MODULE"));
	return false;
}

CModule::AddAutoloadClasses(
	$sModuleID,
	array(
		"CTCSBank" => "general/tcsbank.php",
		"CTCSOrder" => "general/tcsorder.php",
		"CTCSExchange" => "general/tcsexchange.php",
		"CTCSOrderAll" => "mysql/tcsorder.php"
	)
);

$obModule = new CTCSBank;
$obTCSOrder = new CTCSOrder;

$arOptions = Array();

$arOptions["OPTIONS"] = $obModule->GetSitesData();

?>