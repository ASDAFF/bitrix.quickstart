<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Профиль доставки");
$APPLICATION->SetTitle("Профили доставки");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.profile",
	"flyaway",
	array(
		"COMPONENT_TEMPLATE" => "monopoly",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "#SITE_DIR#personal/delivery/",
		"PER_PAGE" => "20",
		"USE_AJAX_LOCATIONS" => "Y",
		"SET_TITLE" => "N",
		"SEF_URL_TEMPLATES" => array(
			"list" => "",
			"detail" => "profil#ID#/",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
