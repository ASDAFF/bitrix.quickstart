<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['ebay_mp30'] = array(
	"CODE" => 'ebay_mp30',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_NAME"),
	"DESCRIPTION" => '',
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_AVAILABLE"),
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
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PICTURE"),
		),
        array(
			"CODE" => "STOCK",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_STOCK"),
		),
        array(
			"CODE" => "CONDITION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_CONDITION"),
		),
        array(
			"CODE" => "NAME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_NAME"),
			"VALUE" => "NAME",
            "REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_DESCRIPTION"),
		),
		array(
			"CODE" => "PARAM_1",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_1"),
		),
		array(
			"CODE" => "PARAM_2",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_2"),
		),
		array(
			"CODE" => "PARAM_3",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_3"),
		),
		array(
			"CODE" => "PARAM_4",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PARAM_4"),
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
    
    $profileTypes['ebay_mp30']["FIELDS"][2] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_EBAY_MP30_FIELD_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['ebay_mp30']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PORTAL_REQUIREMENTS' );
$profileTypes['ebay_mp30']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_EBAY_MP30_EXAMPLE');

$profileTypes['ebay_mp30']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['ebay_mp30']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['ebay_mp30']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\" group_id=\"#GROUP_ITEM_ID#\">
    <price>#PRICE#</price>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <stock>#STOCK#</stock>
    <condition>#CONDITION#</condition>
    <name>#NAME#</name>
    <description><![CDATA[#DESCRIPTION#]]></description>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_1_NAME')."\">#PARAM_1#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_2_NAME')."\">#PARAM_2#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_3_NAME')."\">#PARAM_3#</param>
    <param name=\"".GetMessage('ACRIT_EXPORTPRO_TYPE_EBAY_MP30_PARAM_4_NAME')."\">#PARAM_4#</param>
</offer>
";
    
$profileTypes['ebay_mp30']['LOCATION'] = array(
	'ebay_mp30' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_ANDEKS"),
		'sub' => array(
			'market' => array(
				'name' => GetMessage("ACRIT_EXPORTPRO_VEBMASTER"),
				'sub' => '',
			)
		)
	),
);