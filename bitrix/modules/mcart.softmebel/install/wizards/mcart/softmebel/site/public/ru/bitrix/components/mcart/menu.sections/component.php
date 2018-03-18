<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//if($GLOBALS["APPLICATION"]->GetShowIncludeAreas() && $USER->IsAdmin())
//	echo "<br />";

if(!isset($arParams["CACHE_TIME"]))
$arParams["CACHE_TIME"] = 3600;
if($arParams["CACHE_TYPE"] == "N" || $arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "N")
$arParams["CACHE_TIME"] = 0;

$arParams["ID"] = intval($arParams["ID"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"]<=0)
$arParams["DEPTH_LEVEL"]=1;

$aMenuLinksNew = array();

$CACHE_ID = "v0.1".__FILE__.$arParams["IBLOCK_ID"].$arParams["DEPTH_LEVEL"];
$obMenuCache = new CPHPCache;
if($obMenuCache->StartDataCache($arParams["CACHE_TIME"], $CACHE_ID, "/".SITE_ID.$this->GetRelativePath()))
{
	$arSections = array();
	$arElementLinks = array();
	if(CModule::IncludeModule("iblock"))
	{
		$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"GLOBAL_ACTIVE"=>"Y",
			"IBLOCK_ACTIVE"=>"Y",
			"<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
		);
		$arOrder = array(
			"left_margin"=>"asc",
		);

		$rsSections = CIBlockSection::GetList($arOrder, $arFilter);
		if($arParams["IS_SEF"] !== "Y")
		$rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);
		else
		$rsSections->SetUrlTemplates("", $arParams["SEF_BASE_URL"].$arParams["SECTION_PAGE_URL"]);
		while($arSection = $rsSections->GetNext())
		{
			$arSections[] = array(
				"ID" => $arSection["ID"],
				"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
				"~NAME" => $arSection["~NAME"],
				"~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
			);
			$arElementLinks[$arSection["ID"]] = array();
		}
	}
	$obMenuCache->EndDataCache(Array("arSections" => $arSections, "arElementLinks"=>$arElementLinks));
}
else
{
	$arVars = $obMenuCache->GetVars();
	$arSections = $arVars["arSections"];
	$arElementLinks = $arVars["arElementLinks"];
}

if(CModule::IncludeModule("iblock"))
{
	//In "SEF" mode we'll try to parse URL and get ELEMENT_ID from it
	if($arParams["IS_SEF"] === "Y")
	{
		$componentPage = CComponentEngine::ParseComponentPath(
		$arParams["SEF_BASE_URL"],
		array(
				"section" => $arParams["SECTION_PAGE_URL"],
				"detail" => $arParams["DETAIL_PAGE_URL"],
		),
		$arVariables
		);
		if($componentPage === "detail")
		{
			CComponentEngine::InitComponentVariables(
			$componentPage,
			array("SECTION_ID", "ELEMENT_ID"),
			array(
					"section" => array("SECTION_ID" => "SECTION_ID"),
					"detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
			),
			$arVariables
			);
			$arParams["ID"] = intval($arVariables["ELEMENT_ID"]);
		}
	}

	if(($arParams["ID"] > 0) && (intval($arVariables["SECTION_ID"]) <= 0))
	{
		$arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
		$arFilter = array(
			"ID" => $arParams["ID"],
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		);
		$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		if(($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0))
		$rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"].$arParams["DETAIL_PAGE_URL"]);
		while($arElement = $rsElements->GetNext())
		{
			$arElementLinks[$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
		}
	}
}

$menuIndex = 0;
$previousDepthLevel = 1;
foreach($arSections as $arSection)
{
	if ($menuIndex > 0)
	$aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
	$previousDepthLevel = $arSection["DEPTH_LEVEL"];

	$aMenuLinksNew[$menuIndex++] = array(
	$arSection["~NAME"],
	$arSection["~SECTION_PAGE_URL"],
	$arElementLinks[$arSection["ID"]],
	array(
			"FROM_IBLOCK" => true,
			"IS_PARENT" => false,
			"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
			"SECTION_ID" => $arSection["ID"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]	
	)
	);
}

return $aMenuLinksNew;
?>
