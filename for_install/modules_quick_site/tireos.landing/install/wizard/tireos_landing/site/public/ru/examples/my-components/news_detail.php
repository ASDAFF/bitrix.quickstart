<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

<?$APPLICATION->IncludeComponent("demo:news.detail", ".default", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCK_ID"	=>	"3",
	"ELEMENT_ID"	=>	$_REQUEST["ID"],
	"IBLOCK_URL"	=>	"news_list.php",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"3600",
	"DISPLAY_PANEL"	=>	"N",
	"SET_TITLE"	=>	"Y",
	"ADD_SECTIONS_CHAIN"	=>	"N",
	"DISPLAY_DATE"	=>	"Y",
	"DISPLAY_NAME"	=>	"N",
	"DISPLAY_PICTURE"	=>	"Y"
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>