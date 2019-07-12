<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!empty($arResult["ELEMENTS"])){

	if(!CModule::IncludeModule("iblock")){
		return;
	}

	include(__DIR__."/functions.php");

	$rsCatalogIblock = CIBlock::GetList(array(),array("TYPE" => "catalog", "SITE_ID" => SITE_ID, "ACTIVE" => "Y"));
	if($arCatalogIblock = $rsCatalogIblock -> Fetch()){
		$rsLinkedElements = CIBLockElement::GetList(array(), array("IBLOCK_ID" => $arCatalogIblock["ID"], "PROPERTY_SPECIALOFFER" => $arResult["ELEMENTS"]), array("PROPERTY_SPECIALOFFER"));
		while($arLinkedElement = $rsLinkedElements -> GetNext()){
			$arResult["LINKED_ELEMENTS"][ $arLinkedElement["PROPERTY_SPECIALOFFER_VALUE"] ] = array( "CNT" => $arLinkedElement["CNT"], "STRING" => CUtils::plural_form($arLinkedElement["CNT"], array(GetMessage("OFFER_1"), GetMessage("OFFER_2"), GetMessage("OFFER_MANY"))));
		}
	}
}
?>