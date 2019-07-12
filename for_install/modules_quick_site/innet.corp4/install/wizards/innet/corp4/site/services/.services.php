<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arServices = Array(
    "main" => Array(
        "NAME" => GetMessage("INNET_SERVICE_MAIN_SETTINGS"),
        "STAGES" => Array(
            "files.php",
            "template.php",
            "theme.php",
			"innetAddEvent.php"
        ),
    ),
    "iblock" => Array(
        "NAME" => GetMessage("INNET_SERVICE_IBLOCK"),
        "STAGES" => Array(
            "types.php",

            "references/references.php",
            "references/references2.php",


            "catalog/catalog.php",


            "objects/articles.php",
            "objects/news.php",
            "objects/partners.php",
            "objects/projects.php",
            "objects/services.php",
            "objects/slider.php",
            "objects/jobs.php",
            "objects/partners_on_maps.php",


            "forms/callback.php",
            "forms/feedback.php",
            "forms/questions_answer.php",

            "forms/catalog_orders.php",
            "forms/catalog_questions.php",
            "forms/catalog_reviews.php",

            "forms/services_orders.php",
            "forms/services_questions.php",

            "forms/projects_orders.php",
            "forms/projects_reviews.php",
        ),
    ),
	"sale" => Array(
        "NAME" => GetMessage("INNET_SERVICE_SALE_DEMO_DATA"),
        "STAGES" => Array(
            "step1.php",
            "step2.php",
            "step3.php"
        ),
    ),
    "catalog" => Array(
        "NAME" => GetMessage("INNET_SERVICE_CATALOG_SETTINGS"),
        "STAGES" => Array(
            "index.php",
        ),
    ),
    "forum" => Array(
        "NAME" => GetMessage("INNET_SERVICE_FORUM")
    )
);
?>