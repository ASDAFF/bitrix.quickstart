<?php
IncludeModuleLangFile(__FILE__);

$profileTypes["ym_simple"] = array(
	"CODE" => "ym_simple",
    "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_AVAILABLE"),
			"VALUE" => "",
            "TYPE" => "const",
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
            "USE_CONDITION" => "Y",
            "CONTVALUE_TRUE" => "true",
            "CONTVALUE_FALSE" => "false",
		),
		array(
			"CODE" => "BID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_BID"),
			"VALUE" => "",
		),
        array(
            "CODE" => "BASE_DELIVERY_COST",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_BASEDELIVERYCOST"),
        ),
        array(
            "CODE" => "BASE_DELIVERY_DAYS",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_BASEDELIVERYDAYS"),
        ),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_PRICE"),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "OLDPRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_OLDPRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_CURRENCY"),
			"REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => "Y",
            "TYPE" => "field",
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_PICTURE"),
		),
        array(
			"CODE" => "STORE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_STORE"),
		),
        array(
			"CODE" => "PICKUP",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_PICKUP"),
		),
        array(
			"CODE" => "DELIVERY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_DELIVERY"),
		),
        array(
            "CODE" => "LOCAL_DELIVERY_COST",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_LOCALDELIVERYCOST"),
        ),
        array(
			"CODE" => "LOCAL_DELIVERY_DAYS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_LOCALDELIVERYDAYS"),
		),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_NAME"),
			"VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_VENDOR"),
		),
		array(
			"CODE" => "VENDORCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_VENDORCODE"),
		),

		array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_DESCRIPTION"),
		),
		array(
			"CODE" => "SALES_NOTES",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_SALESNOTES"),
		),
        array(
			"CODE" => "MANUFACTURER_WARRANTY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_MANUFACTURERWARRANTY"),
		),
        array(
			"CODE" => "COUNTRY_OF_ORIGIN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_COUNTRYOFORIGIN"),
		),
		array(
			"CODE" => "ADULT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_ADULT"),
		),
        array(
			"CODE" => "AGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_AGE"),
		),
		array(
			"CODE" => "BARCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_BARCODE"),
		),
		array(
            "CODE" => "CPA",
            "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_CPA"),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
			"CODE" => "UTM_CAMPAIGN",
			"NAME" => GetMessage( "ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
		),
        array(
			"CODE" => "PARAM",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_PARAM"),
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
        <delivery-options> 
            <option cost="#BASE_DELIVERY_COST#" days="#BASE_DELIVERY_DAYS#"/> 
        </delivery-options>
        <offers>
            #ITEMS#
        </offers>
    </shop>
</yml_catalog>',

	"DATEFORMAT" => "Y-m-d_H:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["ym_simple"]["FIELDS"][6] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
    
    $profileTypes["ym_simple"]["FIELDS"][7] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_MARKET_SIMPLE_FIELD_OLDPRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ym_simple"]["PORTAL_REQUIREMENTS"] = GetMessage("ACRIT_EXPORTPRO_TYPE_MARKET_SIMPLE_PORTAL_REQUIREMENTS");
$profileTypes["ym_simple"]["PORTAL_VALIDATOR"] = GetMessage("ACRIT_EXPORTPRO_TYPE_MARKET_SIMPLE_PORTAL_VALIDATOR");
$profileTypes["ym_simple"]["EXAMPLE"] = GetMessage("ACRIT_EXPORTPRO_TYPE_MARKET_SIMPLE_EXAMPLE");

$profileTypes["ym_simple"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes["ym_simple"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes["ym_simple"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\" bid=\"#BID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <market_category>#MARKET_CATEGORY#</market_category>
    <picture>#SITE_URL##PICTURE#</picture>
    <store>#STORE#</store>
    <pickup>#PICKUP#</pickup>
    <delivery>#DELIVERY#</delivery>
    <delivery-options> 
        <option cost=\"#LOCAL_DELIVERY_COST#\" days=\"#LOCAL_DELIVERY_DAYS#\"/> 
    </delivery-options>
    <name>#NAME#</name>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <description>#DESCRIPTION#</description>
    <sales_notes>#SALES_NOTES#</sales_notes>
    <manufacturer_warranty>#MANUFACTURER_WARRANTY#</manufacturer_warranty>
    <country_of_origin>#COUNTRY_OF_ORIGIN#</country_of_origin>
    <adult>#ADULT#</adult>
    <age>#AGE#</age>
    <barcode>#BARCODE#</barcode>
    <cpa>#CPA#</cpa>
    <param>#PARAM#</param>
</offer>
";

$profileTypes["ym_simple"]["LOCATION"] = array(
	"yandex" => array(
		"name" => GetMessage("ACRIT_EXPORTPRO_ANDEKS"),
		"sub" => array(
			"market" => array(
				"name" => GetMessage("ACRIT_EXPORTPRO_VEBMASTER"),
				"sub" => "",
			)
		)
	),
);