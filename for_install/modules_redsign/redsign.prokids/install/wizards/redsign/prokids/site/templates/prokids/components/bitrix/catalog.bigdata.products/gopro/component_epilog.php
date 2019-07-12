<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/component_epilog.php');

$APPLICATION->AddHeadSCript(SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/script.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/style.css');