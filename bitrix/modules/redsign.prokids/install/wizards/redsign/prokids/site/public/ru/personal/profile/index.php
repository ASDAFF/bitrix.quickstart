<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Профиль");
$APPLICATION->SetTitle("Профиль");
?>

<div class="pmenu">
<?$APPLICATION->IncludeComponent("bitrix:menu", "personal", array(
	"ROOT_MENU_TYPE" => "personal",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "N",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N",
	"SEPARATORS_PLACE" => array(
		0 => "2",
		1 => "5",
		2 => "",
	)
	),
	false
);?>
</div>

<div class="pcontent">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile", 
	"gopro", 
	array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"SET_TITLE" => "N",
		"USER_PROPERTY" => array(
		),
		"SEND_INFO" => "N",
		"CHECK_RIGHTS" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>