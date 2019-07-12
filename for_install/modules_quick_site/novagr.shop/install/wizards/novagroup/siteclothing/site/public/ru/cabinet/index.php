<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); $APPLICATION->SetTitle("Персональный раздел");?> <?$APPLICATION->IncludeComponent(
    "novagr.shop:cabinet",
    ".default",
    Array(
        "SEF_MODE" => "Y",
        "AJAX_MODE" => "Y",
        "SET_STATUS_404" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_NOTES" => "",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "SEF_FOLDER" => "#SITE_DIR#cabinet/",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "SEF_URL_TEMPLATES" => Array(
            "cart" => "",
            "orders" => "orders/",
            "subscr" => "subscr/",
            "userinfo" => "userinfo/",
            "cancel" => "cancel/?ID=#ID#"
        ),
        "VARIABLE_ALIASES" => Array(
            "cart" => Array(),
            "orders" => Array(),
            "subscr" => Array(),
            "userinfo" => Array(),
            "cancel" => Array(
                "ID" => "ID"
            ),
        )
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>