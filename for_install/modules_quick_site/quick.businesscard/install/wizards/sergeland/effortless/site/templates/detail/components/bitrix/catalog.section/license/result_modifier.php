<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (empty($arResult["ITEMS"]))
	return;
		
if(!function_exists("fuzzbot_sort_section_asc"))
{
	function fuzzbot_sort_section_asc($a, $b)
	{
			return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
	}
}

if(!function_exists("fuzzbot_sort_margin_left_asc"))
{
	function fuzzbot_sort_margin_left_asc($a, $b)
	{
			return ($a["LEFT_MARGIN"] < $b["LEFT_MARGIN"]) ? -1 : 1;
	}
}

$arFilter = array(
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"CNT_ACTIVE" => "Y",
);

$arResult["SECTION"] = false;
$arResult["~SECTION"] = array();

if($arParams["SECTION_ID"]>0)
{
	$arFilter["ID"] = $arParams["SECTION_ID"];
	$rsSections = CIBlockSection::GetList(array(), $arFilter);
	$rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);
	$arResult["SECTION"] = $rsSections->GetNext();
}
elseif('' != $arParams["SECTION_CODE"])
{
	$arFilter["=CODE"] = $arParams["SECTION_CODE"];
	$rsSections = CIBlockSection::GetList(array(), $arFilter);
	$rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);
	$arResult["SECTION"] = $rsSections->GetNext();
}

if(is_array($arResult["SECTION"]))
{
	unset($arFilter["ID"]);
	unset($arFilter["=CODE"]);
	
	$arFilter["LEFT_MARGIN"]=$arResult["SECTION"]["LEFT_MARGIN"]+1;
	$arFilter["RIGHT_MARGIN"]=$arResult["SECTION"]["RIGHT_MARGIN"];		
}

$rsSections = CIBlockSection::GetList(array(), $arFilter);
$rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);

while($arSection = $rsSections->GetNext())
	$arResult["~SECTION"][$arSection["ID"]] = $arSection;

if(!is_array($arResult["SECTION"]))
{
	$arUsort = array();
	$top_parent = 0;
	
	usort($arResult["~SECTION"], "fuzzbot_sort_margin_left_asc");
	foreach($arResult["~SECTION"] as $arItem)
	{
		$arUsort[$arItem["ID"]] =  $arItem;		
		if($arItem["DEPTH_LEVEL"] == 1)
			$top_parent = $arItem["ID"];
		else $arUsort[$arItem["ID"]]["TOP_PARENT"] = $top_parent;
	}		
	$arResult["~SECTION"] = $arUsort;
}

foreach($arResult["ITEMS"] as $arItem)
{
    if(!array_key_exists($arItem["~IBLOCK_SECTION_ID"], $arResult["~SECTION"]) && $arItem["~IBLOCK_SECTION_ID"] > 0 && !is_array($arResult["SECTION"]))
		continue;

	$key = 0;	
	if(array_key_exists($arItem["~IBLOCK_SECTION_ID"], $arResult["~SECTION"]))
		$key = $arResult["~SECTION"][$arItem["~IBLOCK_SECTION_ID"]]["TOP_PARENT"] > 0 ? $arResult["~SECTION"][$arItem["~IBLOCK_SECTION_ID"]]["TOP_PARENT"] : $arResult["~SECTION"][$arItem["~IBLOCK_SECTION_ID"]]["ID"];
		
	//$arResult["~ITEMS"][$key]["COUNT"]++;	
	//if($arResult["~ITEMS"][$key]["COUNT"] > $arParams["LINE_ELEMENT_COUNT"] && !empty($arResult["~SECTION"])) continue;

	$arResult["~ITEMS"][$key]["IBLOCK_ID"] 			= $arResult["~SECTION"][$key]["IBLOCK_ID"];
	$arResult["~ITEMS"][$key]["IBLOCK_SECTION_ID"] 	= $arResult["~SECTION"][$key]["ID"];
	$arResult["~ITEMS"][$key]["SORT"] 				= $arResult["~SECTION"][$key]["SORT"] > 0 ?  $arResult["~SECTION"][$key]["SORT"] : 0;
	$arResult["~ITEMS"][$key]["NAME"] 				= $arResult["~SECTION"][$key]["NAME"];
	$arResult["~ITEMS"][$key]["DESCRIPTION"] 		= $arResult["~SECTION"][$key]["DESCRIPTION"];	
	$arResult["~ITEMS"][$key]["PATH"] 				= $arResult["~SECTION"][$key];
	
	$arResult["~ITEMS"][$key]["ITEMS"][] = $arItem;
}

// ОТОБРАЖЕНИЕ ПУСТЫХ РАЗДЕЛОВ
/*foreach($arResult["~SECTION"] as &$arItem)
	if(!empty($arResult["~ITEMS"][$arItem["ID"]]["ITEMS"]))
		 $arItem["ITEMS"] = $arResult["~ITEMS"][$arItem["ID"]]["ITEMS"];
	else $arItem["ITEMS"] = array();
$arResult["~ITEMS"] = $arResult["~SECTION"];*/
// END

if(!empty($arResult["~ITEMS"]))
	usort($arResult["~ITEMS"], "fuzzbot_sort_section_asc");	
?>