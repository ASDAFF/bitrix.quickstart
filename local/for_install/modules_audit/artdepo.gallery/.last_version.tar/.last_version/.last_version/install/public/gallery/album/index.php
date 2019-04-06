<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage("ADG_INSTALL_PUBLIC_TITLE_ALBUM_INDEX"));?>

<?$APPLICATION->IncludeComponent(
	"artdepo:gallery.photo.list",
	"rectangle",
	Array(
		"POPUP_TEMPLATE" => "magnific",
		"SECTION_ID" => "2",
		"PARENT_ID" => $_REQUEST["ID"],
		"LANGUAGE_ID" => "en",
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"DISPLAY_NAME" => "N",
		"NAME_TRUNCATE_LEN" => "",
		"SKIP_FIRST" => "N",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",
		"BACK_URL" => "../",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
