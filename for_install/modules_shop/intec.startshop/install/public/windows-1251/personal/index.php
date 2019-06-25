<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>
<?if ($USER->IsAuthorized()):?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:menu",
		"startshop.top.1",
		array(
			"COMPONENT_TEMPLATE" => "startshop.top.1",
			"ROOT_MENU_TYPE" => "personal",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => array(
			),
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "personal",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
		),
		false
	);?>
<?endif;?>
<?$APPLICATION->IncludeComponent(
	"intec:startshop.profile",
	".default",
	array(
			"USE_ADAPTABILITY" => "N"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

