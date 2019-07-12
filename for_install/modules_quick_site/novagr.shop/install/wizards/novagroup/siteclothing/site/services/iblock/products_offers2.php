<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
        die();
    COption::SetOptionInt("novagr.shop", 'xml_products_offers'.WIZARD_SITE_ID, 4 );
    COption::SetOptionString("novagr.shop", 'xml_products_offers_file'.WIZARD_SITE_ID, 'novagr_standard_products_offers' );
    COption::SetOptionString("novagr.shop", 'xml_products_offers_code'.WIZARD_SITE_ID, 'novagr_standard_products_offers' );
    require dirname(__FILE__).'/products_offers.inc2.php';
?>