<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?$APPLICATION->IncludeComponent(
	"webdebug:reviews2.list", 
	".default", 
	array(
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"TARGET" => $arParams['TARGET'],
		"INTERFACE_ID" => $arParams['INTERFACE_ID'],
		"TARGET_SUFFIX" => $arParams['TARGET_SUFFIX'],
		"DISPLAY_TOP_PAGER" => $arParams['DISPLAY_TOP_PAGER'],
		"DISPLAY_BOTTOM_PAGER" => $arParams['DISPLAY_BOTTOM_PAGER'],
		"PAGER_TITLE" => $arParams['PAGER_TITLE'],
		"PAGER_SHOW_ALWAYS" => $arParams['PAGER_SHOW_ALWAYS'],
		"PAGER_SHOW_ALL" => $arParams['PAGER_SHOW_ALL'],
		"FILTER_NAME" => $arParams['FILTER_NAME'],
		"COUNT" => $arParams['COUNT'],
		"DATE_FORMAT" => $arParams['DATE_FORMAT'],
		"USER_ANSWER_NAME" => $arParams['USER_ANSWER_NAME'],
		"SHOW_AVATARS" => $arParams['SHOW_AVATARS'],
		"SHOW_ANSWERS" => $arParams['SHOW_ANSWERS'],
		"SHOW_ANSWER_DATE" => $arParams['SHOW_ANSWER_DATE'],
		"SHOW_ANSWER_AVATAR" => $arParams['SHOW_ANSWER_AVATAR'],
		"ALLOW_VOTE" => $arParams['ALLOW_VOTE'],
		"MANUAL_CSS_INCLUDE" => $arParams['MANUAL_CSS_INCLUDE'],
		"SHOW_ALL_IF_ADMIN" => $arParams['SHOW_ALL_IF_ADMIN'],
		"SORT_BY_1" => $arParams['SORT_BY_1'],
		"SORT_ORDER_1" => $arParams['SORT_ORDER_1'],
		"SORT_BY_2" => $arParams['SORT_BY_2'],
		"SORT_ORDER_2" => $arParams['SORT_ORDER_2'],
		"AUTO_LOADING" => $arParams['AUTO_LOADING'],
		
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
	),
	$component
);?>

<br/>

<?$APPLICATION->IncludeComponent(
	"webdebug:reviews2.add", 
	".default", 
	array(
		"CACHE_TYPE" => $arParams['CACHE_TYPE'],
		"CACHE_TIME" => $arParams['CACHE_TIME'],
		"INTERFACE_ID" => $arParams['INTERFACE_ID'],
		"TARGET" => $arParams['TARGET'],
		"INTERFACE_ID" => $arParams['INTERFACE_ID'],
		"TARGET_SUFFIX" => $arParams['TARGET_SUFFIX'],
		"MANUAL_CSS_INCLUDE" => $arParams['MANUAL_CSS_INCLUDE'],
		"MINIMIZE_FORM" => $arParams['MINIMIZE_FORM'],
	),
	$component
);?>

<br/><br/>
