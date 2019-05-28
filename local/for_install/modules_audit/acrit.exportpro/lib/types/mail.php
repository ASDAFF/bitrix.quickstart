<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['mailru'] = array(
	"CODE" => 'mailru',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_AVAILABLE"),
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
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_BID"),
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_PICTURE"),
		),
        array(
			"CODE" => "TYPEPREFIX",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_TYPEPREFIX"),
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_VENDOR"),
		),
		array(
			"CODE" => "MODEL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_MODEL"),
		),
        array(
			"CODE" => "VENDORCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_VENDORCODE"),
		),
		array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_NAME"),
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "DELIVERY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_DELIVERY"),
		),
        array(
			"CODE" => "PICKUP",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_PICKUP"),
		),
        array(
			"CODE" => "LOCAL_DELIVERY_COST",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_LOCALDELIVERYCOST"),
		),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<torg_price date="2014-06-22 14:42">
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
</torg_price>',
    
	"DATEFORMAT" => "Y-m-d_h:i",
    "ENCODING" => 'cp1251',
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['mailru']["FIELDS"][4] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_MAILRU_VENDORMODEL_FIELD_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['mailru']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_MAILRU_VENDORMODEL_PORTAL_REQUIREMENTS' );
$profileTypes['mailru']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_MAILRU_VENDORMODEL_EXAMPLE');

$profileTypes['mailru']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['mailru']['SECTIONS'] =
    '<category id="#ID#" parentId="#PARENT_ID#">#NAME#</category>' . PHP_EOL;

$profileTypes['mailru']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\" cbid=\"#BID#\">
    <url>#SITE_URL##URL#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</url>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <typePrefix>#TYPEPREFIX#</typePrefix>
    <vendor>#VENDOR#</vendor>
    <model>#MODEL#</model>
    <vendorCode>#VENDORCODE#</vendorCode>
    <name>#NAME#</name>
    <description>#DESCRIPTION#</description>
    <delivery>#DELIVERY#</delivery>
    <pickup>#PICKUP#</pickup>
    <local_delivery_cost>#LOCAL_DELIVERY_COST#</local_delivery_cost>
</offer>
";
    
$profileTypes['mailru']['LOCATION'] = array(
	'mailru' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_MAILRU"),
		'sub' => array(
		)
	),
);