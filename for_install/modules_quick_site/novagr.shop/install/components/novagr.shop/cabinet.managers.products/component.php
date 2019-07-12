<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arResult = array();

CModule::IncludeModule("iblock");

global $USER;

$arSelect = array( 'ID', 'IBLOCK_SECTION_ID', 'NAME');

$arFilter= array("IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"]);

$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);

$arResult['COUNT_PRODUCT_ALL'] = $rsElement->SelectedRowsCount();
while ($data = $rsElement->Fetch()) {
	//deb($data);
	$arResult['SECTIONS'][$data["IBLOCK_SECTION_ID"]]["COUNT"] += 1;
}



if (!empty($_REQUEST['iNumPage'])) $arParams['iNumPage'] = (int)$_REQUEST['iNumPage'];
if ($arParams['iNumPage'] < 1) $arParams['iNumPage'] = 1;
if (!empty($_REQUEST['nPageSize'])) $arParams['nPageSize'] = (int)$_REQUEST['nPageSize'];
if ($arParams['nPageSize'] == 0) $arParams['nPageSize'] = 20	;

$arNavStartParams = array('nPageSize' => $arParams['nPageSize'], 'iNumPage' => $arParams['iNumPage']);

$arFilter = array();	
$arSelect = array(
		'ID',
		'NAME',
		"ACTIVE",
		"CODE",
		'IBLOCK_SECTION_ID',
		'CATALOG_GROUP_1',
		'PROPERTY_PHOTO_COLOR_1'
);

if (!empty($_REQUEST["section_code"])) {
	$arFilter["SECTION_CODE"] = $_REQUEST["section_code"];
	$arFilter["INCLUDE_SUBSECTIONS"] = "Y";		
} 
if (!empty($arParams["SECTION_ID"])) $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];

$arFilter["IBLOCK_ID"] = $arParams["PRODUCT_IBLOCK_ID"];

	
$arElements = array();
	

$rsElement = CIBlockElement::GetList(array('ID' => "DESC"), $arFilter, false, $arNavStartParams, $arSelect);
//echo $rsElement->SelectedRowsCount();
$arResult["NAV_STRING"] = $rsElement -> GetPageNavStringEx($navComponentObject, "", "dealers_cabinet");

$sectionIDS = array();

// get the code base price
$basePrice = CCatalogGroup::GetBaseGroup();

$nameBasePrice = $basePrice["NAME"];

$arResult["BASE_PRICE_CODE"] = $basePrice["NAME"];

$arParams["PRICE_CODE"] = array($arResult["BASE_PRICE_CODE"]);
//This function returns array with prices description and access rights
//in case catalog module n/a prices get values from element properties
$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["PRODUCT_IBLOCK_ID"], $arParams["PRICE_CODE"]);

$arResult["CAT_PRICES"] = $arResultPrices;
$arConvertParams = array();

while ($data = $rsElement -> Fetch())
{
	
	$arParams["OFFERS_FIELD_CODE"] = array("NAME");
	//$arParams["OFFERS_PROPERTY_CODE"] = array("STD_SIZE");
	$arOffers = CIBlockPriceTools::GetOffersArray(
			$arParams["PRODUCT_IBLOCK_ID"]
			,array($data["ID"])
			,array(
					$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
					"ID" => "DESC",
			)
			,$arParams["OFFERS_FIELD_CODE"]
			,$arParams["OFFERS_PROPERTY_CODE"]
			,0
			,$arResult["CAT_PRICES"]
			,1
			,$arConvertParams
	);
	//deb($arOffers);
	$firstOfferFlag = true;
	foreach($arOffers as $arOffer)
	{
		//deb($arOffers);
		if ($arOffer["PRICES"][$nameBasePrice]["VALUE_NOVAT"] > 0 && $firstOfferFlag == true) {
			$minPrice = $arOffer["PRICES"][$nameBasePrice]["VALUE_NOVAT"];
				
			$firstOfferFlag = false;

		} elseif ($arOffer["PRICES"][$nameBasePrice]["VALUE_NOVAT"] > 0 && $arOffer["PRICES"][$nameBasePrice]["DISCOUNT_VALUE_NOVAT"] < $minPrice) {
			$minPrice = $arOffer["PRICES"][$nameBasePrice]["VALUE_NOVAT"];
		}
	}	
	
	if (!in_array($data["IBLOCK_SECTION_ID"], $sectionIDS)) {
		$sectionIDS[] = $data["IBLOCK_SECTION_ID"];			
	}
	
	$arElements[ $data['ID'] ] = $data;
	$arElements[ $data['ID'] ]['PRICE'] = number_format($minPrice, 0, ".", " ");
	//$arElements[ $data['ID'] ]['PRICE'] = number_format($data['CATALOG_PRICE_1'], 0, ".", " ");
	
}
//deb($sectionIDS);
$arResult["SECTION_CODES"] = array();
if (count($sectionIDS)>0) {
	$arSelect = array( 'ID', 'NAME', 'CODE' );
	$arFilter = array( "IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"], "ID" => $sectionIDS);
	$rsSection = CIBlockSection::GetList(Array("ID"=>"ASC"), $arFilter, false, $arSelect);
	//echo "vv ".$rsSection->SelectedRowsCount();
	while ($data = $rsSection -> Fetch())
	{	
		$arResult["SECTION_CODES"][$data["ID"]] =  $data["CODE"];
		
	}
}

$arResult['ELEMENTS'] = $arElements;
			


$this->IncludeComponentTemplate();
?>