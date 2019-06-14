<?$APPLICATION->AddHeadString("<link href='http://fonts.googleapis.com/css?family=PT+Sans:700,400&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' href='/bitrix/components/mcart.libereya/libereya/styles/libereya.css'>");?>
<?
CJSCore::RegisterExt('lib', array(	
'js' => '/bitrix/components/mcart.libereya/libereya/js/lib.js',	
//'css' => '/bitrix/js/your_module/css/functions.css',	
'lang' => '/bitrix/modules/mcart.libereya/lang/'.LANGUAGE_ID.'/lib_js.php',	
'rel' => array('jquery') 
));
CJSCore::RegisterExt('tinyscrollbar', array(	
'js' => "/bitrix/components/mcart.libereya/libereya/js/tinyscrollbar.min.js",		
'rel' => array('jquery') 
));
CJSCore::RegisterExt('selectbox', array(	
'js' => "/bitrix/components/mcart.libereya/libereya/js/selectbox.min.js",		
'rel' => array('jquery') 
));
?>
<?CJSCore::Init(array("ajax", "window", 'lib', 'tinyscrollbar', 'selectbox'));?>


<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="contentbox">
	<div class="content content-library">


<?if($arParams["USE_FILTER"]=="Y"):?>

<?
$linked_elements = $APPLICATION->IncludeComponent(
	"mcart.libereya:libereya.filter",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"BLOCK_HTML_ID"	=> 	'sort-1'
	),
	$component
);
?>
<?
if(empty($linked_elements)):
	ShowError(GetMessage('MCART_LIBEREYA_EMPTY_AUTHORS_AND_GANRES'));
endif;
?>
<br />
<?endif?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"catalog",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
		"LINKED_ELEMENTS"	=> $linked_elements,
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"	=>	$arParams["SORT_BY1"],
		"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
		"SORT_BY2"	=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"	=>	$arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"	=>	"Y",
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"USE_RATING"	=>	$arParams["USE_RATING"],
		"MAX_VOTE"	=>	$arParams["MAX_VOTE"],
		"VOTE_NAMES"	=>	$arParams["VOTE_NAMES"],
		"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
	),
	$component
);?>

<?if($arParams["USE_FILTER"]=="Y"):?>

<?$APPLICATION->IncludeComponent(
	"mcart.libereya:libereya.filter",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"BLOCK_HTML_ID"	=> 	'sort-2'
	),
	$component
);
?>
<br />
<?endif?>

	</div>
</div>

