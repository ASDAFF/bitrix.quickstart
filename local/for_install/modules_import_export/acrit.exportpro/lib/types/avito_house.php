<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_house'] = array(
	"CODE" => 'avito_house',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "Category",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_CATEGORY"),
            "REQUIRED" => 'Y',
		),
		array(
			"CODE" => "DateBegin",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DATEBEGIN"),
		),
		array(
			"CODE" => "DateEnd",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DATEEND"),
		),
        array(
			"CODE" => "OperationType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_OPERATIONTYPE"),
			"REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Region",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_REGION"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "City",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_CITY"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "Locality",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LOCALITY"),
		),
        array(
			"CODE" => "Street",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_STREET"),
		),
        array(
			"CODE" => "ObjectType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_OBJECTTYPE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Square",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_SQUARE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "LandArea",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LANDAREA"),
		),
        array(
			"CODE" => "DistanceToCity",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DISTANCETOCITY"),
		),
        array(
			"CODE" => "WallsType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_WALLSTYPE"),
		),
        array(
			"CODE" => "LeaseType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_LEASETYPE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Subway",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_SUBWAY"),
		),
        array(
			"CODE" => "Description",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "ContactPhone",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_CONTACTPHONE"),
		),
        array(
			"CODE" => "AdStatus",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_ADSTATUS"),
		),
        array(
			"CODE" => "Image",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_IMAGE"),
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
    
    $profileTypes['avito_house']["FIELDS"][17] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_HOUSE_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_house']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_PORTAL_REQUIREMENTS' );
$profileTypes['avito_house']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_PORTAL_VALIDATOR' );
$profileTypes['avito_house']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_HOUSE_EXAMPLE');

$profileTypes['avito_house']['CURRENCIES'] = "";

$profileTypes['avito_house']['SECTIONS'] = "";

$profileTypes['avito_house']['ITEMS_FORMAT'] = "
<Ad>
    <Id>#Id#</Id>
    <Category>#Category#</Category>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <OperationType>#OperationType#</OperationType>
    <Region>#Region#</Region>
    <City>#City#</City>
    <Locality>#Locality#</Locality>
    <Street>#Street#</Street>
    <Square>#Square#</Square>
    <ObjectType>#ObjectType#</ObjectType>
    <LandArea>#LandArea#</LandArea>
    <DistanceToCity>#DistanceToCity#</DistanceToCity>
    <WallsType>#WallsType#</WallsType>
    <LeaseType>#LeaseType#</LeaseType>
    <Subway>#Subway#</Subway>
    <Description>#Description#</Description>
    <Price>#Price#</Price>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <AdStatus>#AdStatus#</AdStatus>
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
</Ad>
";
    
$profileTypes['avito_house']['LOCATION'] = array(
	'avito' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);