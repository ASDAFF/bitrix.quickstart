<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ua_price_ua"] = array(
    "CODE" => "ua_price_ua",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "AVAILABLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_AVAILABLE" ),
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
            "CODE" => "NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_NAME" ),
            "VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "CATEGORYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_PRICE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_OLDPRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_URL" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PICTURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_PICTURE" ),
        ),
        array(
            "CODE" => "VENDOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_VENDOR" ),
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "WARRANTY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_WARRANTY" ),
        ),
        array(
            "CODE" => "AVAILABLE_AREA",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_AVAILABLE_AREA" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "Склад",
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
        array(
            "CODE" => "PARAM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_PARAM" ),
        ),            
    ),
    "FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<price date="#DATE#">
    <name>#SHOP_NAME#</name>
    #CURRENCY#
    <catalog>#CATEGORY#</catalog>
    <items>
        #ITEMS#
    </items>
</yml_catalog>',

    "DATEFORMAT" => "Y-m-d_H:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["ua_price_ua"]["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
    
    $profileTypes["ua_price_ua"]["FIELDS"][5] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_PRICE_UA_FIELD_OLDPRICE" ),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["ua_price_ua"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_UA_PRICE_UA_PORTAL_REQUIREMENTS" );
$profileTypes["ua_price_ua"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_UA_PRICE_UA_EXAMPLE" );

$profileTypes["ua_price_ua"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes["ua_price_ua"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes["ua_price_ua"]["ITEMS_FORMAT"] = "
<item id=\"#ID#\">
    <name>#NAME#</name>
    <categoryId>#CATEGORYID#</categoryId>
    <price>#PRICE#</price>
    <bnprice>#OLDPRICE#</bnprice>
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <image>#SITE_URL##PICTURE#</image>
    <vendor>#VENDOR#</vendor>
    <description>#DESCRIPTION#</description>
    <warranty>#WARRANTY#</warranty>
    <available>#AVAILABLE_AREA#</available>
</item>
";

$profileTypes["ua_price_ua"]["LOCATION"] = array(
    "yandex" => array(
        "name" => GetMessage( "ACRIT_EXPORTPRO_ANDEKS" ),
        "sub" => array(
            "market" => array(
                "name" => GetMessage( "ACRIT_EXPORTPRO_VEBMASTER" ),
                "sub" => "",
            )
        )
    ),
);