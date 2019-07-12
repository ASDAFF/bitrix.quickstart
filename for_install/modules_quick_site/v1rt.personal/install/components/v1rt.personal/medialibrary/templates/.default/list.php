<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->IncludeComponent(
	"v1rt.personal:medialibrary.gallery",
	"",
	Array(
		"FOLDERS"     => $arParams["FOLDERS"],
        "CHILDREN"    => "Y",
		"DETAIL_URL"  => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"CACHE_TYPE"  => $arParams["CACHE_TYPE"],
		"CACHE_TIME"  => $arParams["CACHE_TIME"],
	),
    $component
);

?>