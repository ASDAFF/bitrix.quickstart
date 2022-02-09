<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALL"));
	return;
}

if (!CBXFeatures::IsFeatureEnabled('CatMultiStore'))
{
	ShowError(GetMessage("CAT_FEATURE_NOT_ALLOW"));
	return;
}

$arDefaultUrlTemplates404 = array(
	"liststores" => "index.php",
	"element" => "#store_id#",
);
$arDefaultUrlTemplatesN404 = array(
	"liststores" => "",
	"element" => "store_id=#store_id#",
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
$arComponentVariables = array("store_id");

$sefFolder = "/store/";
$arUrlTemplates = array();

if ($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$arUrlTemplates =
		CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = CComponentEngine::ParseComponentPath($arParams["SEF_FOLDER"], $arUrlTemplates, $arVariables);

	if (StrLen($componentPage) <= 0)
		$componentPage = "liststores";

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables,	$arVariableAliases,	$arVariables);

	foreach ($arUrlTemplates as $url => $value)
		$arResult["PATH_TO_".strtoupper($url)] = $arParams["SEF_FOLDER"].$value;

	$sefFolder = $arParams["SEF_FOLDER"];
}
else
{
	$arVariables = array();
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	foreach ($arDefaultUrlTemplatesN404 as $url => $value)
		$arResult["PATH_TO_".strtoupper($url)] = $GLOBALS["APPLICATION"]->GetCurPageParam($value, $arComponentVariables);

	$componentPage = "";
	if (IntVal($arVariables["store_id"]) > 0)
		$componentPage = "element";
	else
		$componentPage = "liststores";
}
$arResult = array_merge(
	array(
		"FOLDER" => $sefFolder,
		"URL_TEMPLATES" => $arUrlTemplates,
		"STORE" => $arVariables["store_id"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
	), $arResult);

$this->IncludeComponentTemplate($componentPage);
?>