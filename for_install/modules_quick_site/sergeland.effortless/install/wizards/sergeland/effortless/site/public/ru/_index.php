<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Effortless - Корпоративный, адаптивный сайт");
?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/extra.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/warning.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/services.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/about.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/callback.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/products.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/products-popular.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/photo.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/testimonials.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/works.php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS", "Y", SITE_ID)))
);?>
<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "#SITE_DIR#include/".(!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_VER", "logo-ver-1", SITE_ID)).".php"
	),
	false,
	array("ACTIVE_COMPONENT" => (!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO", "Y", SITE_ID)))
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>