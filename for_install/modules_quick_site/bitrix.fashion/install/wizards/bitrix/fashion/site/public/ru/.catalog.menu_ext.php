<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"fashion:menu.sections",
	"",
	Array(
		"IS_SEF" => "Y",
		"SEF_BASE_URL" => SITE_DIR."catalog/",
		"SECTION_PAGE_URL" => "#SECTION_CODE#/",
		"DETAIL_PAGE_URL" => "#SECTION_CODE#/#ELEMENT_CODE#",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
		"DEPTH_LEVEL" => "2",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
false
);

$aMenuLinks[] = array(
	"Бренды",
	SITE_DIR."brands/",
	array(),
	array(
		"IS_PARENT" => true,
		"DEPTH_LEVEL" => "1"
	)
);

CModule::IncludeModule("iblock");

$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC", "VALUE"=>"ASC"), Array("IBLOCK_ID"=>"#CATALOG_MODELS_IBLOCK_ID#", "CODE"=>"fil_models_brand"));
while($enum_fields = $property_enums->GetNext()){
	$elems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => "#CATALOG_BRANDS_IBLOCK_ID#", "PROPERTY_xmlcode" => $enum_fields["XML_ID"]), false, false);
	if($elem = $elems->GetNext()){}
	else
	{
		$elem["NAME"] = $enum_fields["VALUE"];
		$elem["DETAIL_PAGE_URL"] = SITE_DIR."brands/".$enum_fields["ID"]."/";
	}

	$elem["COUNT"] = CIBlockElement::GetList(array(), array("IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#", "ACTIVE" => "Y", "PROPERTY_fil_models_brand" => $enum_fields["ID"]), array());

	$aMenuLinks[] = array(
		$elem["NAME"],
		$elem["DETAIL_PAGE_URL"],
		array(),
		array(
			"IS_PARENT"=>false,
			"DEPTH_LEVEL"=>"2",
			"CNT" => $elem["COUNT"],
			"SELECTED" => strlen(strstr($APPLICATION->GetCurUri(), $elem["DETAIL_PAGE_URL"]))>0  ? true : false
		)
	);
	strlen(strstr($APPLICATION->GetCurUri(), $elem["DETAIL_PAGE_URL"]))>0 ? $aMenuLinks[0][3]["SELECTED"] = true : "" ;
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>