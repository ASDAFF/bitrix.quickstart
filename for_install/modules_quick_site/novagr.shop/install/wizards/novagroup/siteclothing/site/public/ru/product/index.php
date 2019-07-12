<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Лендинг");

$currentUri = $APPLICATION->GetCurPage();
if ($currentUri == '/product/') {
    LocalRedirect("/catalog/", true, '301 Moved permanently');
}
?>
<?$APPLICATION->IncludeComponent(
    "novagr.shop:landing",
    "",
    Array(
        "IBLOCK_ID" => "#LANDINGPAGES_IBLOCK_ID#",
        "CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
        "ELEMENT_CODE" => $_REQUEST["elem_code"],
        "CATALOG_OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#",
        "ARTICLES_IBLOCK_ID" => "#ARTICLES_IBLOCK_ID#",
        "OPT_GROUP_ID" => "#GROUP_TRADE#",
        "OPT_PRICE_ID" => "#PRICE_TRADE#",
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "1",
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>