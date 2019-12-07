<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if(!function_exists(number_ending))
{
	function number_ending($number, $ending0, $ending1, $ending2) { 
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
global $MESS; @include(__DIR__.'/lang/'.LANGUAGE_ID.'/template.php');

foreach($arResult['SECTIONS'] as &$arSection)
	$arSection["NUMBER"]["TEXT"] = number_ending($arSection['ELEMENT_CNT'], GetMessage("QUICK_BUSINESSCARD_NUMBER1"), GetMessage("QUICK_BUSINESSCARD_NUMBER2"), GetMessage("QUICK_BUSINESSCARD_NUMBER3"));

?>