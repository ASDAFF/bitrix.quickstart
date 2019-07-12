<?
function rip_tags($string) { 

	// ----- remove HTML TAGs ----- 
	$string = preg_replace ('/<[^>]*>/', ' ', $string); 
	
	// ----- remove control characters ----- 
	$string = str_replace("\r", '', $string);    // --- replace with empty space
	$string = str_replace("\n", ' ', $string);   // --- replace with space
	$string = str_replace("\t", ' ', $string);   // --- replace with space
	
	// ----- remove multiple spaces ----- 
	$string = trim(preg_replace('/ {2,}/', ' ', $string));
	
	return $string; 

}
if($arResult["ITEMS"]){
	foreach($arResult["ITEMS"] as $arItem){
		if($arItem["PROPERTIES"]["BANNER_SIZE"]["VALUE_XML_ID"]){
			$arResult["OTHER_BANNERS_VIEW"]="Y";
		}
	}
}
?>