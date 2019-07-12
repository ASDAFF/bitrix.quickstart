<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/j/cloud-zoom.1.0.2.min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/j/jquery.scrollTo-min.js");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/j/jquery.rating.pack.js");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/cloud-zoom.css");

?>