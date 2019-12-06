<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (empty($arResult["ITEMS"]))
	return;

if(!function_exists(sergeland_number_ending))
{
	function sergeland_number_ending($number, $ending0, $ending1, $ending2) { 
	   $num100 = $number % 100; 
	   $num10 = $number % 10; 
	   if ($num100 >= 5 && $num100 <= 20) { 
		  return $ending0; 
	   } else if ($num10 == 0) { 
		  return $ending0; 
	   } else if ($num10 == 1) { 
		  return $ending1; 
	   } else if ($num10 >= 2 && $num10 <= 4) { 
		  return $ending2; 
	   } else if ($num10 >= 5 && $num10 <= 9) { 
		  return $ending0; 
	   } else { 
		  return $ending2; 
	   } 
	}
}	
	
$arSections = array();
$rsSections = CIBlockSection::GetList(array(), array("ACTIVE" => "Y","GLOBAL_ACTIVE" => "Y","IBLOCK_ID" => $arParams["IBLOCK_ID"],"CNT_ACTIVE" => "Y"), false,array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL" ));
while($ar_result = $rsSections->GetNext())
	$arSections[$ar_result["ID"]] = $ar_result;

foreach ($arResult["ITEMS"] as $key => &$arItem)
{		
	$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d F Y", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	//$arItem["~DISPLAY_ACTIVE_FROM"]["DD"] = CIBlockFormatProperties::DateFormat("d", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));		
	//$arItem["~DISPLAY_ACTIVE_FROM"]["MM"] = CIBlockFormatProperties::DateFormat("M", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	
	$arItem["IBLOCK_SECTION"] = $arSections[$arItem["IBLOCK_SECTION_ID"]];
	$arItem["COMMENT"]["COUNT"] = 0;
	
	$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arParams["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "PROPERTY_".$arParams["LINK_PROPERTY_SID"] => $arItem["ID"]));
	while($res->Fetch())
	{
		$arItem["COMMENT"]["COUNT"]++;	
		if($arItem["COMMENT"]["COUNT"] == $arParams["LINK_PAGE_ELEMENT_COUNT"])
			break;		
	}
		
	$arItem["COMMENT"]["TEXT"] =  sergeland_number_ending($arItem["COMMENT"]["COUNT"], GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENT1"), GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENT2"), GetMessage("SERGELAND_EFFORTLESS_ARTICLES_COMMENT3"));
}

//if (defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]))
//{  
//   $cp = &$this->__component;
//   if(strlen($cp->getCachePath())) 
//      $GLOBALS['CACHE_MANAGER']->RegisterTag("iblock_articles");
//}    
?>