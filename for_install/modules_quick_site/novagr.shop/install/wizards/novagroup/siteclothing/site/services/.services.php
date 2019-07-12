<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arServices = Array(
	
	'main' => Array(
		'NAME' => GetMessage("SERVICE_MAIN_SETTINGS"),
		'STAGES' => Array(
			"files.php",
			"template.php",
            "import.php",
		),
	),
	/*
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
			"step1.php",
			"step2.php",
			"step3.php"
		),
	),
	*/
	"iblock"	=> array(
		"NAME"		=> GetMessage("SERVICE_IBLOCK"),
		"STAGES"	=> array(
			"types.php",
			
			"articles.php",
			"colors.php",
            "style.php",
            "comments.php",
			"countries.php",
			"events.php",
			"materials.php",
			"samples.php",
			"seasons.php",
			"std_sizes.php",
			"vendor.php",
			//"article.php",
			"blogs.php",
            "feedback.php",
			//"faq.php",
			"news.php",
			"system.php",
            "seo_urls.php",
            "banners.php",
            "quickbuy_order.php",
            "quickbuy_product.php",
            "LandingPages.php",

            "opt.php",

			"products1.php",
            "products2.php",
            "products3.php",
            "images.php",
			"products_offers1.php",
            "products_offers2.php",
            "products_offers3.php",

			"macros.php",
            "tabs.php"
		),
	),
	"sale" => Array(
		"NAME" => GetMessage("SERVICE_SALE_DEMO_DATA"),
		"STAGES" => Array(
            "locations.php","step1.php", "step2.php", "discount.php", "integration1C.php"
		),
	),
);
?>
