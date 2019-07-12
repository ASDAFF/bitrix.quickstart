<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?$APPLICATION->IncludeComponent(
	"smartrealt:catalog.filter",
	".default",
	Array(
		"CATALOG_LIST_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	)
);?>

<?$APPLICATION->IncludeComponent(
	"smartrealt:catalog.map",
	".default",
	Array(
        "PAGE" => $_GET['PAGEN_2'],
		"CATALOG_DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	)
);?>