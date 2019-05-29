<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_importreal'] = array(
	"CODE" => 'avito_importreal',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "Category",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CATEGORY"),
            "REQUIRED" => 'Y',
            "TYPE" => 'const',
            "CONTVALUE_TRUE" => GetMessage('ACRIT_EXPORTPRO_AVITO_IMPORTREAL_CATEGORY_VALUE'),
		),
		array(
			"CODE" => "DateBegin",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DATEBEGIN"),
		),
		array(
			"CODE" => "DateEnd",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DATEEND"),
		),
        array(
			"CODE" => "OperationType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_OPERATIONTYPE"),
			"REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Region",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_REGION"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "City",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CITY"),
			"REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Country",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_COUNTRY"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "Locality",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_LOCALITY"),
		),
        array(
			"CODE" => "ObjectType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_OBJECTTYPE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Square",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_SQUARE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Rooms",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ROOMS"),
		),
        array(
			"CODE" => "LeaseType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_LEASETYPE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Description",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "ContactPhone",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_CONTACTPHONE"),
		),
        array(
			"CODE" => "AdStatus",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_ADSTATUS"),
		),
        array(
			"CODE" => "Image",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_IMAGE"),
		),
	),
	"FORMAT" => '<?xml version="1.0"?>
<Ads target="Avito.ru" formatVersion="1">
    #ITEMS#
</Ads>',
    
	"DATEFORMAT" => "Y-m-d",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['avito_importreal']["FIELDS"][14] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_IMPORTREAL_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_importreal']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_PORTAL_REQUIREMENTS' );
$profileTypes['avito_importreal']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_PORTAL_VALIDATOR' );
$profileTypes['avito_importreal']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_IMPORTREAL_EXAMPLE');

$profileTypes['avito_importreal']['CURRENCIES'] = "";

$profileTypes['avito_importreal']['SECTIONS'] = "";

$profileTypes['avito_importreal']['ITEMS_FORMAT'] = "
<Ad>
    <Id>#Id#</Id>
    <Category>#Category#</Category>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <OperationType>#OperationType#</OperationType>
    <Region>#Region#</Region>
    <City>#City#</City>
    <Country>#Country#</Country>
    <Locality>#Locality#</Locality>
    <Rooms>#Rooms#</Rooms>
    <Square>#Square#</Square>
    <ObjectType>#ObjectType#</ObjectType>
    <LeaseType>#LeaseType#</LeaseType>
    <Description>#Description#</Description>
    <Price>#Price#</Price>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <AdStatus>#AdStatus#</AdStatus>
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
</Ad>
";
    
$profileTypes['avito_importreal']['LOCATION'] = array(
	'avito' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);