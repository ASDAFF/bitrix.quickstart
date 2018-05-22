<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["google"] = array(
    "CODE"=>"google",
    "NAME"=>"Google Merchants",
    "DESCRIPTION"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_DESCRIPTION" ),
    "REG"=>"http://google.com/merchants/",
    "HELP"=>"https://support.google.com/merchants/?hl=ru#topic=3404818",
    "FIELDS"=>array(
        array(
              "CODE"=>"g:id",
              "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ID" ),
              "VALUE"=>"ID",
              "TYPE" => "field",
              "REQUIRED" => "Y",
              "DELETE_ONEMPTY" => "N",
        ),
        array(
              "CODE"=>"g:title",
              "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TITLE" ),
              "VALUE" => "NAME",
              "TYPE" => "field",
              "REQUIRED" => "Y",
              "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE"=>"g:description",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_DESCRIPTION" ),
            "VALUE"=>"PREVIEW_TEXT",
            "TYPE" => "field",
        ),
        array(
            "CODE"=>"g:link",
            "NAME"=> GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_LINK" ),
            "VALUE"=>"DETAIL_PAGE_URL",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE"=>"g:mobile_link",
            "NAME"=> GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MOBILE_LINK" ),
            "VALUE"=>"DETAIL_PAGE_URL",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE"=>"g:image_link",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_IMAGELINK" ),
            "VALUE"=>"DETAIL_PICTURE",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE"=>"g:additional_image_link",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ADDITIONAL_IMAGELINK" ),
            "VALUE"=>"DETAIL_PICTURE",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE"=>"g:condition",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CONDITION" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "new",
        ),
        array(
            "CODE"=>"g:availability",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_AVAILABILITY" ),
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
            "CONTVALUE_TRUE" => "in stock",
            "CONTVALUE_FALSE" => "out of stock",
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "g:availability_date",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_AVAILABILITY_DATE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE"=>"g:price",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRICE" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE"=>"g:sale_price",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SALE_PRICE" ),
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "g:sale_price_effective",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SALE_PRICE_EFFECTIVE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE"=>"g:shipping_country",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_COUNTRY" ),
        ),
        array(
            "CODE"=>"g:shipping_service",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SERVICE" ),
        ),
        array(
            "CODE"=>"g:shipping_price",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SHIPPINGPRICE" ),
        ),
        array(
            "CODE" => "g:shipping_weight",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SHIPPINGWEIGHT" ),
        ),
        array(
            "CODE" => "g:shipping_label",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SHIPPINGLABEL" ),
        ),
        array(
            "CODE" => "g:multipack",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MULTIPACK" ),
        ),
        array(
            "CODE" => "g:is_bundle",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_IS_BUNDLE" ),
        ),
        array(
            "CODE"=>"g:gtin",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_GTIN" ),
        ),
        array(
            "CODE"=>"g:brand",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_BRAND" ),
        ),
        array(
            "CODE"=>"g:mpn",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MPN" ),
        ),
        array(
            "CODE" => "g:item_group_id",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ITEM_GROUP_ID" ),
        ),
        array(
            "CODE" => "g:color",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_COLOR" ),
        ),
        array(
            "CODE" => "g:gender",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_GENDER" ),
        ),
        array(
            "CODE" => "g:age_group",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_AGE_GROUP" ),
        ),
        array(
            "CODE" => "g:material",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_MATERIAL" ),
        ),
        array(
            "CODE" => "g:pattern",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PATTERN" ),
        ),
        array(
            "CODE" => "g:size",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SIZE" ),
        ),
        array(
            "CODE" => "g:size_type",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SIZE_TYPE" ),
        ),
        array(
            "CODE" => "g:size_system",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SIZE_SYSTEM" ),
        ),
        array(
            "CODE" => "g:adult",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ADULT" ),
        ),
        array(
            "CODE" => "g:adwords_grouping",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ADWORDS_GROUPING" ),
        ),
        array(
            "CODE" => "g:adwords_labels",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ADWORDS_LABELS" ),
        ),
        array(
            "CODE" => "g:adwords_redirect",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_ADWORDS_REDIRECT" ),
        ),
        array(
            "CODE" => "g:custom_label_0",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CUSTOM_LABEL_0" ),
        ),
        array(
            "CODE" => "g:custom_label_1",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CUSTOM_LABEL_1" ),
        ),
        array(
            "CODE" => "g:custom_label_2",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CUSTOM_LABEL_2" ),
        ),
        array(
            "CODE" => "g:custom_label_3",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CUSTOM_LABEL_3" ),
        ),
        array(
            "CODE" => "g:custom_label_4",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_CUSTOM_LABEL_4" ),
        ),
        array(
            "CODE" => "g:unit_pricing_measure",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UNIT_PRICING_MEASURE" ),
        ),
        array(
            "CODE" => "g:unit_pricing_base_measure",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UNIT_PRICING_BASE_MEASURE" ),
        ),
        array(
            "CODE" => "g:excluded_destination",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_EXCLUDED_DESTINATION" ),
        ),
        array(
            "CODE" => "g:expiration_date",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_EXPIRATION_DATE" ),
        ),
        array(
            "CODE"=>"g:google_product_category",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRODUCTCATEGORY" ),
        ),
        array(
            "CODE"=>"g:product_type",
            "NAME"=>GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_TYPE" ),
        ),
        array(
            "CODE" => "UTM_SOURCE",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE" ),
            "REQUIRED" => "Y",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_SOURCE_VALUE" )
        ),
        array(
            "CODE" => "UTM_MEDIUM",
            "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_UTM_MEDIUM" ),
            "REQUIRED" => "Y",
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
    "ENCODING" => "utf8",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["google"]["FIELDS"][10] = array(
        "CODE" => "g:price",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PRICE" ),
        "REQUIRED" => "Y",
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
    
    $profileTypes["google"]["FIELDS"][11] = array(
        "CODE" => "g:sale_price",
        "NAME" => GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_SALE_PRICE" ),
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
}

$profileTypes["google"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_PORTAL_REQUIREMENTS" );
$profileTypes["google"]["EXAMPLE"] = GetMessage( "ACRIT_EXPORTPRO_GOOGLE_MERCHANT_EXAMPLE" );

$profileTypes["google"]["ITEMS_FORMAT"] = "<item>
    <g:id>#g:id#</g:id>
    <g:title>#g:title#</g:title>
    <g:description>#g:description#</g:description>
    <g:link>#SITE_URL##g:link#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</g:link>
    <g:mobile_link>#SITE_URL##g:mobile_link#?utm_source=#UTM_SOURCE#&amp;utm_medium=#UTM_MEDIUM#&amp;utm_term=#UTM_TERM#&amp;utm_content=#UTM_CONTENT#&amp;utm_campaign=#UTM_CAMPAIGN#</g:mobile_link>
    <g:image_link>#SITE_URL##g:image_link#</g:image_link>
    <g:additional_image_link>#SITE_URL##g:additional_image_link#</g:additional_image_link>
    <g:condition>#g:condition#</g:condition>
    <g:availability>#g:availability#</g:availability>
    <g:availability_date>#g:availability_date#</g:availability_date>
    <g:price>#g:price#</g:price>
    <g:sale_price>#g:sale_price#</g:sale_price>
    <g:sale_price_effective>#g:sale_price_effective#</g:sale_price_effective>
    <g:shipping>
        <g:country>#g:shipping_country#</g:country>
        <g:service>#g:shipping_service#</g:service>
        <g:price>#g:shipping_price#</g:price>
    </g:shipping>
    <g:shipping_weight>#g:shipping_weight#</g:shipping_weight>
    <g:shipping_label>#g:shipping_label#</g:shipping_label>
    <g:multipack>#g:multipack#</g:multipack>
    <g:is_bundle>#g:is_bundle#</g:is_bundle>
    <g:gtin>#g:gtin#</g:gtin>
    <g:brand>#g:brand#</g:brand>
    <g:mpn>#g:mpn#</g:mpn>    
    <g:item_group_id>#g:item_group_id#</g:item_group_id>
    <g:color>#g:color#</g:color>
    <g:gender>#g:gender#</g:gender>
    <g:age_group>#g:age_group#</g:age_group>
    <g:material>#g:material#</g:material>
    <g:pattern>#g:pattern#</g:pattern>
    <g:size>#g:size#</g:size>
    <g:size_type>#g:size_type#</g:size_type>
    <g:size_system>#g:size_system#</g:size_system>
    <g:adult>#g:adult#</g:adult>
    <g:adwords_grouping>#g:adwords_grouping#</g:adwords_grouping>
    <g:adwords_labels>#g:adwords_labels#</g:adwords_labels>
    <g:adwords_redirect>#g:adwords_labels#</g:adwords_redirect>
    <g:custom_label_0>#g:custom_label_0#</g:custom_label_0>
    <g:custom_label_1>#g:custom_label_1#</g:custom_label_1>
    <g:custom_label_2>#g:custom_label_2#</g:custom_label_2>
    <g:custom_label_3>#g:custom_label_3#</g:custom_label_3>
    <g:custom_label_4>#g:custom_label_4#</g:custom_label_4>
    <g:unit_pricing_measure>#g:unit_pricing_measure#</g:unit_pricing_measure>
    <g:unit_pricing_base_measure>#g:unit_pricing_base_measure#</g:unit_pricing_base_measure>
    <g:excluded_destination>#g:excluded_destination#</g:excluded_destination>
    <g:expiration_date>#g:expiration_date#</g:expiration_date>
    <g:google_product_category>#MARKET_CATEGORY#</g:google_product_category>
    <g:product_type>#g:product_type#</g:product_type>
</item>";
    
$profileTypes["google"]["LOCATION"] = array(
	"google" => array(
        "name" => "",
    ),
);