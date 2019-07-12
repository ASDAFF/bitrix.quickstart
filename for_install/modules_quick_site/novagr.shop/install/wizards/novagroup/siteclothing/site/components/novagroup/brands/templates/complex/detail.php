<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); $this->setFrameMode(true) ?>
<?$APPLICATION->IncludeComponent(
    "novagroup:brands.element",
    ".default",
    Array(
        "BRANDS_IBLOCK_CODE" => "vendor",
        "BRAID_ID" => $_REQUEST['id'],
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "360000"
    )
);?>