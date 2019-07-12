<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
        die();
    COption::SetOptionString("novagr.shop", 'xml_system_file'.WIZARD_SITE_ID, 'system_lite' );
    require dirname(__FILE__).'/system.inc.php'; 
?>