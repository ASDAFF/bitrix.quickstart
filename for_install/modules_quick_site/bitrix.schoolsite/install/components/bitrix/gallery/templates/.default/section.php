<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:gallery.view", "", array(
	"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
	"SORT_BY1" => $arParams["SORT_BY1"],
	"SORT_ORDER1" => $arParams["SORT_ORDER1"],
	"SORT_BY2" => $arParams["SORT_BY2"],
	"SORT_ORDER2" => $arParams["SORT_ORDER2"],
	"FILTER_NAME" => $arParams["FILTER_NAME"],
	"INCLUDE_SUBSECTIONS" => "Y",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"GALLERY_ID" => $arParams["GALLERY_GALLERY_ID"],
	"GALLERY_SKIN" => $arParams["GALLERY_GALLERY_SKIN"],
	"SMALL_IMAGE_WIDTH" => $arParams["GALLERY_SMALL_IMAGE_WIDTH"],
	"SMALL_IMAGE_HEIGHT" => $arParams["GALLERY_SMALL_IMAGE_HEIGHT"],
	"SHOW_BIG_IMAGE" => "Y",
	"BIG_IMAGE_WIDTH" => $arParams["GALLERY_BIG_IMAGE_WIDTH"],
	"BIG_IMAGE_HEIGHT" => $arParams["GALLERY_BIG_IMAGE_HEIGHT"],
	"USE_PRELOADER" => "Y",
	"SHOW_IMAGE_CAPTIONS" => "Y"
	),
	false
);?> 