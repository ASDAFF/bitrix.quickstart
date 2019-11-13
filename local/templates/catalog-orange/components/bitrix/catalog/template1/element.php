<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="breadcrumb" <?if(!is_array($arEl)){?>style="margin-bottom:10px;"<?}?>>
	<?
	$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
		"START_FROM" => "1",
		"PATH" => "",
		"SITE_ID" => "-"
		),
		false
	);
	?>
</div>

<?$ElementID=$APPLICATION->IncludeComponent("bitrix:catalog.element", ".default", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
	"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
	"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
	"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
	"PROPERTY_CODE" => array(
		0 => "MANUFACTURER",
		1 => "PROP8",
		2 => "PROP9",
		3 => "PROP10",
		4 => "RECOMMEND",
		5 => "",
	),
	"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
	"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
	"BASKET_URL" => $arParams["BASKET_URL"],
	"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
	"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => "N",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"ADD_SECTIONS_CHAIN" => "Y",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
	"PRICE_VAT_INCLUDE" => "N",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "Y",
	"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
	"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
	"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
	"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"]
	),
	$component
);?>

<?global $assignFilter;?>
<?if($assignFilter["ID"][0]){?>
<h2 class="assign-title"><?=GetMessage("CATALOG_ASSIGNITEMS")?></h2>
<?$APPLICATION->IncludeComponent("g-tech:catalog.section", "assign_items", array(
	"IBLOCK_TYPE" => "catalog",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "active_from",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "assignFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "Y",
	"PAGE_ELEMENT_COUNT" => "6",
	"LINE_ELEMENT_COUNT" => "3",
	"PROPERTY_CODE" => array(
		0 => "MANUFACTURER",
		1 => "PROP8",
		2 => "",
	),
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/cart/",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "modern",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<?}?>

<p><span style="font-family:Verdana; font-size:12px; color:#000000; font-weight:bold; border-bottom:dashed 1px #000000;"><?=GetMessage("CATALOG_ITEM_REVIEWS")?></span><br/></p>
                <?if(CModule::IncludeModule("forum")){$arForumReview = CForumNew::GetList(array(),array("TEXT"=>"Отзывы к товарам"))->Fetch();}?>
                <?$APPLICATION->IncludeComponent("bitrix:forum.topic.reviews", "reviews", array(
	"FORUM_ID" => $arForumReview["ID"],
	"IBLOCK_TYPE" => "catalogs",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ELEMENT_ID" => $ElementID,
	"POST_FIRST_MESSAGE" => "N",
	"POST_FIRST_MESSAGE_TEMPLATE" => "",
	"URL_TEMPLATES_READ" => "",
	"URL_TEMPLATES_DETAIL" => "",
	"URL_TEMPLATES_PROFILE_VIEW" => "",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"MESSAGES_PER_PAGE" => "10",
	"PAGE_NAVIGATION_TEMPLATE" => "modern",
	"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
	"NAME_TEMPLATE" => "",
	"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
	"EDITOR_CODE_DEFAULT" => "N",
	"SHOW_AVATAR" => "N",
	"SHOW_RATING" => "Y",
	"RATING_TYPE" => "standart_text",
	"SHOW_MINIMIZED" => "Y",
	"USE_CAPTCHA" => "Y",
	"PREORDER" => "Y",
	"SHOW_LINK_TO_FORUM" => "N",
	"FILES_COUNT" => "0",
	"AJAX_POST" => "Y"
	),
	false
);?>