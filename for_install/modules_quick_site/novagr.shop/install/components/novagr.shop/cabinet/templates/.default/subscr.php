<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
    "bitrix:subscribe.edit",
    "demoshop_clear",
    Array(
        "AJAX_MODE" => "N",
        "SHOW_HIDDEN" => "N",
        "ALLOW_ANONYMOUS" => "Y",
        "SHOW_AUTH_LINKS" => "Y",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "360000",
        "SET_TITLE" => "N",
        "AJAX_OPTION_SHADOW" => "Y",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N"
    ),
    $component
    );?>

