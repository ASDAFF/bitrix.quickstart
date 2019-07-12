<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$site_id = strtoupper(WIZARD_SITE_ID);

$rsSites = CSite::GetByID(WIZARD_SITE_ID);
$arSite = $rsSites->Fetch();

$MESS["INNET_CATALOG_" . $site_id . "_TYPE_NAME"] = "Товары сайта " . WIZARD_SITE_ID/* . " (" . $arSite['SITE_NAME'] . ")"*/;
$MESS["INNET_CATALOG_ELEMENT_NAME"] = "Товары";
$MESS["INNET_CATALOG_SECTION_NAME"] = "Категории";

$MESS["INNET_OBJECTS_" . $site_id . "_TYPE_NAME"] = "Объекты сайта " . WIZARD_SITE_ID/* . " (" . $arSite['SITE_NAME'] . ")"*/;
$MESS["INNET_OBJECTS_ELEMENT_NAME"] = "Элементы";
$MESS["INNET_OBJECTS_SECTION_NAME"] = "Разделы";

$MESS["INNET_FORMS_" . $site_id . "_TYPE_NAME"] = "Формы для сайта " . WIZARD_SITE_ID/* . " (" . $arSite['SITE_NAME'] . ")"*/;
$MESS["INNET_FORMS_ELEMENT_NAME"] = "Элементы";
$MESS["INNET_FORMS_SECTION_NAME"] = "Разделы";

$MESS["REFERENCES_TYPE_NAME"] = "Справочники";
$MESS["REFERENCES_ELEMENT_NAME"] = "Справочник";
$MESS["REFERENCES_SECTION_NAME"] = "Справочник";
?>