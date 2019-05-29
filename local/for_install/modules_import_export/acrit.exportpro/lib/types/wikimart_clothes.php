<?php
IncludeModuleLangFile(__FILE__);

$profileTypes["wikimart_clothes"] = array(
    "CODE" => "wikimart_clothes",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK" ),
    "REG" => "http://market.yandex.ru/",
    "HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
    "FIELDS" => array(
        array(
            "CODE" => "ID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_ID" ),
            "VALUE" => "ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "AVAILABLE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_AVAILABLE" ),
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
            "CODE" => "URL",
            "NAME" => "URL ".GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_URL" ),
            "REQUIRED" => "Y",
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PRICE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "OLDPRICE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PRICE" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "CURRENCYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_CURRENCY" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "CATEGORYID",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_CATEGORY" ),
            "VALUE" => "IBLOCK_SECTION_ID",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "PICTURE", 
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PICTURE" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "MODEL", 
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_MODEL" ),
            "VALUE" => "NAME",
            "REQUIRED" => "Y",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "TYPEPREFIX",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_TYPEPREFIX" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "VENDOR",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_VENDOR" ),
            "REQUIRED" => "Y",
        ),
        array(
            "CODE" => "VENDOR_CODE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_VENDOR_CODE" ),
        ),
        array(
            "CODE" => "DESCRIPTION",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_DESCRIPTION" ),
        ),
        array(
            "CODE" => "COUNTRY_OF_ORIGIN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_COUNTRY_OF_ORIGIN" ),
        ),
        array(
            "CODE" => "STOCK",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_STOCK" ),
        ),
        array(
            "CODE" => "ACCESSORY",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_ACCESSORY" ),
        ),
        array(
            "CODE" => "PARAM1",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PARAM" ),
        ),
        array(
            "CODE" => "PARAM2",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PARAM" ),
        ),
        array(
            "CODE" => "PARAM3",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PARAM" ),
        ),
        array(
            "CODE" => "PARAM4",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PARAM" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_UTM_CAMPAIGN" ),
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
    
    $profileTypes["wikimart_clothes"]["FIELDS"][3] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_PRICE" ),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
    
    $profileTypes["wikimart_clothes"]["FIELDS"][4] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_WIKIMART_CLOTHES_FIELD_OLDPRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes["wikimart_clothes"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_WIKIMART_CLOTHES_PORTAL_REQUIREMENTS" );
$profileTypes["wikimart_clothes"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_TYPE_WIKIMART_CLOTHES_EXAMPLE" );

$profileTypes["wikimart_clothes"]["CURRENCIES"] =
    "<currency id='#CURRENCY#' rate='#RATE#'/>" . PHP_EOL;

$profileTypes["wikimart_clothes"]["SECTIONS"] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes["wikimart_clothes"]["ITEMS_FORMAT"] = "
<offer id=\"#ID#\" type=\"vendor.model\" available=\"#AVAILABLE#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <model>#MODEL#</model>
    <typePrefix>#TYPEPREFIX#</typePrefix>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDOR_CODE#</vendorCode>
    <description>#DESCRIPTION#</description>
    <country_of_origin>#COUNTRY_OF_ORIGIN#</country_of_origin>
    <stock>#STOCK#</stock>
    <accessory offer=\"#ACCESSORY#\"/>
    <param name=\"\">#PARAM1#</param>
    <param name=\"\">#PARAM2#</param>
    <param name=\"\">#PARAM3#</param>
    <param name=\"\">#PARAM4#</param>
</offer>
";
    
$profileTypes["wikimart_clothes"]["LOCATION"] = array(
    "yandex" => array(
        "name" => GetMessage( "ACRIT_EXPORTPRO_WIKIMART" ),
        "sub" => array(
        )
    ),
);