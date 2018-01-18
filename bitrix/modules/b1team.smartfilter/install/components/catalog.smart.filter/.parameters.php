<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if (!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$rsIBlock = CIBlock::GetList(array(
	"sort" => "asc",
), array(
	"TYPE" => $arCurrentValues["IBLOCK_TYPE"],
	"ACTIVE" => "Y",
));
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arPrice = array();
if (CModule::IncludeModule("catalog"))
{
	$rsPrice = CCatalogGroup::GetList($v1 = "sort", $v2 = "asc");
	while ($arr = $rsPrice->Fetch())
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
}

//получим свойства торговых предложений
$arProperty_Offers = array();
if (intval($arCurrentValues['IBLOCK_ID']) > 0) {

    //получим инфоблок SKU
    $arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues['IBLOCK_ID']);
    $OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;

    if($OFFERS_IBLOCK_ID) {
        //получим свойтсва SKU
        $rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$OFFERS_IBLOCK_ID));
        while($arr=$rsProp->Fetch()) {
            if($arr["PROPERTY_TYPE"] != "F") {
                $arProperty_Offers[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
            }
        }
    }
}

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("CP_BCSF_PRICES"),
		),
            "OFFERS" => array(
			"NAME" => GetMessage("GROUP_OFFERS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSF_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSF_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSF_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"FILTER_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BCSF_FILTER_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "arrFilter",
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("CP_BCSF_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"CACHE_TIME" => array(
			"DEFAULT" => 36000000,
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BCSF_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SAVE_IN_SESSION" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_BCSF_SAVE_IN_SESSION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
            
                //
                // Торговые предложения
                //
                "OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "OFFERS",
			"NAME" => GetMessage("CP_BCSF_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
		),
            
                //
                // SEF
                //
            
                "IS_SEF" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSF_IS_SEF"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"SEF_BASE_URL" => array(
			"PARENT" => "BASE",
			"NAME"=>GetMessage("CP_BCSF_SEF_BASE_URL"),
			"TYPE"=>"STRING",
			"DEFAULT"=>'/catalog/',
		),
		"SECTION_PAGE_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_PAGE_URL",
			GetMessage("CP_BCSF_SECTION_PAGE_URL"),
			"#SECTION_ID#/",
			"BASE"
		),
	),
);
if(empty($arPrice))
	unset($arComponentParameters["PARAMETERS"]["PRICE_CODE"]);

if($arCurrentValues["IS_SEF"] === "Y"){
    unset($arComponentParameters["PARAMETERS"]["SECTION_ID"]);
}
else {
    unset($arComponentParameters["PARAMETERS"]["SEF_BASE_URL"]);
    unset($arComponentParameters["PARAMETERS"]["SECTION_PAGE_URL"]);
}
?>