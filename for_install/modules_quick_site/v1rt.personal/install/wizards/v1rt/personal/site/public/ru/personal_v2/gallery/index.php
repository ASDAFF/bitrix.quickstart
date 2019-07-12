<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Фотогалерея");
?><?$APPLICATION->IncludeComponent(
	"v1rt.personal:medialibrary",
	"",
	Array(
		"FOLDERS" => array("#PHOTO_GALLERY_ID#"),
		"SEF_MODE" => "Y",
		"TITLE" => "Y",
		"RESIZE_MODE" => "F",
		"RESIZE_MODE_W" => "130",
		"RESIZE_MODE_H" => "130",
		"PAGE_NAV_MODE" => "Y",
		"ELEMENT_PAGE" => "10",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TITLE" => "",
		"PAGER_TEMPLATE" => "modern",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"SEF_FOLDER" => "#SITE_DIR#gallery/",
		"SEF_URL_TEMPLATES" => Array(
			"list" => "/",
			"detail" => "#GALLERY_ID#/"
		),
		"VARIABLE_ALIASES" => Array(
			"list" => Array(),
			"detail" => Array(),
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>