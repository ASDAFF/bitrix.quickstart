<?php
IncludeModuleLangFile(__FILE__);

$profileTypes['google'] = array(
    "CODE"=>"google",
    "NAME"=>"Google Merchants",
    "DESCRIPTION"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_DESCRIPTION"),
    "REG"=>"http://google.com/merchants/",
    "HELP"=>"https://support.google.com/merchants/?hl=ru#topic=3404818",
    "FIELDS"=>array(
        array(
              "CODE"=>"g:id",
              "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ID"),
              "VALUE"=>"ID",
              "TYPE" => 'field',
              'REQUIRED' => 'Y',
              'DELETE_ONEMPTY' => 'N',
        ),
        array(
              "CODE"=>"g:title",
              "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TITLE"),
              'VALUE' => 'NAME',
              "TYPE" => 'field',
              'REQUIRED' => 'Y',
              'DELETE_ONEMPTY' => 'N',
        ),
        array(
            "CODE"=>"g:description",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_DESCRIPTION"),
            "VALUE"=>"PREVIEW_TEXT",
            "TYPE" => 'field',
        ),
        array(
            "CODE"=>"g:link",
            "NAME"=> GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_LINK"),
            "VALUE"=>"DETAIL_PAGE_URL",
            "TYPE" => 'field',
            'DELETE_ONEMPTY' => 'N',
        ),
        array(
            "CODE"=>"g:image_link",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_IMAGELINK"),
            "VALUE"=>"DETAIL_PICTURE",
            "TYPE" => 'field',
            'DELETE_ONEMPTY' => 'N',
        ),
        array(
            "CODE"=>"g:condition",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CONDITION"),
            'REQUIRED' => 'Y',
            'DELETE_ONEMPTY' => 'N',
        ),
        array(
            "CODE"=>"g:availability",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_AVAILABILITY"),
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
            'CONTVALUE_TRUE' => 'in stock',
            'CONTVALUE_FALSE' => 'out of stock',
            'REQUIRED' => 'Y',
            'DELETE_ONEMPTY' => 'N',
        ),
        array(
            "CODE"=>"g:price",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRICE"),
            'REQUIRED' => 'Y',
            'DELETE_ONEMPTY' => 'N',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE"=>"g:shipping_country",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_COUNTRY"),
        ),
        array(
            "CODE"=>"g:shipping_service",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SERVICE"),
        ),
        array(
            "CODE"=>"g:shipping_price",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SHIPPINGPRICE"),
        ),
        array(
            "CODE"=>"g:gtin",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_GTIN"),
        ),
        array(
            "CODE"=>"g:brand",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_BRAND"),
        ),
        array(
            "CODE"=>"g:mpn",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MPN"),
        ),
        array(
            "CODE"=>"g:google_product_category",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRODUCTCATEGORY"),
        ),
        array(
            "CODE"=>"g:product_type",
            "NAME"=>GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TYPE"),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_MEDIUM" ),
            "REQUIRED" => 'Y',
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_MEDIUM_VALUE" )
        ),
        array(
            "CODE" => "UTM_TERM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_TERM" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CONTENT",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_CONTENT" ),
            "TYPE" => "field",
            "VALUE" => "ID",
        ),
        array(
            "CODE" => "UTM_CAMPAIGN",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_CAMPAIGN" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
    ),
    "FORMAT"=> '<?xml version="1.0" encoding="#ENCODING#"?>
    <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <link>#SITE_URL#</link>
    <description>#DESCRIPTION#</description>
    <channel>#ITEMS#</channel>
</rss>
    ',
    "DATEFORMAT"=>"Y-m-d_h:i",
    "ENCODING" => 'utf8',
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes['google']["FIELDS"][7] = array(
        "CODE" => "g:price",
        "NAME" => GetMessage("ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRICE"),
        "REQUIRED" => "Y",
        'DELETE_ONEMPTY' => 'N',
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
}

$profileTypes['google']['PORTAL_REQUIREMENTS'] = GetMessage('ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PORTAL_REQUIREMENTS');
$profileTypes['google']['EXAMPLE'] = GetMessage('ACRIT_EXPORTPRO_GOOGLE_MERCHANT_EXAMPLE');

$profileTypes['google']['ITEMS_FORMAT'] = "<item>
    <g:id>#g:id#</g:id>
    <g:item_group_id>#GROUP_ITEM_ID#</g:item_group_id>
    <g:title>#g:title#</g:title>
    <g:description>#g:description#</g:description>
    <g:link>#SITE_URL##g:link#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</g:link>
    <g:image_link>#SITE_URL##g:image_link#</g:image_link>
    <g:condition>#g:condition#</g:condition>
    <g:availability>#g:availability#</g:availability>
    <g:price>#g:price#</g:price>
    <g:shipping>
        <g:country>#g:shipping_country#</g:country>
        <g:service>#g:shipping_service#</g:service>
        <g:price>#g:shipping_price#</g:price>
    </g:shipping>
    <g:gtin>#g:gtin#</g:gtin>
    <g:brand>#g:brand#</g:brand>
    <g:mpn>#g:mpn#</g:mpn>
    <g:google_product_category>#MARKET_CATEGORY#</g:google_product_category>
    <g:product_type>#g:product_type#</g:product_type>
</item>";
    
$profileTypes['google']['LOCATION'] = array(
	'google' => array(
        'name' => '',
    ),
);