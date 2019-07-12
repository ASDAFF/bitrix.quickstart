<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function aszCatalogResolveComponentEngine(CComponentEngine $engine, $pageCandidates, &$arVariables)
{

	/** @global CMain $APPLICATION */
	global $APPLICATION, $CACHE_MANAGER;
	$component = $engine->GetComponent();
	if ($component)
		$iblock_id = intval($component->arParams["IBLOCK_ID"]);
	else
		$iblock_id = 0;

	$requestURL = $APPLICATION->GetCurPage(true);

	$cacheId = $requestURL.implode("|", array_keys($pageCandidates))."|".SITE_ID;
	$cache = new CPHPCache;
	if ($cache->startDataCache(3600, $cacheId, "iblock_find"))
	{
		if (defined("BX_COMP_MANAGED_CACHE"))
		{
			$CACHE_MANAGER->StartTagCache("iblock_find");
			CIBlock::registerWithTagCache($iblock_id);
		}
		foreach ($pageCandidates as $pageID => $arVariablesTmp)
		{
			if ($arVariablesTmp["FILTER_ID"] != ""){
			
				if($arVariablesTmp["SECTION_CODE_PATH"] != "" && (!$arVariablesTmp["SECTION_CODE"] != "" || !$arVariablesTmp["SECTION_ID"])){
					$arVariablesTmp["SECTION_CODE"] = preg_replace('/^(.*?)([^\/]+)$/is','$2',$arVariablesTmp["SECTION_CODE_PATH"]);
				}
			
				$arVariables = $arVariablesTmp;
				if (defined("BX_COMP_MANAGED_CACHE"))
					$CACHE_MANAGER->EndTagCache();
				$cache->endDataCache(array($pageID, $arVariablesTmp));
				return $pageID;
			}
		}
		
		foreach ($pageCandidates as $pageID => $arVariablesTmp)
		{
			if (
				$arVariablesTmp["SECTION_CODE_PATH"] != ""
				&& (isset($arVariablesTmp["ELEMENT_ID"]) || isset($arVariablesTmp["ELEMENT_CODE"]))
			)
			{
				if (CIBlockFindTools::checkElement($iblock_id, $arVariablesTmp))
				{
					$arVariables = $arVariablesTmp;
					if (defined("BX_COMP_MANAGED_CACHE"))
						$CACHE_MANAGER->EndTagCache();
					$cache->endDataCache(array($pageID, $arVariablesTmp));
					return $pageID;
				}
			}
		}

		foreach ($pageCandidates as $pageID => $arVariablesTmp)
		{
			if (
				$arVariablesTmp["SECTION_CODE_PATH"] != ""
				&& (!isset($arVariablesTmp["ELEMENT_ID"]) && !isset($arVariablesTmp["ELEMENT_CODE"]))
			)
			{
				if (CIBlockFindTools::checkSection($iblock_id, $arVariablesTmp))
				{
					$arVariables = $arVariablesTmp;
					if (defined("BX_COMP_MANAGED_CACHE"))
						$CACHE_MANAGER->EndTagCache();
					$cache->endDataCache(array($pageID, $arVariablesTmp));
					return $pageID;
				}
			}
		}

		if (defined("BX_COMP_MANAGED_CACHE"))
			$CACHE_MANAGER->AbortTagCache();
		$cache->abortDataCache();
	}
	else
	{
		$vars = $cache->getVars();
		$pageID = $vars[0];
		$arVariables = $vars[1];
		return $pageID;
	}

	list($pageID, $arVariables) = each($pageCandidates);
	return $pageID;
}

$arDefaultUrlTemplates404 = array(
	"sections" => "",
	"section" => "#SECTION_ID#/",
	"element" => "#SECTION_ID#/#ELEMENT_ID#/",
	"filtersection" => "#SECTION_CODE_PATH#/_filter_#FILTER_ID#/",
	"filter" => "filter_#FILTER_ID#/"
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
	"SECTION_ID",
	"SECTION_CODE",
	"ELEMENT_ID",
	"ELEMENT_CODE",
	"FILTER_ID"
);

if($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$engine = new CComponentEngine($this);
	if (CModule::IncludeModule('iblock'))
	{
		if($arParams["USE_FILTER_SUPER"]=="Y"){
			$engine->addGreedyPart("#SECTION_CODE_PATH#");
			$engine->addGreedyPart("#FILTER_ID#");
			$engine->setResolveCallback("aszCatalogResolveComponentEngine");
		}else{
			$engine->addGreedyPart("#SECTION_CODE_PATH#");
			$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
		}
	}
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = $engine->guessComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);
	
	if($componentPage=="filter") $componentPage = "section";
	if($componentPage=="filtersection") $componentPage = "section";
	
	if(!$componentPage && isset($_REQUEST["q"]))
		$componentPage = "search";

	$b404 = false;
	if(!$componentPage)
	{
		$componentPage = "sections";
		$b404 = true;
	}

	if($componentPage == "section")
	{
		if (isset($arVariables["SECTION_ID"]))
			$b404 |= (intval($arVariables["SECTION_ID"])."" !== $arVariables["SECTION_ID"]);
		else
			$b404 |= !isset($arVariables["SECTION_CODE"]);
	}

	if($b404 && $arParams["SET_STATUS_404"]==="Y")
	{
		$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
		if ($folder404 != "/")
			$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
		if (substr($folder404, -1) == "/")
			$folder404 .= "index.php";

			if($folder404 != $APPLICATION->GetCurPage(true))
			CHTTP::SetStatus("404 Not Found");
	}

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	$arResult = array(
		"FOLDER" => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}
else
{
	$arVariables = array();

	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = "";

	if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		$componentPage = "section";
	elseif(isset($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
		$componentPage = "section";
	elseif(isset($_REQUEST["q"]))
		$componentPage = "search";
	else
		$componentPage = "sections";

	$arResult = array(
		"FOLDER" => "",
		"URL_TEMPLATES" => Array(
			"section" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#",
			"element" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"."&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#",
		),
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}

$this->IncludeComponentTemplate($componentPage);

?>