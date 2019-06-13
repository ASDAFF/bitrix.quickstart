<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ebay_2"] = array(
	"CODE" => "ebay_2",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2_NAME" ),
	"DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
	"REG" => "",
	"HELP" => "",
	"FIELDS" => array(
		array(
			"CODE" => "SKU",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2_FIELD_SKU" ),
            "VALUE" => "ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
        array(
			"CODE" => "PRICE",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2_FIELD_PRICE" ),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "QUANTITY",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2_FIELD_QUANTITY" ),
			"VALUE" => "CATALOG-QUANTITY",
            "REQUIRED" => "Y",
            "TYPE" => "field",
		),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<InventoryArray>
    #ITEMS#
</InventoryArray>',
    "ENCODING" => "utf8",
	"DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["ebay_2"]["FIELDS"][1] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ebay_2"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_EBAY_2_PORTAL_REQUIREMENTS" );
$profileTypes["ebay_2"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_EBAY_2_EXAMPLE" );

$profileTypes["ebay_2"]["CURRENCIES"] = "";

$profileTypes["ebay_2"]["SECTIONS"] = "";

$profileTypes["ebay_2"]["ITEMS_FORMAT"] = "
<Inventory>
	<SKU>#SKU#</SKU>
    <Price>#PRICE#</Price>
    <Quantity>#QUANTITY#</Quantity>
</Inventory>
";
    
$profileTypes["ebay_2"]["LOCATION"] = array(
	"ebay_2" => array(
		"name" => GetMessage( "ACRIT_EXPORTPRO_EBAY_2" ),
		"sub" => array(
		)
	),
);