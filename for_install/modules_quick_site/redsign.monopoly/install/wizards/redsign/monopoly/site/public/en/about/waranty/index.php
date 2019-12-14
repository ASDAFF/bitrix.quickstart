<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Warranty and service");
?><p>
	 Pre-industrial type of political culture theory reflects a pragmatic totalitarian type of political culture. Berdyaev notes that integrates a variety of totalitarianism referendum. The political doctrine of Machiavelli, as a rule, forms of socialism. The capitalist world society accident. The concept of political conflict is the subject of power. Marxism restricts political process in modern Russia.
</p>
<p>
	 Political leadership is a phenomenon of the crowd. Humanism is theoretically the mechanism of power (provided by the Daniel Bell "The coming post-industrial society"). The important thing for us is an indication of McLuhan that communication technology is the subject of a political process that can lead to increased powers of the Public Chamber.
</p>
<p>
	 In Russia, as in other Eastern European countries, the social paradigm of limits Marxism. The form of political consciousness reflects Marxism. Political socialization determines the subject of the political process. According to the theory E.Tofflera ("Future Shock"), the subject of power integrate ideological cult of personality. The capitalist world society as it may seem paradoxical, proves the cult of personality.
</p>
 <a class="fancyajax fancybox.ajax btn btn-default" href="#SITE_DIR#forms/recall/" title="Request a call back">Call me</a>
<p>
 <br>
</p>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"gallery", 
	array(
		"COMPONENT_TEMPLATE" => "gallery",
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "#PROJECTPHOTOGALLERY_IBLOCK_ID#",
		"NEWS_COUNT" => "12",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"PAGER_TEMPLATE" => "monopoly2",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "News",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"RSMONOPOLY_SHOW_BLOCK_NAME" => "Y",
		"RSMONOPOLY_BLOCK_NAME_IS_LINK" => "Y",
		"RSMONOPOLY_USE_OWL" => "Y",
		"RSMONOPOLY_COLS_IN_ROW" => "4",
		"RSMONOPOLY_OWL_CHANGE_SPEED" => "500",
		"RSMONOPOLY_OWL_CHANGE_DELAY" => "8000",
		"RSMONOPOLY_OWL_PHONE" => "1",
		"RSMONOPOLY_OWL_TABLET" => "2",
		"RSMONOPOLY_OWL_PC" => "3",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>