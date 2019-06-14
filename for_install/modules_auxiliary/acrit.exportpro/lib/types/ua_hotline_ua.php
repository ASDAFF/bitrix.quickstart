<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["ua_hotline_ua"] = array(
    "CODE" => "ua_hotline_ua",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "AVAILABLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_AVAILABLE" ),
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
            "CODE" => "CATEGORYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "CODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_CODE" ),
        ),
        array(
            "CODE" => "BARCODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_BARCODE" ),
        ),
        array(
            "CODE" => "VENDOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_VENDOR" ),
        ),
        array(
            "CODE" => "NAME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_NAME" ),
            "VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_URL" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PICTURE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_PICTURE" ),
        ),
        array(
            "CODE" => "CURRENCYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_CURRENCY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "UAH",
        ),
        array(
            "CODE" => "PRICE_RUAH",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_PRICE_RUAH" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_OLDPRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "PRICE_RUSD",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_PRICE_RUSD" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "STOCK_DAYS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_STOCK_DAYS" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "STOCK_STATUS",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_STOCK_STOCK_STATUS" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "В наличии",
        ),
        array(
            "CODE" => "GUATANTEE_TYPE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_STOCK_STOCK_GUATANTEE_TYPE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "manufacturer",
        ),
        array(
            "CODE" => "GUATANTEE_TIME",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_STOCK_STOCK_GUATANTEE_TIME" ),
        ),
        array(
            "CODE" => "ORIGINAL",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_STOCK_STOCK_ORIGINAL" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "Оригинал",
        ),
        array(
            "CODE" => "COUNTRY_OF_ORIGIN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_COUNTRYOFORIGIN" ),
        ),
        array(
            "CODE" => "CUSTOM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_CUSTOM" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "1",
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
        array(
            "CODE" => "PARAM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_UA_HOTLINE_UA_FIELD_PARAM" ),
        ),            
    ),
    "FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<price>
    <date>#DATE#</date>
    <firmName>#COMPANY_NAME#</firmName>
    <rate>#CURRENCY#</rate>
    <categories>#CATEGORY#</categories>
    <items>
        #ITEMS#
    </items>
</price>',

    "DATEFORMAT" => "Y-m-d_H:i",
);

$profileTypes["ua_hotline_ua"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_UA_HOTLINE_UA_PORTAL_REQUIREMENTS" );
$profileTypes["ua_hotline_ua"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_UA_HOTLINE_UA_EXAMPLE" );

$profileTypes["ua_hotline_ua"]["SECTIONS"] = "
<category>
    <id>#ID#</id>
    <name>#NAME#</name>
</category>
" . PHP_EOL;

$profileTypes["ua_hotline_ua"]["ITEMS_FORMAT"] = "
<item>
    <id>#ID#</id>
    <categoryId>#CATEGORYID#</categoryId>
    <code>#CODE#</code>
    <barcode>#BARCODE#</barcode>
    <vendor>#VENDOR#</vendor>
    <name>#NAME#</name>
    <description>#DESCRIPTION#</description>
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <image>#SITE_URL##PICTURE#</image>
    <priceRUAH>#PRICE_RUAH#</priceRUAH>
    <oldprice>#OLDPRICE#</oldprice>
    <priceRUSD>#PRICE_RUSD#</priceRUSD>
    <stock days='#STOCK_DAYS#'>#STOCK_STATUS#</stock>
    <guarantee type='#GUATANTEE_TYPE#'>#GUATANTEE_TIME#</guarantee>
    <param name='Оригинальность'>#ORIGINAL#</param>
    <param name='Страна изготовления'>#COUNTRY_OF_ORIGIN#</param>
    <custom>#CUSTOM#</custom>
</item>
";

$profileTypes["ua_hotline_ua"]["LOCATION"] = array(
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