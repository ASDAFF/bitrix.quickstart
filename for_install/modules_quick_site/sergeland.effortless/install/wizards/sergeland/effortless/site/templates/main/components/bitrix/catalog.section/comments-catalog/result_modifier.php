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

foreach($arResult["ITEMS"] as $key => &$arItem)	
	$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat("d-m-Y H:i", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));

//if (defined("BX_COMP_MANAGED_CACHE") && is_object($GLOBALS["CACHE_MANAGER"]))
//{  
//   $cp = &$this->__component;
//   if(strlen($cp->getCachePath())) 
//      $GLOBALS['CACHE_MANAGER']->RegisterTag("iblock_articles");
//}    
?>