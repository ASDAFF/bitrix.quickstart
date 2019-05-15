<?php
IncludeModuleLangFile(__FILE__);
$profileTypes["1c_trade"] = array(
    "CODE" => "1c_trade",
    "NAME" => GetMessage( "ACRIT_EXPORTPRO_1C_TRADE_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_EXPORTPRO_1C_TRADE_DESCRIPTION" ),
    "DATEFORMAT" => "c",
    "ENCODING" => "utf8",
    "EXAMPLE" => GetMessage( "ACRIT_EXPORTPRO_1C_TRADE_EXAMPLE" ),
    "NAMESCHEMA" => array( "CATALOG_QUANTITY" => "CATALOG_QUANTITY_SKU" )
);