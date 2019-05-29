<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_realty'] = array(
	"CODE" => 'avito_realty',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "Category",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CATEGORY"),
            "TYPE" => 'const',
			"REQUIRED" => 'Y',
            'CONTVALUE_TRUE' => GetMessage('ACRIT_EXPORTPRO_AVITO_REALTY_CATEGORY_VALUE')
		),
		array(
			"CODE" => "DateBegin",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DATEBEGIN"),
		),
		array(
			"CODE" => "DateEnd",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DATEEND"),
		),
        array(
			"CODE" => "OperationType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OPERATIONTYPE"),
			"REQUIRED" => 'Y',
		),
        array(
            "CODE" => "ObjectType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OBJECTTYPE"),
        ),
        array(
            "CODE" => "ObjectSubtype",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_OBJECTSUBTYPE"),
        ),
        array(
            "CODE" => "NewDevelopmentId",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_NEWDEVELOPMENTID"),
        ),
        array(
            "CODE" => "CadastralNumber",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CADASTRAL_NUMBER"),
        ),
        array(
			"CODE" => "Region",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_REGION"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "City",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CITY"),
			"REQUIRED" => 'Y',
		),
        array(
            "CODE" => "Latitude",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LATITUDE"),
        ),
        array(
            "CODE" => "Longitude",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LONGTITUDE"),
        ),
        array(
            "CODE" => "DistanceToCity",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DISTANCETOCITY"),
        ),
        array(
            "CODE" => "LandArea",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LANDAREA"),
        ),
        array(
			"CODE" => "Street",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_STREET"),
		),
        array(
			"CODE" => "Rooms",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ROOMS"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Square",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SQUARE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "Floor",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_FLOOR"),
		),
        array(
			"CODE" => "Floors",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_FLOORS"),
		),
        array(
			"CODE" => "HouseType",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_HOUSETYPE"),
		),
        array(
            "CODE" => "MarketType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_MARKETTYPE"),
            "REQUIRED" => 'Y',
        ),
        array(
            "CODE" => "WallsType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_WALLSTYPE"),
        ),
        array(
            "CODE" => "Secured",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SECURED"),
        ),
        array(
            "CODE" => "BuildingClass",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_BUILDINGCLASS"),
        ),
        array(
            "CODE" => "LeaseType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASETYPE"),
        ),
        array(
            "CODE" => "LeaseCommission",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMMISSION"),
        ),
        array(
            "CODE" => "LeaseCommissionSize",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMMISSIONSIZE"),
        ),
        array(
            "CODE" => "LeaseDeposit",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEDEPOSIT"),
        ),
        array(
            "CODE" => "LeaseBeds",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEBEDS"),
        ),
        array(
            "CODE" => "LeaseSleepingPlaces",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASESLEEPINGPLACES"),
        ),
        array(
            "CODE" => "LeaseMultimediaOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEMULTIMEDIAOPTION"),
        ),
        array(
            "CODE" => "LeaseAppliancesOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEAPPLIANCESOPTION"),
        ),
        array(
            "CODE" => "LeaseComfortOption",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASECOMFORTOPTION"),
        ),
        array(
			"CODE" => "LeaseAdditionallyOption",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_LEASEADDITIONALLYOPTION"),
		),
        array(
			"CODE" => "Subway",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_SUBWAY"),
		),
        array(
            "CODE" => "Title",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_TITLE"),
        ),
        array(
			"CODE" => "Description",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
            "CODE" => "PriceType",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_PRICETYPE"),
        ),
        array(
            "CODE" => "CompanyName",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_COMPANYNAME"),
        ),
        array(
            "CODE" => "ManagerName",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_MANAGERNAME"),
        ),
        array(
            "CODE" => "EMail",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_EMAIL"),
        ),
        array(
            "CODE" => "AllowEmail",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ALLOWEMAIL"),
        ),
        array(
			"CODE" => "ContactPhone",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_CONTACTPHONE"),
		),
        array(
			"CODE" => "AdStatus",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_ADSTATUS"),
		),
        array(
			"CODE" => "Image",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_IMAGE"),
		),
        array(
            "CODE" => "VideoURL",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_VIDEOURL"),
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
    
    $profileTypes['avito_realty']["FIELDS"][38] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_REALTY_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_realty']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_PORTAL_REQUIREMENTS' );
$profileTypes['avito_realty']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_PORTAL_VALIDATOR' );
$profileTypes['avito_realty']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_REALTY_EXAMPLE');

$profileTypes['avito_realty']['CURRENCIES'] = "";

$profileTypes['avito_realty']['SECTIONS'] = "";

$profileTypes['avito_realty']['ITEMS_FORMAT'] = "
<Ad>
    <Id>#Id#</Id>
    <Category>#Category#</Category>
    <OperationType>#OperationType#</OperationType>
    <ObjectType>#ObjectType#</ObjectType>
    <ObjectSubtype>#ObjectSubtype#</ObjectSubtype>
    <BuildingClass>#BuildingClass#</BuildingClass>
    <Secured>#Secured#</Secured>
    <LeaseType>#LeaseType#</LeaseType>
    <LeaseCommission>#LeaseCommission#</LeaseCommission>
    <LeaseCommissionSize>#LeaseCommissionSize#</LeaseCommissionSize>
    <LeaseDeposit>#LeaseDeposit#</LeaseDeposit>
    <LeaseBeds>#LeaseBeds#</LeaseBeds>
    <LeaseSleepingPlaces>#LeaseSleepingPlaces#</LeaseSleepingPlaces>
    <LeaseMultimedia>
        <Option>#LeaseMultimediaOption#</Option>
    </LeaseMultimedia>
    <LeaseAppliances>
        <Option>#LeaseAppliancesOption#</Option>
    </LeaseAppliances>
    <LeaseComfort>
        <Option>#LeaseComfortOption#</Option>
    </LeaseComfort>
    <LeaseAdditionally>
        <Option>#LeaseAdditionallyOption#</Option>
    </LeaseAdditionally>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <Region>#Region#</Region>
    <City>#City#</City>
    <Street>#Street#</Street>
    <Subway>#Subway#</Subway>
    <DistanceToCity>#DistanceToCity#</DistanceToCity>
    <Latitude>#Latitude#</Latitude>
    <Longitude>#Longitude#</Longitude>
    <Description>#Description#</Description>
    <Price>#Price#</Price>
    <PriceType>#PriceType#</PriceType>
    <CompanyName>#CompanyName#</CompanyName>
    <ManagerName>#ManagerName#</ManagerName>
    <EMail>#EMail#</EMail>
    <AllowEmail>#AllowEmail#</AllowEmail>
    <ContactPhone>#ContactPhone#</ContactPhone>
    <Images>
        <Image url=\"#SITE_URL##Image#\"></Image>
    </Images>
    <VideoURL>#VideoURL#</VideoURL>
    <AdStatus>#AdStatus#</AdStatus>
    <Rooms>#Rooms#</Rooms>
    <Square>#Square#</Square>
    <Title>#Title#</Title>
    <LandArea>#LandArea#</LandArea>
    <Floor>#Floor#</Floor>
    <Floors>#Floors#</Floors>
    <WallsType>#WallsType#</WallsType>
    <HouseType>#HouseType#</HouseType>
    <MarketType>#MarketType#</MarketType>
    <NewDevelopmentId>#NewDevelopmentId#</NewDevelopmentId>
    <CadastralNumber>#CadastralNumber#</CadastralNumber>
</Ad>
";
    
$profileTypes['avito_realty']['LOCATION'] = array(
	'avito' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);