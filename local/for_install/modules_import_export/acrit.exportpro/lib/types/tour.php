<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['ym_tour'] = array(
	"CODE" => 'ym_tour',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_AVAILABLE"),
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
            'CONTVALUE_TRUE' => 'true',
            'CONTVALUE_FALSE' => 'false',
		),
		array(
			"CODE" => "BID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_BID"),
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PICTURE"),
		),
         array(
			"CODE" => "STORE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_STORE"),
		),
        array(
			"CODE" => "PICKUP",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PICKUP"),
		),
        array(
			"CODE" => "DELIVERY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DELIVERY"),
		),
        array(
			"CODE" => "WORLDREGION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_WORLDREGION"),
		),
        array(
			"CODE" => "COUNTRY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_COUNTRY"),
		),
		array(
			"CODE" => "REGION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_REGION"),
		),
		array(
			"CODE" => "DAYS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DAYS"),
            'REQUIRED' => 'Y',
		),
        array(
			"CODE" => "DATAtOUR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DATATOUR"),
		),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_NAME"),
            'REQUIRED' => 'Y',
            'VALUE' => 'NAME',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "HOTEL_STARS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_HOTELSTARS"),
		),
        array(
			"CODE" => "ROOM",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_ROOM"),
		),
        array(
			"CODE" => "MEAL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_MEAL"),
		),
        array(
			"CODE" => "INCLUDED",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_INCLUDED"),
            'REQUIRED' => 'Y',
		),
        array(
			"CODE" => "TRANSPORT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_TRANSPORT"),
            'REQUIRED' => 'Y',
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "AGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_AGE"),
		),
        array(
			"CODE" => "PRICE_MIN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICEMIN"),
		),
        array(
			"CODE" => "PRICE_MAX",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICEMAX"),
		),
        array(
			"CODE" => "OPTIONS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_OPTIONS"),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="#DATE#">
    <shop>
        <name>#SHOP_NAME#</name>
        <company>#COMPANY_NAME#</company>
        <url>#SITE_URL#</url>
        <currencies>#CURRENCY#</currencies>
        <categories>#CATEGORY#</categories>
        <offers>
            #ITEMS#
        </offers>
    </shop>
</yml_catalog>',
    
	"DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['ym_tour']["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_TOUR_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['ym_tour']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_PORTAL_REQUIREMENTS' );
$profileTypes['ym_tour']['PORTAL_VALIDATOR'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_PORTAL_VALIDATOR' );
$profileTypes['ym_tour']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_MARKET_TOUR_EXAMPLE');

$profileTypes['ym_tour']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['ym_tour']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['ym_tour']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" type=\"tour\" available=\"#AVAILABLE#\" bid=\"#BID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <market_category>#MARKET_CATEGORY#</market_category>
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    <delivery>#DELIVERY#</delivery>
    <worldRegion>#WORLDREGION#</worldRegion>
    <country>#COUNTRY#</country>
    <region>#REGION#</region>
    <days>#DAYS#</days>
    <dataTour>#DATAtOUR#</dataTour>
    <name>#NAME#</name>
    <hotel_stars>#HOTEL_STARS#</hotel_stars>
    <room>#ROOM#</room>
    <meal>#MEAL#</meal>
    <included>#INCLUDED#</included>
    <transport>#TRANSPORT#</transport>
    <description>#DESCRIPTION#</description>
    <age>#AGE#</age>
    <price_min>#PRICE_MIN#</price_min>
    <price_max>#PRICE_MAX#</price_max>
    <options>#OPTIONS#</options>
</offer>
";
    
$profileTypes['ym_tour']['LOCATION'] = array(
	'yandex' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_ANDEKS"),
		'sub' => array(
			'market' => array(
				'name' => GetMessage("ACRIT_EXPORTPRO_VEBMASTER"),
				'sub' => '',
			)
		)
	),
);