<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CModule::IncludeModule("iblock");

$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID"=>"#CATALOG_MODELS_IBLOCK_ID#", "CODE"=>"fil_models_brand"));
while($enum_fields = $property_enums->GetNext())
{
    $elems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => "#CATALOG_BRANDS_IBLOCK_ID#", "PROPERTY_xmlcode" => $enum_fields["XML_ID"]), false, false);
    if($elem = $elems->GetNext()){}
    else
    {
        $elem["NAME"] = $enum_fields["VALUE"];
        $elem["DETAIL_PAGE_URL"] = SITE_DIR."brands/".$enum_fields["ID"]."/";
    }
    $aMenuLinksExt[] = array(
        $elem["NAME"],
        $elem["DETAIL_PAGE_URL"],
        array(),
        array(
            "IS_PARENT"=>false,
            "DEPTH_LEVEL"=>"2",
            "BRAND_ID" => $enum_fields["ID"],
            "BRAND_URL" => $elem["DETAIL_PAGE_URL"]
        )
    );
}


$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);

?>