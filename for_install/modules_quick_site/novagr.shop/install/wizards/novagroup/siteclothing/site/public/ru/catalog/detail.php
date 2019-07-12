<?
if($_REQUEST['CAJAX'] == 1){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}else
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent(
	"novagr.shop:catalog.element",
	".default",
	Array(
		"SORT_FIELD" => "ID",
		"SORT_BY" => "DESC",
		"CATALOG_IBLOCK_TYPE" => "catalog",
		"CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
        "LANDING_IBLOCK_ID" => "#LANDINGPAGES_IBLOCK_ID#",
		"CATALOG_OFFERS_IBLOCK_ID" => "#OFFERS_IBLOCK_ID#",
		"ARTICLES_IBLOCK_ID" => "#ARTICLES_IBLOCK_ID#",
		"FASHION_IBLOCK_ID" => "#FASHION_IBLOCK_ID#",
		"SAMPLES_IBLOCK_CODE" => "samples",
		"BRANDNAME_IBLOCK_CODE" => "vendor",
		"COLORS_IBLOCK_CODE" => "colors",
		"MATERIALS_IBLOCK_CODE" => "materials",
		"STD_SIZES_IBLOCK_CODE" => "std_sizes",
        "CATALOG_SUBSCRIBE_ENABLE" => "#CATALOG_SUBSCRIBE_ENABLE#",
        "INET_MAGAZ_ADMIN_USER_GROUP_ID" => "#GROUP_SADMIN#",
        "OPT_GROUP_ID" => "#GROUP_TRADE#",
        "OPT_PRICE_ID" => "#PRICE_TRADE#",
        "SIZE_NO_ID" => "#SIZE_NO_ID#",
        "COLOR_NO_ID" => "#COLOR_NO_ID#",

		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "2592000",
        "cs"=>$_REQUEST['cs'],
	),
false,
Array(
	'ACTIVE_COMPONENT' => 'Y',
)
);?>


<?
if($_REQUEST['CAJAX'] == 1)
{
	$APPLICATION->IncludeFile(SITE_DIR."include/pSubscribe.php");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}else
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
