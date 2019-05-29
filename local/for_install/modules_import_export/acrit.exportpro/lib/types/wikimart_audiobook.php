<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['wikimart_audiobook'] = array(
	"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_AVAILABLE"),
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
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_BID"),
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_CATEGORY"),
			"TYPE" => 'field',
            "VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PICTURE"),
		),
        array(
			"CODE" => "AUTHOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_AUTHOR"),
		),
		array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_NAME"),
			"TYPE" => 'field',
            "VALUE" => "NAME",
		),
        array(
			"CODE" => "PUBLISHER",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PUBLISHER"),
		),
		array(
			"CODE" => "SERIES",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_SERIES"),
		),
		
		array(
			"CODE" => "YEAR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_YEAR"),
		),
		array(
			"CODE" => "ISBN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_ISBN"),
		),
        array(
			"CODE" => "VOLUME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_VOLUME"),
		),
        array(
			"CODE" => "PART",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PART"),
		),
		array(
			"CODE" => "LANGUAGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_LANGUAGE"),
		),
        array(
			"CODE" => "TABLE_OF_CONTENTS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_TABLEOFCONTENTS"),
		),
		array(
			"CODE" => "PERFORMED_BY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PERFORMEDBY"),
		),
		array(
			"CODE" => "PERFORMANCE_TYPE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PERFORMANCETYPE"),
		),
        array(
			"CODE" => "STORAGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_STORAGE"),
		),
		array(
			"CODE" => "FORMAT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_FORMAT"),
		),
        array(
			"CODE" => "RECORDING_LENGTH",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_RECORDINGLENGTH"),
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "AGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_AGE"),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_UTM_CAMPAIGN" ),
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
    
    $profileTypes['wikimart_audiobook']["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_AUDIOBOOK_FIELD_PRICE"),
        "REQUIRED" => 'Y',
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['wikimart_audiobook']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_WIKIMART_AUDIOBOOK_EXAMPLE');

$profileTypes['wikimart_audiobook']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['wikimart_audiobook']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['wikimart_audiobook']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" type=\"audiobook\" available=\"#AVAILABLE#\" bid=\"#BID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <market_category>#MARKET_CATEGORY#</market_category>
    <picture>#SITE_URL##PICTURE#</picture>
    <author>#AUTHOR#</author>
    <name>#NAME#</name>
    <publisher>#PUBLISHER#</publisher>
    <series>#SERIES#</series>
    <year>#YEAR#</year>
    <ISBN>#ISBN#</ISBN>
    <volume>#VOLUME#</volume>
    <part>#PART#</part>
    <language>#LANGUAGE#</language>
    <table_of_contents>#TABLE_OF_CONTENTS#</table_of_contents>
    <performed_by>#PERFORMED_BY#</performed_by>
    <performance_type>#PERFORMANCE_TYPE#</performance_type>
    <storage>#STORAGE#</storage>
    <format>#FORMAT#</format>
    <recording_length>#RECORDING_LENGTH#</recording_length>
    <description>#DESCRIPTION#</description>
    <age>#AGE#</age>
</offer>
";
    
$profileTypes['wikimart_audiobook']['LOCATION'] = array(
	'wikimart' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_WIKIMART"),
		'sub' => array(
		)
	),
);