<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['ozon'] = array(
	"CODE" => "ozon",
	"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_OZON_DESCR"),
	"REG" => "/",
	"HELP" => "/",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "SELLING_STATE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_AVAILABLE"),
			"VALUE" => "",
            'TYPE' => 'const',
            "CONDITION" => array(
                "CLASS_ID" => "CondGroup",
                "DATA" => array(
                    "All" => "AND",
                    "True" => "True"
                ),
                "CHILDREN" => array(
                    array(
                        "CLASS_ID" => "CondCatQuantity",
                        "DATA" => array(
                                "logic" => "EqGr",
                                "value" => "1"
                        )
                    )
                )
            ),
            'USE_CONDITION' => 'Y',
            'CONTVALUE_TRUE' => 'ForSale',
            'CONTVALUE_FALSE' => 'NotForSale',
		),
        array(
            "CODE" => "SUPPLY_PERIOD",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_SUPPLY_PERIOD"),
        ),
        array(
            "CODE" => "QTY",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_SUPPLY_QTY"),
        ),
		array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_NAME"),
			'VALUE' => 'NAME',
            "TYPE" => 'field',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "MANUFACTURER_IDENTIFIER",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_MANUFACTURER_IDENTIFIER"),
		),
		array(
			"CODE" => "GROSS_WEIGHT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_GROSS_WEIGHT"),
			"REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "INTERNAL_NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_INTERNAL_NAME"),
		),
		array(
			"CODE" => "SELLING_PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_SELLING_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "DISCOUNT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_DISCOUNT"),
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_PICTURE"),
            "VALUE" => "DETAIL_PICTURE",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "IMAGES",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_IMAGES"),
		),
		array(
			"CODE" => "RELEASEYEAR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_RELEASEYEAR"),
		),
		array(
			"CODE" => "PRODUCER_NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_PRODUCER_NAME"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "CAPABILITY_NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_CAPABILITY_NAME"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		/*array(
			"CODE" => "CAPABILITY_TYPE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_CAPABILITY_TYPE"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),*/
		array(
			"CODE" => "CAPABILITY_ANNOTATION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_CAPABILITY_ANNOTATION"),
		),
		array(
			"CODE" => "CAPABILITY_EXTERNALID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_CAPABILITY_CAPABILITY_EXTERNALID"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
            "VALUE" => "ID",
			"TYPE" => 'field',
		),
		/*array(
			"CODE" => "CAPABILITY_WEIGHT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_CAPABILITY_CAPABILITY_WEIGHT"),
		),*/
		array(
			"CODE" => "BRAND_NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_BRAND_NAME"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "BRAND_DIMENSIONS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_BRAND_DIMENSIONS"),
		),
		array(
			"CODE" => "COLOR_NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_COLOR_NAME"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "COLOR_COLOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_COLOR_COLOR"),
            "REQUIRED" => 'Y',
            "DELETE_ONEMPTY" => 'N',
		),
		array(
			"CODE" => "TDIMENSIONS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_TDIMENSIONS"),
		),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<Products>
    #ITEMS#
</Products>',
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['ozon']["FIELDS"][8] = array(
        "CODE" => "SELLING_PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_OZON_FIELD_SELLING_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['ozon']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_OZON_PORTAL_REQUIREMENTS' );
$profileTypes['ozon']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_OZON_PORTAL_VALIDATOR' );
$profileTypes['ozon']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_OZON_EXAMPLE');
$profileTypes['ozon']['SCHEME_OFFER_DESCRIPTION'] = GetMessage('ACRIT_EXPORTPRO_TYPE_OZON_SCHEME_DESCRIPTION');

$profileTypes['ozon']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['ozon']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['ozon']['ITEMS_FORMAT'] = '
<Product MerchantSKU="#ID#" ProductTypeID="#MARKET_CATEGORY#">
    <SKU>
        <Name>#NAME#</Name>
        <ManufacturerIdentifier>#MANUFACTURER_IDENTIFIER#</ManufacturerIdentifier>
        <GrossWeight>#GROSS_WEIGHT#</GrossWeight>
        <InternalName>#INTERNAL_NAME#</InternalName>
    </SKU>
    <Price>
        <SellingPrice>#SELLING_PRICE#</SellingPrice>
        <Discount>#DISCOUNT#</Discount>
    </Price>
    <Availability>
        <SellingState>#SELLING_STATE#</SellingState>
        <SupplyPeriod>#SUPPLY_PERIOD#</SupplyPeriod>
        <Qty>#QTY#</Qty>
    </Availability>
    <Description>
        <Name>#NAME#</Name>
        <Picture>#SITE_URL##PICTURE#</Picture>
        <Images>#SITE_URL##IMAGES#</Images>
        <ReleaseYear>#RELEASEYEAR#</ReleaseYear>
        <Producer>
            <Name>#PRODUCER_NAME#</Name>
        </Producer>
        <Capability>
            <Type>#CAPABILITY_TYPE#</Type>
            <Name>#CAPABILITY_NAME#</Name>
            <Annotation>#CAPABILITY_ANNOTATION#</Annotation>
            <ExternalID>#CAPABILITY_EXTERNALID#</ExternalID>
            <Weight>#CAPABILITY_WEIGHT#</Weight>
        </Capability>
        <Brand>
            <Name>#BRAND_NAME#</Name>
            <Dimensions>#BRAND_DIMENSIONS#</Dimensions>
        </Brand>
        <Color>
            <Name>#COLOR_NAME#</Name>
            <Color>#COLOR_COLOR#</Color>
        </Color>
        <TDimensions>#TDIMENSIONS#</TDimensions>
    </Description>
</Product>';
    
$profileTypes['ozon']['LOCATION'] = array(
    'ozon' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_OZON"),
		'sub' => array(
		)
	),
);