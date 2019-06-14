<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['avito_general'] = array(
	"CODE" => 'avito_general',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "Id",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "Category",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_CATEGORY"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "DateBegin",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DATEBEGIN"),
		),
		array(
			"CODE" => "DateEnd",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DATEEND"),
		),
		array(
			"CODE" => "Region",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_REGION"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "City",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_CITY"),
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "Subway",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_SUBWAY"),
		),
        array(
			"CODE" => "Description",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "Price",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "ContactPhone",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_CONTACTPHONE"),
		),
        array(
			"CODE" => "AdStatus",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_ADSTATUS"),
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
    
    $profileTypes['avito_general']["FIELDS"][8] = array(
        "CODE" => "Price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_AVITO_GENERAL_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['avito_general']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_PORTAL_REQUIREMENTS' );
$profileTypes['avito_general']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_PORTAL_VALIDATOR' );
$profileTypes['avito_general']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_AVITO_GENERAL_EXAMPLE');

$profileTypes['avito_general']['CURRENCIES'] = "";

$profileTypes['avito_general']['SECTIONS'] = "";

$profileTypes['avito_general']['ITEMS_FORMAT'] = "
<Ad>
    <Id>#Id#</Id>
    <Category>#Category#</Category>
    <DateBegin>#DateBegin#</DateBegin>
    <DateEnd>#DateEnd#</DateEnd>
    <Region>#Region#</Region>
    <City>#City#</City>
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
    
$profileTypes['avito_general']['LOCATION'] = array(
	'yandex' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_AVITO"),
		'sub' => array(
		)
	),
);