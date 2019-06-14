<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_room'] = array(
	"CODE" => 'avito_room',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "Category",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_CATEGORY"),
            "TYPE" => 'const',
			"REQUIRED" => 'Y',
            'CONTVALUE_TRUE' => GetMessage('ACRIT_EXPORTPRO_AVITO_ROOM_CATEGORY_VALUE')
		),
		array(
			"CODE" => "DateBegin",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_DATEBEGIN"),
		),
		array(
			"CODE" => "DateEnd",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_DATEEND"),
		),
        array(
			"CODE" => "OperationType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_OPERATIONTYPE"),
			"REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Region",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_REGION"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "City",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_CITY"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "Locality",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_LOCALITY"),
		),
        array(
			"CODE" => "Street",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_STREET"),
		),
        array(
			"CODE" => "SaleRooms",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_SALEROOMS"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Rooms",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_ROOMS"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Square",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_SQUARE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Floor",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_FLOOR"),
		),
        array(
			"CODE" => "Floors",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_FLOORS"),
		),
        array(
			"CODE" => "HouseType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_HOUSETYPE"),
		),
        array(
			"CODE" => "LeaseType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_LEASETYPE"),
		),
        array(
			"CODE" => "Subway",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_SUBWAY"),
		),
        array(
			"CODE" => "Description",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "ContactPhone",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_CONTACTPHONE"),
		),
        array(
			"CODE" => "AdStatus",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_ADSTATUS"),
		),
        array(
			"CODE" => "Image",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_IMAGE"),
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
    
    $profileTypes['avito_room']["FIELDS"][18] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_ROOM_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_room']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_ROOM_PORTAL_REQUIREMENTS' );
$profileTypes['avito_room']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_ROOM_PORTAL_VALIDATOR' );
$profileTypes['avito_room']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_ROOM_EXAMPLE');

$profileTypes['avito_room']['CURRENCIES'] = "";

$profileTypes['avito_room']['SECTIONS'] = "";

$profileTypes['avito_room']['ITEMS_FORMAT'] = "
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
    <SaleRooms>#SaleRooms#</SaleRooms>
    <Rooms>#Rooms#</Rooms>
    <Square>#Square#</Square>
    <Floor>#Floor#</Floor>
    <Floors>#Floors#</Floors>
    <HouseType>#HouseType#</HouseType>
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
    
$profileTypes['avito_room']['LOCATION'] = array(
	'avito' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);