<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(CModule::IncludeModule("advertising") && !empty($arResult["BANNER_PROPERTIES"])){
	$arBannerList = array();
	if(strpos($arResult["BANNER"], "<a") !== false){
		$arResult["BANNER"] = str_replace("<img", "<img class='img-responsive'", $arResult["BANNER"]);
		$arBannerList[ $arResult["BANNER_PROPERTIES"]["ID"] ] = str_replace("<a", "<a class='col-sm-6 col-xs-12'", $arResult["BANNER"]);
	}else{
		$arResult["BANNER"] = str_replace("<img", "<span class='col-sm-6 col-xs-12'><img class='img-responsive'", $arResult["BANNER"]);
		$arBannerList[ $arResult["BANNER_PROPERTIES"]["ID"] ] = $arResult["BANNER"]."</span>";
	}
	$arBannerProps = array();
	$arBannerProps[] = $arResult["BANNER_PROPERTIES"];
	for ($i=0; $i < 1; $i++) { 
		 $arBanner = CAdvBanner::GetRandom($arParams["TYPE"]);
		 if(!empty($arBanner)){
		 	CAdvBanner::FixShow(array(
			  "FIX_SHOW" => "Y",
			  "ID" => $arBanner["ID"]
			));
		 	$HTML = CAdvBanner::GetHTML($arBanner);
		 	if(strpos($HTML, "<a") !== false){
				$HTML = str_replace("<img", "<img class='img-responsive'", $HTML);
				$arBannerProps[] = $arBanner;
				$arBannerList[ $arBanner["ID"] ] = str_replace("<a", "<a class='col-sm-6 col-xs-12'", $HTML);
			}else{
				$HTML = str_replace("<img", "<span class='col-sm-6 col-xs-12'><img class='img-responsive'", $HTML);
				$arBannerProps[] = $arBanner;
				$arBannerList[ $arBanner["ID"] ] = $HTML."</span>";
			}
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