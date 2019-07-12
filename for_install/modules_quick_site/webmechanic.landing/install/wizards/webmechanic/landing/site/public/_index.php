<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$module_id = "webmechanic.landing";
CModule::IncludeModule($module_id);

$APPLICATION->SetPageProperty("title",COption::GetOptionString($module_id,"WEBMECHANIC_CREDIT_TITLE_EDIT"));
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
?> 


<?
$APPLICATION->IncludeComponent("webmechanic:landing.credit", ".default", array(
    "CODE" => 'credit_elem',
    "PRODUCT_SECTION" => false,
    "ACTIONS_SECTION" => false,
    "BANNERS_SECTION" => false,
), false);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>