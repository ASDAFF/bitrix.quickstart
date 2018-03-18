<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$arDefaultUrlTemplates404 = array(
	"list" => "index.php",
	"detail" => "order_detail.php?ID=#ID#",
	"cancel" => "order_cancel.php?ID=#ID#",
// UnitellerPlugin add
	'check' => 'order_check.php?ID=#ID#',
// /UnitellerPlugin add
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();
// UnitellerPlugin change
$arComponentVariables = array("CANCEL", "COPY_ORDER", "ID", 'CHECK');
// /UnitellerPlugin change
$componentPage = "";
$arVariables = array();

if ($arParams["SEF_MODE"] == "Y")
{
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = CComponentEngine::ParseComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	
	foreach ($arUrlTemplates as $url => $value)
		$arResult["PATH_TO_".strToUpper($url)] = $arParams["SEF_FOLDER"].$value;

	if ($componentPage == "cancel" || $_REQUEST["CANCEL"]=="Y")
		$componentPage = "cancel";
	elseif ($componentPage == "detail")
		$componentPage = "detail";
// UnitellerPlugin add
	elseif ($componentPage == 'check') {
		$componentPage = 'check';
	}
// /UnitellerPlugin add
	else
		$componentPage = "list";

	$arResult = array_merge(
			Array(
				"SEF_FOLDER" => $arParams["SEF_FOLDER"], 
				"URL_TEMPLATES" => $arUrlTemplates, 
				"VARIABLES" => $arVariables, 
				"ALIASES" => $arVariableAliases,
			),
			$arResult
		);
}
else
{
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);
	
	if ($_REQUEST["CANCEL"]=="Y")
		$componentPage = "cancel";
	elseif (IntVal($arVariables["ID"]) > 0 && $_REQUEST["COPY_ORDER"]!="Y")
		$componentPage = "detail";
// UnitellerPlugin add
	elseif ((int)$arVariables['ID'] > 0 && $_REQUEST['CHECK'] != 'Y') {
		$componentPage = 'check';
	}
// /UnitellerPlugin add
	else
		$componentPage = "list";

	$arResult = array(
			"VARIABLES" => $arVariables, 
			"ALIASES" => $arVariableAliases
		);
}

$this->IncludeComponentTemplate($componentPage);
?>