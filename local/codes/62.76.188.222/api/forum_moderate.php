<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
  $APPLICATION->ShowHead();
  
$APPLICATION->ShowCSS();

$APPLICATION->IncludeComponent(
	"bitrix:forum",
	"",
	Array(
		"THEME" => "blue",
		"SHOW_TAGS" => "Y",
		"SEO_USER" => "Y",
		"SHOW_FORUM_USERS" => "Y",
		"SHOW_SUBSCRIBE_LINK" => "N",
		"SHOW_AUTH_FORM" => "Y",
		"SHOW_NAVIGATION" => "Y",
		"SHOW_LEGEND" => "Y",
		"SHOW_STATISTIC_BLOCK" => array( "STATISTIC", 
                                                 "BIRTHDAY", 
                                                 "USERS_ONLINE" ),
		"SHOW_FORUMS" => "Y",
		"SHOW_FIRST_POST" => "N",
		"SHOW_AUTHOR_COLUMN" => "N",
		"TMPLT_SHOW_ADDITIONAL_MARKER" => "",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"PAGE_NAVIGATION_TEMPLATE" => "forum",
		"PAGE_NAVIGATION_WINDOW" => "5",
		"AJAX_POST" => "N",
		"WORD_WRAP_CUT" => "23",
		"WORD_LENGTH" => "50",
		"USE_LIGHT_VIEW" => "Y",
		"SEF_MODE" => "N",
		"CHECK_CORRECT_TEMPLATES" => "Y",
		"FID" => array("1"),
		"USER_PROPERTY" => array(),
		"FORUMS_PER_PAGE" => "10",
		"TOPICS_PER_PAGE" => "10",
		"MESSAGES_PER_PAGE" => "10",
		"TIME_INTERVAL_FOR_USER_STAT" => "10",
		"DATE_FORMAT" => "d.m.Y",
		"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
		"USE_NAME_TEMPLATE" => "N",
		"IMAGE_SIZE" => "500",
		"ATTACH_MODE" => array("NAME"),
		"ATTACH_SIZE" => "90",
		"EDITOR_CODE_DEFAULT" => "N",
		"SEND_MAIL" => "E",
		"SEND_ICQ" => "A",
		"SET_NAVIGATION" => "Y",
		"SET_TITLE" => "Y",
		"SET_DESCRIPTION" => "Y",
		"SET_PAGE_PROPERTY" => "Y",
		"USE_RSS" => "Y",
		"SHOW_FORUM_ANOTHER_SITE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_TIME_USER_STAT" => "60",
		"CACHE_TIME_FOR_FORUM_STAT" => "3600",
		"RATING_ID" => array(),
		"VARIABLE_ALIASES" => Array(
			"FID" => "FID",
			"TID" => "TID",
			"MID" => "MID",
			"UID" => "UID"
		)
	)
); 

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");