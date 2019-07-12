<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->IncludeComponent("v1rt.personal:medialibrary.view", "", array(
        "FOLDERS"           => $arResult["VARIABLES"]["GALLERY_ID"],
       	"VARIABLE"          => "",
       	"COUNT_IMAGE"       => "",
       	"RANDOM"            => "N",
       	"TITLE"             => $arParams["TITLE"],
       	"CACHE_TYPE"        => $arParams["CACHE_TYPE"],
		"CACHE_TIME"        => $arParams["CACHE_TIME"],
       	"PAGE_NAV_MODE"     => $arParams["PAGE_NAV_MODE"],
       	"ELEMENT_PAGE"      => $arParams["ELEMENT_PAGE"],
       	"PAGER_SHOW_ALL"    => $arParams["PAGER_SHOW_ALL"],
       	"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
       	"PAGER_TITLE"       => $arParams["PAGER_TITLE"],
       	"PAGER_TEMPLATE"    => $arParams["PAGER_TEMPLATE"],
       	"RESIZE_MODE"       => $arParams["RESIZE_MODE"],
       	"RESIZE_MODE_W"     => $arParams["RESIZE_MODE_W"],
       	"RESIZE_MODE_H"     => $arParams["RESIZE_MODE_H"],
       	"PAGE_LINK"         => "",
       	"PAGE_LINK_TEXT"    => "",
       	"LOAD_JS"           => "Y"
   	),
   	$component
);

?>