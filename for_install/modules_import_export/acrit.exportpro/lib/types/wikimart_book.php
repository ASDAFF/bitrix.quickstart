<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['wikimart_book'] = array(
	"CODE" => 'wikimart_book',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_AVAILABLE"),
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
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_BID"),
			"VALUE" => "",
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PICTURE"),
		),
         array(
			"CODE" => "STORE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_STORE"),
		),
        array(
			"CODE" => "PICKUP",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PICKUP"),
		),
        array(
			"CODE" => "AUTHOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_AUTHOR"),
		),
		array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_NAME"),
			"VALUE" => "NAME",
            "TYPE" => 'field',
		),
        array(
			"CODE" => "PUBLISHER",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PUBLISHER"),
		),
		array(
			"CODE" => "SERIES",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_SERIES"),
		),
		
		array(
			"CODE" => "YEAR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_YEAR"),
		),
		array(
			"CODE" => "ISBN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_ISBN"),
		),
        array(
			"CODE" => "VOLUME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_VOLUME"),
		),
        array(
			"CODE" => "PART",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PART"),
		),
		array(
			"CODE" => "LANGUAGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_LANGUAGE"),
		),
        array(
			"CODE" => "BINDING",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_BINDING"),
		),
        array(
			"CODE" => "PAGE_EXTENT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PAGEEXTENT"),
		),
        array(
			"CODE" => "TABLE_OF_CONTENTS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_TABLEOFCONTENTS"),
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "AGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_AGE"),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_UTM_CAMPAIGN" ),
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
    
    $profileTypes['wikimart_book']["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_BOOK_FIELD_PRICE"),
        "REQUIRED" => 'Y',
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['wikimart_book']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_WIKIMART_BOOK_EXAMPLE');

$profileTypes['wikimart_book']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' />" . PHP_EOL;

$profileTypes['wikimart_book']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['wikimart_book']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" type=\"book\" available=\"#AVAILABLE#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <local_delivery_cost>#LOCAL_DELIVERY_COST#</local_delivery_cost>
    <author>#AUTHOR#</author>
    <name>#NAME#</name>
    <publisher>#PUBLISHER#</publisher>
    <series>#SERIES#</series>
    <year>#YEAR#</year>
    <ISBN>#ISBN#</ISBN>
    <volume>#VOLUME#</volume>
    <part>#PART#</part>
    <language>#LANGUAGE#</language>
    <binding>#BINDING#</binding>
    <page_extent>#PAGE_EXTENT#</page_extent>
    <description>#DESCRIPTION#</description>
    
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    
    
    
    
    
    
    
    
    
    
    
    <table_of_contents>#TABLE_OF_CONTENTS#</table_of_contents>
    
    <age>#AGE#</age>
</offer>
";
    
$profileTypes['wikimart_book']['LOCATION'] = array(
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