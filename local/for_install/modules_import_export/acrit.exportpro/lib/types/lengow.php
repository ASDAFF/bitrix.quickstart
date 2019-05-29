<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['lengow'] = array(
	"CODE" => 'lengow',
    "NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_NAME"),
	"DESCRIPTION" => GetMessage("ACRIT_EXPORTPRO_PODDERJIVAETSA_ANDEK"),
	"REG" => "http://market.yandex.ru/",
	"HELP" => "http://help.yandex.ru/partnermarket/export/feed.xml",
	"FIELDS" => array(
		array(
			"CODE" => "UNIQUE_ID",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_ID"),
            "VALUE" => "ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
			"CODE" => "TITEL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_NAME"),
			"VALUE" => "NAME",
            "REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
			"CODE" => "DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_DESCRIPTION"),
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "PRICE_INCLUDING_TAX",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_PRICE"),
			"REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "BARRED_PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_BARRED_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "SALE_PRICE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SALE_PRICE"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
		),
        array(
			"CODE" => "CATEGORY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_CATEGORY"),
			"VALUE" => "IBLOCK_SECTION_ID",
			"REQUIRED" => 'Y',
            "TYPE" => 'field',
		),
        array(
			"CODE" => "SUB_CATEGORY1",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SUB_CATEGORY1"),
		),
        array(
			"CODE" => "SUB_CATEGORY2",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SUB_CATEGORY2"),
		),
        array(
			"CODE" => "PRODUCT_URL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_PRODUCT_URL"),
			"VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => 'field',
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "IMAGE_URL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_IMAGE_URL"),
			"VALUE" => "DETAIL_PICTURE",
            "TYPE" => 'field',
            "REQUIRED" => 'Y',
		),
        array(
			"CODE" => "EAN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_EAN"),
		),
        array(
			"CODE" => "MPN",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_MPN"),
		),
        array(
			"CODE" => "BRAND",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_BRAND"),
		),
        array(
			"CODE" => "DELIVERY_COSTS",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_DELIVERY_COSTS"),
		),
        array(
			"CODE" => "DELIVERY_TIME",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_DELIVERY_COSTS"),
		),
        array(
			"CODE" => "DELIVERY_DESCRIPTION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_DELIVERY_COSTS"),
		),
        array(
			"CODE" => "QUANTITY_IN_STOCK",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_QUANTITY_IN_STOCK"),
            "VALUE" => "CATALOG-QUANTITY",
            "TYPE" => 'field',
		),
		array(
			"CODE" => "AVAILABILITY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_AVAILABLE"),
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
            'CONTVALUE_TRUE' => '0',
            'CONTVALUE_FALSE' => '',
		),
        array(
			"CODE" => "WARRANTY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_WARRANTY"),
		),
        array(
			"CODE" => "SIZE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SIZE"),
		),
        array(
			"CODE" => "COLOUR",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_COLOUR"),
		),
        array(
			"CODE" => "MATERIAL",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_MATERIAL"),
		),
        array(
			"CODE" => "GENDER",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_GENDER"),
		),
        array(
			"CODE" => "WEIGHT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_WEIGHT"),
		),
        array(
			"CODE" => "CONDITION",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_CONDITION"),
		),
        array(
			"CODE" => "SALES",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SALES"),
		),
        array(
			"CODE" => "PROMO_TEXT",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_PROMO_TEXT"),
		),
        array(
			"CODE" => "PROMO_PERCENTAGE",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_PROMO_PERCENTAGE"),
		),
        array(
			"CODE" => "START_DATE_FOR_PROMO",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_START_DATE_FOR_PROMO"),
		),
        array(
			"CODE" => "END_DATE_FOR_PROMO",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_END_DATE_FOR_PROMO"),
		),
        array(
			"CODE" => "ECOTAX",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_ECOTAX"),
		),
        array(
			"CODE" => "CURRENCY",
			"NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_CURRENCY"),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
		),
	),
	"FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
    #ITEMS#',
    
	"DATEFORMAT" => "Y-m-d_h:i",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['lengow']["FIELDS"][3] = array(
        "CODE" => "PRICE_INCLUDING_TAX",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
    
    $profileTypes['lengow']["FIELDS"][4] = array(
        "CODE" => "BARRED_PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_BARRED_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
    
    $profileTypes['lengow']["FIELDS"][5] = array(
        "CODE" => "SALE_PRICE",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_LENGOW_FIELD_SALE_PRICE"),
        "REQUIRED" => "Y",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
}

$profileTypes['lengow']['PORTAL_REQUIREMENTS'] = GetMessage( 'ACRIT_EXPORTPRO_TYPE_LENGOW_PORTAL_REQUIREMENTS' );
$profileTypes['lengow']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_TYPE_LENGOW_EXAMPLE');

$profileTypes['lengow']['CURRENCIES'] =
    "<currency id='#CURRENCY#' rate='#RATE#' plus='#PLUS#'></currency>" . PHP_EOL;

$profileTypes['lengow']['SECTIONS'] =
    "<category id='#ID#'>#NAME#</category>" . PHP_EOL;

$profileTypes['lengow']['ITEMS_FORMAT'] = "
<product>
    <Unique_ID><![CDATA[ #UNIQUE_ID# ]]></Unique_ID>
    <Parent_ID><![CDATA[ #GROUP_ITEM_ID# ]]></Parent_ID>
    <Titel><![CDATA[ #TITEL# ]]></Titel>
    <Description><![CDATA[ #DESCRIPTION# ]]></Description>
    <Price_including_tax><![CDATA[ #PRICE_INCLUDING_TAX# ]]></Price_including_tax>
    <Barred_price><![CDATA[ #BARRED_PRICE# ]]></Barred_price>
    <Sale_price><![CDATA[ #SALE_PRICE# ]]></Sale_price>
    <Category><![CDATA[ #CATEGORY# ]]></Category>
    <Sub_category1><![CDATA[ #SUB_CATEGORY1# ]]></Sub_category1>
    <Sub_category2><![CDATA[ #SUB_CATEGORY2# ]]></Sub_category2>
    <Product_URL><![CDATA[ #SITE_URL##PRODUCT_URL# ]]></Product_URL>
    <Image_URL><![CDATA[ #SITE_URL##IMAGE_URL# ]]></Image_URL>
    <EAN><![CDATA[ #EAN# ]]></EAN>
    <MPN><![CDATA[ #MPN# ]]></MPN>
    <Brand><![CDATA[ #BRAND# ]]></Brand>
    <Delivery_costs><![CDATA[ #DELIVERY_COSTS# ]]></Delivery_costs>
    <Delivery_time><![CDATA[ #DELIVERY_TIME# ]]></Delivery_time>
    <Delivery_description><![CDATA[ #DELIVERY_DESCRIPTION# ]]></Delivery_description>
    <Quantity_in_Stock><![CDATA[ #QUANTITY_IN_STOCK# ]]></Quantity_in_Stock>
    <Availability><![CDATA[ #AVAILABILITY# ]]></Availability>
    <Warranty><![CDATA[ #WARRANTY# ]]></Warranty>
    <Size><![CDATA[ #SIZE# ]]></Size>
    <Colour><![CDATA[ #COLOUR# ]]></Colour>
    <Material><![CDATA[ #MATERIAL# ]]></Material>
    <Gender><![CDATA[ #GENDER# ]]></Gender>
    <Weight><![CDATA[ #WEIGHT# ]]></Weight>
    <Condition><![CDATA[ #CONDITION# ]]></Condition>
    <Sales><![CDATA[ #SALES# ]]></Sales>
    <Promo_text><![CDATA[ #PROMO_TEXT# ]]></Promo_text>
    <Promo_percentage><![CDATA[ #PROMO_PERCENTAGE# ]]></Promo_percentage>
    <Start_date_for_promo><![CDATA[ #START_DATE_FOR_PROMO# ]]></Start_date_for_promo>
    <End_date_for_promo><![CDATA[ #END_DATE_FOR_PROMO# ]]></End_date_for_promo>
    <Ecotax><![CDATA[ #ECOTAX# ]]></Ecotax>
    <Currency><![CDATA[ #CURRENCY# ]]></Currency>
</product>
";
    
$profileTypes['lengow']['LOCATION'] = array(
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