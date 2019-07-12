<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("advertising") && !empty($arResult["BANNER_PROPERTIES"])){
	$arBannerList = array();
	$arBannerList[ $arResult["BANNER_PROPERTIES"]["ID"] ] = str_replace("<a", "<a data-id='".$arResult["BANNER_PROPERTIES"]["ID"]."' class='item active'", $arResult["BANNER"]);
	$arBannerProps = array();
	$arBannerProps[] = $arResult["BANNER_PROPERTIES"];
	for ($i=0; $i < 5; $i++) { 
		 $arBanner = CAdvBanner::GetRandom($arParams["TYPE"]);
		 if(!empty($arBanner)){
		 	CAdvBanner::FixShow(array(
			  "FIX_SHOW" => "Y",
			  "ID" => $arBanner["ID"]
			));
		 	$HTML = CAdvBanner::GetHTML($arBanner);
		 	$arBannerProps[] = $arBanner;
		 	$arBannerList[ $arBanner["ID"] ] = str_replace("<a", "<a data-id='".$arBanner["ID"]."' class='item'", $HTML);
		 }
	}
	if(!function_exists('sortByWeight')){
		require_once($_SERVER["DOCUMENT_ROOT"].$this->GetFolder()."/function.php");
	}
	$arBannerProps = sortByWeight($arBannerProps);
	foreach ($arBannerProps as $key => $banner) {
		$arResult["banners"][] = $arBannerList[ $banner["ID"] ];
	}
	$arResult["COUNT"] = count($arBannerList);
}
?>