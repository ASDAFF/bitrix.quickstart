<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

CJSCore::Init(array('popup'));
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Function sort
*************************************************************************/

if(!function_exists("sergeland_sort_id_asc"))
{
	function sergeland_sort_id_asc($a, $b)
	{
			return ($a["ID"] < $b["ID"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_id_desc"))
{
	function sergeland_sort_id_desc($a, $b)
	{
			return ($a["ID"] > $b["ID"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_value_enum_id_asc"))
{
	function sergeland_sort_value_enum_id_asc($a, $b)
	{
			return ($a["VALUE_ENUM_ID"] < $b["VALUE_ENUM_ID"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_value_enum_id_desc"))
{
	function sergeland_sort_value_enum_id_desc($a, $b)
	{
			return ($a["VALUE_ENUM_ID"] > $b["VALUE_ENUM_ID"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_sort_asc"))
{
	function sergeland_sort_sort_asc($a, $b)
	{
			return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_sort_desc"))
{
	function sergeland_sort_sort_desc($a, $b)
	{
			return ($a["SORT"] > $b["SORT"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_value_sort_asc"))
{
	function sergeland_sort_value_sort_asc($a, $b)
	{
			return ($a["VALUE_SORT"] < $b["VALUE_SORT"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_value_sort_desc"))
{
	function sergeland_sort_value_sort_desc($a, $b)
	{
			return ($a["VALUE_SORT"] > $b["VALUE_SORT"]) ? -1 : 1;
	}
}

if(!function_exists("sergeland_sort_name_asc"))
{
	function sergeland_sort_name_asc($a, $b)
	{
			return strcmp($a["NAME"], $b["NAME"]);
	}
}

if(!function_exists("sergeland_sort_name_desc"))
{
	function sergeland_sort_name_desc($a, $b)
	{
			if(strcmp($a["NAME"], $b["NAME"]) > 0) return -1;
			if(strcmp($a["NAME"], $b["NAME"]) < 0) return 1;
			
			return strcmp($a["NAME"], $b["NAME"]);
	}
}


/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

if (empty($arParams["SORT_FIELD"]))
	$arParams["SORT_FIELD"] = "sort";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_FIELD_ORDER"]))
	$arParams["SORT_FIELD_ORDER"] = "asc";
	
if (empty($arParams["SORT_VALUE"]))
	$arParams["SORT_VALUE"] = "name";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_VALUE_ORDER"]))
	$arParams["SORT_VALUE_ORDER"] = "asc";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";

if(strlen($arParams["FILTER_NAME2"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME2"]))
	$arrFilter2 = array();
else
{
	global ${$arParams["FILTER_NAME2"]};
	$arrFilter2 = ${$arParams["FILTER_NAME2"]};
	if(!is_array($arrFilter2))
		$arrFilter2 = array();
}	
	
$arParams["FOLDER"]=trim($arParams["FOLDER"]);
if(strlen($arParams["FOLDER"])<=0)
	$arParams["FOLDER"] = SITE_DIR."catalog/";

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

$arParams["SHOW_COUNT_ELEMENT"] = $arParams["SHOW_COUNT_ELEMENT"]=="Y";
$arParams["SHOW_FIELD"] = $arParams["SHOW_FIELD"]=="Y";
$arParams["SHOW_PRICE"] = $arParams["SHOW_PRICE"]=="Y";

if(empty($arParams['HIDE_NOT_AVAILABLE']))
	$arParams['HIDE_NOT_AVAILABLE'] = 'N';
elseif($arParams['HIDE_NOT_AVAILABLE'] != 'Y')
	$arParams['HIDE_NOT_AVAILABLE'] = 'N';	

$arVariables = array();
$arDefaultUrlTemplates404 = array("section" => "#SECTION_ID#/");

$engine = new CComponentEngine($this);
$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, array("section" => $arParams["SEF_URL_SECTION_TEMPLATE"]));	
$componentPage = $engine->guessComponentPath($arParams["FOLDER"], $arUrlTemplates, $arVariables);		

	
/*************************************************************************
			Work with cache
*************************************************************************/
if( $this->StartResultCache( false, array(empty($arrFilter2) ? false : $arrFilter2, (intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]) > 0 ? intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]) : false), (empty($arVariables) ? false : isset($arVariables["SECTION_CODE"]) ? $arVariables["SECTION_CODE"] : $arVariables["SECTION_ID"]), ($arParams["CACHE_GROUPS"]==="N" ? false : $USER->GetGroups())) ) )
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arFilter = array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"IBLOCK_ACTIVE"=>"Y",
		"ACTIVE"=>"Y",
		"GLOBAL_ACTIVE"=>"Y",
	);	

	$bSectionFound = false;		
	if($componentPage == "section")
	{		
		if(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		{
			$arFilter["ID"]=$arVariables["SECTION_ID"];
			$rsSection = CIBlockSection::GetList(Array(), $arFilter);
			$arResult = $rsSection->GetNext();
			if($arResult)
				$bSectionFound = true;
		}
		
		if(isset($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
		{
			$arFilter["=CODE"]=$arVariables["SECTION_CODE"];
			$rsSection = CIBlockSection::GetList(Array(), $arFilter);
			$arResult = $rsSection->GetNext();
			
			if($arResult)
			{
				$bSectionFound = true;				
				$arVariables["SECTION_ID"] = $arResult["ID"];			
			}	
		}
		
		if(!$bSectionFound)
			$this->AbortResultCache();		
	}
	if(intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]) > 0 && !$componentPage)		
	{
		$arFilter["ID"]=intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]);
		$rsSection = CIBlockSection::GetList(Array(), $arFilter);
		$arResult = $rsSection->GetNext();
		if($arResult)
			$bSectionFound = true;
			
		if(!$bSectionFound)
			$this->AbortResultCache();			
	}  

	
	$arResult 	= array();
	$arResult["ITEMS"] = array();
	$arResult["~ITEMS"] = array();
	
	$arFilter = array(
		"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE"=>"Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE"=>"Y",
		"GLOBAL_ACTIVE"=>"Y",
		"CHECK_PERMISSIONS" => "Y",
		"MIN_PERMISSION" => "R",
		"INCLUDE_SUBSECTIONS" => 'Y',		
	);
	
	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"CODE",
		"XML_ID",
		"NAME",
		"ACTIVE",
		"DATE_ACTIVE_FROM",
		"DATE_ACTIVE_TO",
		"SORT",
		"PREVIEW_TEXT",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT",
		"DETAIL_TEXT_TYPE",
		"DATE_CREATE",
		"CREATED_BY",
		"TIMESTAMP_X",
		"MODIFIED_BY",
		"TAGS",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_PICTURE",
		"PREVIEW_PICTURE",
		"PROPERTY_*",
	);	
	
	$arResult["~PRICES"]["SHOW_PRICE"] = $arParams["SHOW_PRICE"];
	
	if($componentPage == "section" && isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0 && $bSectionFound)
		$arFilter["SECTION_ID"] = $arVariables["SECTION_ID"];
		
	if(intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]) > 0 && !$componentPage  && $bSectionFound)
		$arFilter["SECTION_ID"] = intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]);	
		
	//This function returns array with prices description and access rights
	//in case catalog module n/a prices get values from element properties
	if($arParams["SHOW_PRICE"])
		$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);

	foreach( $arResult["PRICES"] as $arPriceID)
		$arSelect[] =  $arPriceID["SELECT"];
	
	$bIBlockCatalog = false;
	$arCatalog = false;
	$bCatalog = CModule::IncludeModule('catalog');
	if ($bCatalog)
	{
		$arCatalog = CCatalog::GetByID($arParams["IBLOCK_ID"]);
		if (!empty($arCatalog) && is_array($arCatalog))
			$bIBlockCatalog = true;
	}
	
	if ($bIBlockCatalog && 'Y' == $arParams['HIDE_NOT_AVAILABLE'])
		$arFilter['CATALOG_AVAILABLE'] = 'Y';
	
	$rsElements = CIBlockElement::GetList(array(), array_merge($arrFilter2, $arFilter), false, false, $arSelect);
	$rsElements->SetUrlTemplates($arParams["FOLDER"]);
	
		
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = $obElement->GetFields();

		if(!empty($arParams["PROPERTY_CODE"]))
			$arItem["PROPERTIES"] = $obElement->GetProperties();

		$section_url_filter = "";	
			
		if($componentPage == "section" && isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
			$section_url_filter = "&".$arParams["FILTER_NAME"]."[SECTION_ID]=".$arVariables["SECTION_ID"];
				
		if(intval($_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"]) > 0)
			$section_url_filter = "&".$arParams["FILTER_NAME"]."[SECTION_ID]=".$_REQUEST[$arParams["FILTER_NAME"]]["SECTION_ID"];
		
		foreach($arrFilter2 as $PROP=>$VALUE)
			$section_url_filter .= "&".$arParams["FILTER_NAME"]."[".urlencode($PROP)."]=".urlencode("$VALUE");
			
		$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);			
				
		foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
		{
			$arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][$arItem["ID"]]["SORT"] = $arItem["PRICES"][$PRICE_CODE]["VALUE"];
			$arResult["~PRICES"]["MINIMUM_PROP_URL"][$PRICE_CODE] = $arItem["DETAIL_PAGE_URL"]."?".$section_url_filter."&".$arParams["FILTER_NAME"]."[~FIELD_NAME]=".urlencode(GetMessage("SERGELAND_FILTER_PRICE"))."&".$arParams["FILTER_NAME"]."[".urlencode(">=CATALOG_PRICE_".$arResult["PRICES"][$PRICE_CODE]["ID"])."]";
			$arResult["~PRICES"]["MAXIMUM_PROP_URL"][$PRICE_CODE] = "&".$arParams["FILTER_NAME"]."[".urlencode("<=CATALOG_PRICE_".$arResult["PRICES"][$PRICE_CODE]["ID"])."]";				
		}
		
		foreach($arParams["PROPERTY_CODE"] as $PROPERTY_CODE)
		{
			if(strlen($arItem["PROPERTIES"][$PROPERTY_CODE]["NAME"]) < 1) continue;	
				
			if(!$arParams["SHOW_FIELD"])
				if( strlen($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]) < 1 && !is_array($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])) continue;			
			
			$arResult["ITEMS"][$PROPERTY_CODE]["ID"]   = $arItem["PROPERTIES"][$PROPERTY_CODE]["ID"];
			$arResult["ITEMS"][$PROPERTY_CODE]["SORT"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["SORT"];
			$arResult["ITEMS"][$PROPERTY_CODE]["NAME"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["NAME"];
			$arResult["ITEMS"][$PROPERTY_CODE]["CODE"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["CODE"];
			$arResult["ITEMS"][$PROPERTY_CODE]["PROPERTY_TYPE"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["PROPERTY_TYPE"];

			if(!is_array($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
			 if(strlen($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]) < 1)
				continue;
				
			if(is_array($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"]))
			{
				foreach($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"] as $VALUE)
				{
					$arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($VALUE)]["COUNT"]++;
					$arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($VALUE)]["NAME"] = $VALUE;
					$arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($VALUE)]["DETAIL_PAGE_URL"] = $arItem["DETAIL_PAGE_URL"]."?".$arParams["FILTER_NAME"]."[PROPERTY_".$PROPERTY_CODE."]=".urlencode($VALUE)."&".$arParams["FILTER_NAME"]."[~FIELD_NAME]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["NAME"]).$section_url_filter;				
				}
			}
			else
			{
				$arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["COUNT"]++;
				$arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["NAME"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"];
				
				if($arItem["PROPERTIES"][$PROPERTY_CODE]["PROPERTY_TYPE"] == "L")
				{
					 $arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["DETAIL_PAGE_URL"] = $arItem["DETAIL_PAGE_URL"]."?".$arParams["FILTER_NAME"]."[PROPERTY_".$PROPERTY_CODE."_VALUE]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])."&".$arParams["FILTER_NAME"]."[~FIELD_NAME]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["NAME"]).$section_url_filter;
					 $arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["VALUE_SORT"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE_SORT"];
					 $arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["VALUE_ENUM_ID"] = $arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE_ENUM_ID"];				
				}
				else $arResult["ITEMS"][$PROPERTY_CODE]["VALUE"][ToUpper($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])]["DETAIL_PAGE_URL"] = $arItem["DETAIL_PAGE_URL"]."?".$arParams["FILTER_NAME"]."[PROPERTY_".$PROPERTY_CODE."]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["VALUE"])."&".$arParams["FILTER_NAME"]."[~FIELD_NAME]=".urlencode($arItem["PROPERTIES"][$PROPERTY_CODE]["NAME"]).$section_url_filter;						
			}			
		}
	}
	
	if($arParams["SHOW_FIELD"] && count($arResult["ITEMS"]) < 1)
	{
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
		while($arProp = $properties->GetNext())
		  if(in_array($arProp["CODE"], $arParams["PROPERTY_CODE"]))
		  {
			$arResult["ITEMS"][$arProp["CODE"]]["ID"]   = $arProp["ID"];
			$arResult["ITEMS"][$arProp["CODE"]]["SORT"] = $arProp["SORT"];
			$arResult["ITEMS"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
			$arResult["ITEMS"][$arProp["CODE"]]["CODE"] = $arProp["CODE"];
			$arResult["ITEMS"][$arProp["CODE"]]["PROPERTY_TYPE"] = $arProp["PROPERTY_TYPE"];			
		  }
		  
		foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
		{
			$arResult["~PRICES"]["MINIMUM_PROP_URL"][$PRICE_CODE] = $arItem["DETAIL_PAGE_URL"]."?".$arParams["FILTER_NAME"]."[".urlencode(">=CATALOG_PRICE_".$arResult["PRICES"][$PRICE_CODE]["ID"])."]";
			$arResult["~PRICES"]["MAXIMUM_PROP_URL"][$PRICE_CODE] = "&".$arParams["FILTER_NAME"]."[".urlencode("<=CATALOG_PRICE_".$arResult["PRICES"][$PRICE_CODE]["ID"])."]";				
		}		  
	}

	switch($arParams["SORT_FIELD"])
	{
		case "name":
					$arParams["SORT_FIELD_ORDER"] == "asc" ?
						 usort($arResult["ITEMS"], "sergeland_sort_name_asc") :
						 usort($arResult["ITEMS"], "sergeland_sort_name_desc");
						 break;
		case "id":
					$arParams["SORT_FIELD_ORDER"] == "asc" ?
						 usort($arResult["ITEMS"], "sergeland_sort_id_asc") :
					     usort($arResult["ITEMS"], "sergeland_sort_id_desc");
						 break;					
		case "sort":
					$arParams["SORT_FIELD_ORDER"] == "asc" ?
						 usort($arResult["ITEMS"], "sergeland_sort_sort_asc") :
						 usort($arResult["ITEMS"], "sergeland_sort_sort_desc");
						 break;
	}
	
	foreach($arResult["ITEMS"] as &$FIELD)
	{
		if(!is_array($FIELD["VALUE"])) $FIELD["VALUE"] = array();		
		if($FIELD["PROPERTY_TYPE"] == "L")
		{
			switch($arParams["SORT_VALUE"])
			{
				case "name":
							$arParams["SORT_VALUE_ORDER"] == "asc" ?
								 usort($FIELD["VALUE"], "sergeland_sort_name_asc") :
								 usort($FIELD["VALUE"], "sergeland_sort_name_desc");
								 break;
				case "id":
							$arParams["SORT_VALUE_ORDER"] == "asc" ?
								 usort($FIELD["VALUE"], "sergeland_sort_value_enum_id_asc") :
								 usort($FIELD["VALUE"], "sergeland_sort_value_enum_id_desc");
								 break;					
				case "sort":
							$arParams["SORT_VALUE_ORDER"] == "asc" ?
								 usort($FIELD["VALUE"], "sergeland_sort_value_sort_asc") :
								 usort($FIELD["VALUE"], "sergeland_sort_value_sort_desc");
								 break;
			}				
		}
		else
		{
			$arParams["SORT_VALUE_ORDER"] == "asc" ?
				usort($FIELD["VALUE"], "sergeland_sort_name_asc") :
				usort($FIELD["VALUE"], "sergeland_sort_name_desc");				
		}

	  $arResult["~ITEMS"][$FIELD["CODE"]] = $FIELD;
	}		

	foreach($arParams["PRICE_CODE"] as $PRICE_CODE)
	{
		if(!is_array($arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE])) $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE] = array();
		usort($arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE], "sergeland_sort_sort_asc");
		
		switch(count($arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE]))
		{
			case 0:
						$arResult["~PRICES"]["MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = 0;						
						break;
					
			case 1:
						$arResult["~PRICES"]["MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][0]["SORT"];
						break;
					
			case 2:
						$arResult["~PRICES"]["MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][0]["SORT"];						
						$arResult["~PRICES"]["MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][1]["SORT"];
						break;
					
			default:
						$arResult["~PRICES"]["MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][0]["SORT"];
						$arResult["~PRICES"]["MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] = $arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE][count($arResult["~PRICES"]["ALL_PRICES"][$PRICE_CODE]) - 1]["SORT"];
		}		
	}
	
	$arResult["ITEMS"] = $arResult["~ITEMS"];	
	$arResult["SHOW_COUNT_ELEMENT"] = $arParams["SHOW_COUNT_ELEMENT"];
	$arResult["NAV_RESULT"] = $rsElements;
	
	$this->SetResultCacheKeys(array(
		"ID",
		"NAME",
		"PATH",
		"IBLOCK_SECTION_ID",
	));

	
	$this->IncludeComponentTemplate();
}
?>