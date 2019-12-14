<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!\Bitrix\Main\Loader::includeModule('redsign.monopoly'))
	return;

// popup gallery
require_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/popupgallery.php');

$ElementID = $APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	$arParams['RSMONOPOLY_DETAIL_TEMPLATES'],
	Array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" 			=> $arParams["USE_SHARE"],
		"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
		// monopoly
		"RSMONOPOLY_PROP_MARKER_TEXT"		=> $arParams["RSMONOPOLY_PROP_MARKER_TEXT_DETAIL"],
		"RSMONOPOLY_PROP_MARKER_COLOR"		=> $arParams["RSMONOPOLY_PROP_MARKER_COLOR_DETAIL"],
		"RSMONOPOLY_PROP_ACTION_DATE"		=> $arParams["RSMONOPOLY_PROP_ACTION_DATE_DETAIL"],
		"RSMONOPOLY_PROP_MORE_PHOTO" 		=> $arParams["RSMONOPOLY_PROP_MORE_PHOTO"],
	),
	$component
);


?><div class="row backshare"><?
	?><div class="col col-md-6"><?
		?><a class="detailback" href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"]?>"><i class="fa"></i><span><?=GetMessage("RS.MONOPOLY.BACK")?></span></a><?
	?></div><?
	?><div class="col col-md-6 yashare"><?
		?><span><?=GetMessage("RS.MONOPOLY.YASHARE")?></span><?
		?><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook,twitter,gplus"></div><?
	?></div><?
?></div><?


if( IsModuleInstalled('subscribe') && $arParams['RSMONOPOLY_DETAIL_USE_SUBSCRIBE']=='Y' && $arParams['RSMONOPOLY_DETAIL_SUBSCRIBE_PAGE']!='' ) {
	$APPLICATION->IncludeComponent(
		"bitrix:subscribe.form", 
		"detail", 
		array(
			"COMPONENT_TEMPLATE" => "detail",
			"USE_PERSONALIZATION" => "Y",
			"SHOW_HIDDEN" => "N",
			"PAGE" => $arParams['RSMONOPOLY_DETAIL_SUBSCRIBE_PAGE'],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"RSMONOPOLY_DETAIL_SUBSCRIBE_NOTE" => $arParams["RSMONOPOLY_DETAIL_SUBSCRIBE_NOTE"],
		),
		$component,
		array('HIDE_ICONS'=>'Y')
	);
}


if( $arParams['RSMONOPOLY_LIST_TEMPLATES_DETAIL_USE']=='Y' ) {
	$APPLICATION->IncludeComponent(
		"bitrix:news.list", 
		$arParams['RSMONOPOLY_LIST_TEMPLATES_DETAIL'],
		array(
			"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
			"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
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
			"SET_TITLE"	=>	"N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN"	=>	"N",
			"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
			"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
			"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"DISPLAY_TOP_PAGER"	=>	"N",
			"DISPLAY_BOTTOM_PAGER"	=>	"N",
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
			"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
			// monopoly
			"RSMONOPOLY_SHOW_BLOCK_NAME"		=> $arParams["RSMONOPOLY_SHOW_BLOCK_NAME_DETAIL"],
			"RSMONOPOLY_BLOCK_NAME_IS_LINK"		=> $arParams["RSMONOPOLY_BLOCK_NAME_IS_LINK_DETAIL"],
			"RSMONOPOLY_USE_OWL"				=> $arParams["RSMONOPOLY_USE_OWL_DETAIL"],
			"RSMONOPOLY_OWL_CHANGE_SPEED"		=> $arParams["RSMONOPOLY_OWL_CHANGE_SPEED_DETAIL"],
			"RSMONOPOLY_OWL_CHANGE_DELAY"		=> $arParams["RSMONOPOLY_OWL_CHANGE_DELAY_DETAIL"],
			"RSMONOPOLY_OWL_PHONE"				=> $arParams["RSMONOPOLY_OWL_PHONE_DETAIL"],
			"RSMONOPOLY_OWL_TABLET"				=> $arParams["RSMONOPOLY_OWL_TABLET_DETAIL"],
			"RSMONOPOLY_OWL_PC"					=> $arParams["RSMONOPOLY_OWL_PC_DETAIL"],
			"RSMONOPOLY_COLS_IN_ROW"			=> $arParams["RSMONOPOLY_COLS_IN_ROW_DETAIL"],
			"RSMONOPOLY_PROP_PUBLISHER_NAME"	=> $arParams["RSMONOPOLY_PROP_PUBLISHER_NAME_DETAIL"],
			"RSMONOPOLY_PROP_PUBLISHER_BLANK"	=> $arParams["RSMONOPOLY_PROP_PUBLISHER_BLANK_DETAIL"],
			"RSMONOPOLY_PROP_PUBLISHER_DESCR"	=> $arParams["RSMONOPOLY_PROP_PUBLISHER_DESCR_DETAIL"],
			"RSMONOPOLY_PROP_MARKER_TEXT"		=> $arParams["RSMONOPOLY_PROP_MARKER_TEXT_DETAIL"],
			"RSMONOPOLY_PROP_MARKER_COLOR"		=> $arParams["RSMONOPOLY_PROP_MARKER_COLOR_DETAIL"],
			"RSMONOPOLY_PROP_ACTION_DATE"		=> $arParams["RSMONOPOLY_PROP_ACTION_DATE_DETAIL"],
			"RSMONOPOLY_PROP_FILE"				=> $arParams["RSMONOPOLY_PROP_FILE_DETAIL"],
			"RSMONOPOLY_LINK"					=> $arParams["RSMONOPOLY_LINK_DETAIL"],
			"RSMONOPOLY_BLANK"					=> $arParams["RSMONOPOLY_BLANK_DETAIL"],
			"RSMONOPOLY_SHOW_DATE"				=> $arParams["RSMONOPOLY_SHOW_DATE_DETAIL"],
			"RSMONOPOLY_AUTHOR_NAME"			=> $arParams["RSMONOPOLY_AUTHOR_NAME_DETAIL"],
			"RSMONOPOLY_AUTHOR_JOB"				=> $arParams["RSMONOPOLY_AUTHOR_JOB_DETAIL"],
			"RSMONOPOLY_SHOW_BUTTON"			=> $arParams["RSMONOPOLY_SHOW_BUTTON_DETAIL"],
			"RSMONOPOLY_BUTTON_CAPTION"			=> $arParams["RSMONOPOLY_BUTTON_CAPTION_DETAIL"],
			"RSMONOPOLY_PROP_VACANCY_TYPE"		=> $arParams["RSMONOPOLY_PROP_VACANCY_TYPE_DETAIL"],
			"RSMONOPOLY_PROP_SIGNATURE"			=> $arParams["RSMONOPOLY_PROP_SIGNATURE_DETAIL"],
		),
		$component
	);
}