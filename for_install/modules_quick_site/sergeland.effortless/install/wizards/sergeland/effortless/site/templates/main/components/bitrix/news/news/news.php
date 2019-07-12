<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "sections",
	),
	$component
);?>
<?if($arParams["USE_RSS"]=="Y"):?>
<div class="block"><p>
	<?if(method_exists($APPLICATION, 'addheadstring'))
		$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" href="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" />');?>
		<a class="pull-right" href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"]?>" title="rss" target="_self"><img alt="RSS" src="<?=$templateFolder?>/images/gif-light/feed-icon-16x16.gif" border="0" /></a>
</p></div>
<div class="clearfix"></div>
<?endif?>
<?
$sort_field = $arParams["SORT_BY1"];
$sort_order = $arParams["SORT_ORDER1"];

if(array_key_exists("desc", $_REQUEST))
{
	$sort_order = "desc";

	if(array_key_exists("name", $_REQUEST))
		$sort_field = "NAME";
		
	if(array_key_exists("date", $_REQUEST))
		$sort_field = "ACTIVE_FROM";		
}

if(array_key_exists("asc", $_REQUEST))
{
	$sort_order = "asc";
	
	if(array_key_exists("name", $_REQUEST))
		$sort_field = "NAME";
		
	if(array_key_exists("date", $_REQUEST))
		$sort_field = "ACTIVE_FROM";
}

$APPLICATION->IncludeComponent("bitrix:news.list","",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"		=>	$arParams["IBLOCK_ID"],
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"		=>	$sort_field,
		"SORT_ORDER1"	=>	$sort_order,
		"SORT_BY2"		=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"		=>	$arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
		"CACHE_GROUPS" 	=> $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"		=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE"			=>	$arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE"		=>	$arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS"		=>	$arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" 	=> $arParams["PAGER_SHOW_ALL"],
		"DISPLAY_DATE"		=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"		=>	"Y",
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"		=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"		=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"			=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES"				=>	$arParams["CHECK_DATES"],
	),
	$component
);?>