<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список новостей");
?><?$APPLICATION->IncludeComponent("demo:news.line", ".default", Array(
	"IBLOCK_TYPE"	=>	"news",
	"IBLOCKS"	=>	array(
		0	=>	"3",
	),
	"NEWS_COUNT"	=>	"20",
	"SORT_BY1"	=>	"ACTIVE_FROM",
	"SORT_ORDER1"	=>	"DESC",
	"SORT_BY2"	=>	"SORT",
	"SORT_ORDER2"	=>	"ASC",
	"DETAIL_URL"	=>	"news_detail.php?ID=#ELEMENT_ID#",
	"CACHE_TYPE"	=>	"A",
	"CACHE_TIME"	=>	"300"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>