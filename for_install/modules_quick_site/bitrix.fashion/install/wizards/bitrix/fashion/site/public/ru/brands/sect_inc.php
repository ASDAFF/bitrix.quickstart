<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="sidebar" id="sideLeft">
    <ul class="blocks">
    <?$APPLICATION->IncludeComponent("bitrix:menu", "left_brands", array(
        "MODELS_IBLOCK_ID" => "#CATALOG_MODELS_IBLOCK_ID#",
        "ROOT_MENU_TYPE" => "catalog_inc",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "1",
        "CHILD_MENU_TYPE" => "",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N"
        ),
        false
    );?>
    </ul><!-- .blocks -->
</div><!-- .sidebar#sideLeft -->