<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!empty($arResult["ELEMENTS"])){

	if(!CModule::IncludeModule("iblock")){
		return;
	}

	include(__DIR__."/functions.php");

	$rsCatalogIblock = CIBlock::GetList(array(),array("TYPE" => "catalog", "SITE_ID" => SITE_ID, "ACTIVE" => "Y"));
	if($arCatalogIblock = $rsCatalogIblock -> Fetch()){
		$rsLinkedElements = CIBLockElement::GetList(array(), array("IBLOCK_ID" => $arCatalogIblock["ID"], "PROPERTY_BRAND" => $arResult["ELEMENTS"]), array("PROPERTY_BRAND"));
		while($arLinkedElement = $rsLinkedElements -> GetNext()){
			$arResult["LINKED_ELEMENTS"][ $arLinkedElement["PROPERTY_BRAND_VALUE"] ] = array( "CNT" => $arLinkedElement["CNT"], "STRING" => CUtils::plural_form($arLinkedElement["CNT"], array(GetMessage("OFFER_1"), GetMessage("OFFER_2"), GetMessage("OFFER_MANY"))));
		}
	}

	foreach ($arResult["ITEMS"] as $key => $arItem) {
		$arResult["ITEMS"][ $key ]["DETAIL_PAGE_URL"] = str_replace("#BRAND_CODE#", $arItem["CODE"], $arItem["DETAIL_PAGE_URL"]);
	}
}
?>