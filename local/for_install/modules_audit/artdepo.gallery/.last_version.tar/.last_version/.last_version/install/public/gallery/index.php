<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage("ADG_INSTALL_PUBLIC_TITLE_INDEX"));?>

<?$APPLICATION->IncludeComponent("artdepo:gallery.album.list", ".default", array(
	"SECTION_ID" => "2",
	"LANGUAGE_ID" => "en",
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "SORT",
	"SORT_ORDER1" => "ASC",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_COUNT" => "Y",
	"DETAIL_URL" => "album/?ID=#ID#",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"CACHE_GROUPS" => "Y",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"NAME_TRUNCATE_LEN" => "",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
