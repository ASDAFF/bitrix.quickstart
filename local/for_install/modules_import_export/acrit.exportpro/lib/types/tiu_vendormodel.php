<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['tiu_vendormodel'] = array(
	"CODE" => 'tiu_vendormodel',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABLE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_AVAILABLE"),
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
            'CODE' => 'SELLING_TYPE',
            'NAME' => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_SELLINGTYPE"),
        ),
		array(
			"CODE" => "PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "OLDPRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_OLDPRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "DISCOUNT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_DISCOUNT"),
		),
        array(
			"CODE" => "OPTPRICE1",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_OPTPRICE1"),
		),
        array(
			"CODE" => "OPTQUANTITY1",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_OPTQUANTITY1"),
		),
        array(
			"CODE" => "MINIMUM_ORDER_QUANTITY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_MINIMUM_ORDER_QUANTITY"),
		),
		array(
			"CODE" => "CURRENCYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_CURRENCY"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
		array(
			"CODE" => "CATEGORYID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
		array(
			"CODE" => "PICTURE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_PICTURE"),
		),
        array(
			"CODE" => "TYPEPREFIX",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_TYPEPREFIX"),
		),
        array(
			"CODE" => "VENDOR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_VENDOR"),
            'REQUIRED' => 'Y',
		),
		array(
			"CODE" => "VENDORCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_VENDORCODE"),
		),
        array(
			"CODE" => "BARCODE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_BARCODE"),
		),
		array(
			"CODE" => "MODEL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_MODEL"),
            'REQUIRED' => 'Y',
		),
		array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_DESCRIPTION"),
		),
        array(
			"CODE" => "COUNTRY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_COUNTRYOFORIGIN"),
		),
        array(
			"CODE" => "PARAM",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_PARAM"),
		),
        array(
			"CODE" => "KEYWORDS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_KEYWORDS"),
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
    
    $profileTypes['tiu_vendormodel']["FIELDS"][3] = array(
        "CODE" => "PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_PRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
    
    $profileTypes['tiu_vendormodel']["FIELDS"][4] = array(
        "CODE" => "OLDPRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_TIU_VENDORMODEL_FIELD_OLDPRICE"),
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['tiu_vendormodel']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_TIU_VENDORMODEL_PORTAL_REQUIREMENTS' );
$profileTypes['tiu_vendormodel']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_TIU_VENDORMODEL_EXAMPLE');

$profileTypes['tiu_vendormodel']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['tiu_vendormodel']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['tiu_vendormodel']['ITEMS_FORMAT'] = "
<offer id=\"#ID#\" available=\"#AVAILABLE#\" type='vendor.model' selling_type=\"#SELLING_TYPE#\">
    <typePrefix>#TYPEPREFIX#</typePrefix>
    <price>#PRICE#</price>
    <oldprice>#OLDPRICE#</oldprice>
    <prices>
        <price>
            <value>#OPTPRICE1#</value>
            <quantity>#OPTQUANTITY1#</quantity>
        </price>
    </prices>
    <discount>#DISCOUNT#</discount>
    <minimum_order_quantity>#MINIMUM_ORDER_QUANTITY#</minimum_order_quantity>
    <discount>#DISCOUNT#</discount>
    <currencyId>#CURRENCYID#</currencyId>
    <categoryId>#CATEGORYID#</categoryId>
    <picture>#SITE_URL##PICTURE#</picture>
    <vendor>#VENDOR#</vendor>
    <vendorCode>#VENDORCODE#</vendorCode>
    <barcode>#BARCODE#</barcode>
    <country>#COUNTRY#</country>
    <param>#PARAM#</param>
    <description>#DESCRIPTION#</description>
    <available>#AVAILABLE#</available>
    <model>#MODEL#</model>
    <keywords>#KEYWORDS#</keywords>
</offer>
";
    
$profileTypes['tiu_vendormodel']['LOCATION'] = array(
	'tiu' => array(
		'name' => GetMessage("ACRIT_EXPORTPRO_TIU"),
		'sub' => array(
		)
	),
);