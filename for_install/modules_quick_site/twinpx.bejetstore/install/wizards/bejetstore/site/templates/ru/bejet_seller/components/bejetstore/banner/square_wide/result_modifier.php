<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(!empty($arResult["ITEMS"])){
	$arResult["WIDE_BANNERS"] = array();
	$arResult["SQUARE_BANNERS"] = array();
	foreach ($arResult["ITEMS"] as $key => $arItem) {
		if($arItem["PROPERTIES"]["GROUP"]["VALUE_XML_ID"] == "SQUARE"){
			$arResult["SQUARE_BANNERS"][] = $arItem;
		}elseif($arItem["PROPERTIES"]["GROUP"]["VALUE_XML_ID"] == "WIDE"){
			$arResult["WIDE_BANNERS"][] = $arItem;
		}
	}
}
$this->__component->SetResultCacheKeys(array("SQUARE_BANNERS","WIDE_BANNERS"));
?>