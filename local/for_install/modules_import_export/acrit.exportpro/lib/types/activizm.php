<?php            
IncludeModuleLangFile(__FILE__);

$profileTypes['activizm'] = array(
	"CODE" => 'activizm',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_AVAILABLE"),
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
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_PICTURE"),
		),
        array(
			"CODE" => "DELIVERY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_DELIVERY"),
		),
        array(
			"CODE" => "LOCAL_DELIVERY_COST",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_LOCALDELIVERYCOST"),
		),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_NAME"),
			"VALUE" => "NAME",
            "REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_VENDOR"),
		),
		array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_DESCRIPTION"),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_ACTIVIZM_FIELD_UTM_CAMPAIGN" ),
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
    
    $profileTypes['activizm']["FIELDS"][3] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM_FIELD_PRICE"),
        "REQUIRED" => 'Y',
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['activizm']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_ACTIVIZM_EXAMPLE');
$profileTypes['activizm']['PORTAL_REQUIREMENTS'] = GetMessage('ACRIT_EXPORTPRO_TYPE_ACTIVIZM_PORTAL_REQUIREMENTS');

$profileTypes['activizm']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['activizm']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['activizm']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <pickup>#PICKUP#</pickup>
    <delivery>#DELIVERY#</delivery>
    <local_delivery_cost>#LOCAL_DELIVERY_COST#</local_delivery_cost>
    <name>#NAME#</name>
    <vendor>#VENDOR#</vendor>
    <description>#DESCRIPTION#</description>
</offer>
";
    
$profileTypes['activizm']['LOCATION'] = array(
	'activizm' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_ACTIVIZM"),
		'sub' => array(
		)
	),
);