<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

IncludeTemplateLangFile(__FILE__);

$arTemplate = Array(
	"NAME"=> GetMessage("WEBMECHANIC_TEMPLATE_AUTO_NAME"), 
	"DESCRIPTION"=> GetMessage("WEBMECHANIC_TEMPLATE_AUTO_DESCRIPTION"),
	"PREVIEW" => dirname(__FILE__) . '/images/preview.png',
	"SCREENSHOT" => dirname(__FILE__) . '/images/preview.png',
);
?>