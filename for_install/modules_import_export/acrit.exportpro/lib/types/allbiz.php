<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['allbiz'] = array(
	"CODE" => 'allbiz',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_AVAILABLE"),
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
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_NAME"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_CURRENCY"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB"
		),
        array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "URL",
			"NAME" => "URL ".GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
		),
		
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PICTURE"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_VENDOR"),
		),
		array(
			"CODE" => "VENDORCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_VENDORCODE"),
		),
        array(
			"CODE" => "COUNTRY_OF_ORIGIN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_COUNTRYOFORIGIN"),
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "PARAM1",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM1"),
		),
        array(
			"CODE" => "PARAM2",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM2"),
		),
        array(
			"CODE" => "PARAM3",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM3"),
		),
        array(
			"CODE" => "PARAM4",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM4"),
		),
        array(
			"CODE" => "PARAM5",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM5"),
		),
        array(
			"CODE" => "PARAM6",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM6"),
		),
        array(
			"CODE" => "PARAM7",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PARAM7"),
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
    "ENCODING" => 'utf8',
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['allbiz']["FIELDS"][3] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_ALLBIZ_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['allbiz']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_ALLBIZ_PORTAL_REQUIREMENTS' );
$profileTypes['allbiz']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_ALLBIZ_EXAMPLE');

$profileTypes['allbiz']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['allbiz']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['allbiz']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\">
    <name>#NAME#</name>
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <country_of_origin>#COUNTRY_OF_ORIGIN#</country_of_origin>
    <url><![CDATA[#SITE_URL##URL#]]></url>
    <picture><![CDATA[#SITE_URL##PICTURE#]]></picture>
    <description>#DESCRIPTION#</description>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_OPTPRICE')."\">#PARAM1#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_ALTPRICE')."\">#PARAM2#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_MINQUANT')."\">#PARAM3#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_MY')."\">#PARAM4#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_EXPORT')."\">#PARAM5#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_IMPORT')."\">#PARAM6#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_ALLBIZ_PARAM_ALLBIZ_ID')."\">#PARAM7#</param>
</offer>
";
    
$profileTypes['allbiz']['LOCATION'] = array(
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