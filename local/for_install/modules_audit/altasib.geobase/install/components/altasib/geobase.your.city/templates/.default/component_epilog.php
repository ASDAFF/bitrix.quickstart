<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (ADMIN_SECTION !== true && COption::GetOptionString("altasib.geobase", "enable_jquery", "ON") == "ON")
	CJSCore::Init(array('jquery'));
?>