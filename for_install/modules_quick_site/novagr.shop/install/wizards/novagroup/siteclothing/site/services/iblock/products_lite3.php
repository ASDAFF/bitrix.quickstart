<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
        die();
    COption::SetOptionInt("novagr.shop", 'xml_products'.WIZARD_SITE_ID, 33 );
    COption::SetOptionString("novagr.shop", 'xml_products_file'.WIZARD_SITE_ID, 'novagr_lite_products' );
    COption::SetOptionString("novagr.shop", 'xml_products_code'.WIZARD_SITE_ID, 'novagr_lite_products' );
    require dirname(__FILE__).'/products.inc3.php';
?>