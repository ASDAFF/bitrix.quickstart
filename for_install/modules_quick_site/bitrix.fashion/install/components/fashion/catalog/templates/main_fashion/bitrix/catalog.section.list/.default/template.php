<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (strlen($arResult["SECTIONS"][0]["SECTION_PAGE_URL"]) > 0) {
    LocalRedirect($arResult["SECTIONS"][0]["SECTION_PAGE_URL"]);
}

LocalRedirect(SITE_DIR);?>