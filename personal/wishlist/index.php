<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранные товары");
?>

<div class="container">
    <div class="row">
        <?$APPLICATION->IncludeComponent(
            "custom:favorites",
            "",
            Array(
                "ACTION_VARIABLE" => "action",
                "ADDITIONAL_PICT_PROP_2" => "MORE_PHOTO",
                "ADDITIONAL_PICT_PROP_3" => "MORE_PHOTO",
                "ADD_PROPERTIES_TO_BASKET" => "N",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "BASKET_URL" => "/personal/cart/",
                "BY" => "AMOUNT",
                "CACHE_TIME" => "86400",
                "CACHE_TYPE" => "A",
                "CART_PROPERTIES_2" => array(0=>"SALELEADER,,",),
                "CART_PROPERTIES_3" => array(0=>",",),
                "COMPONENT_TEMPLATE" => "main.bestsellers",
                "CONVERT_CURRENCY" => "N",
                "DETAIL_URL" => "",
                "DISPLAY_COMPARE" => "Y",
                "FILTER" => array(0=>"N",1=>"P",2=>"F",),
                "HIDE_NOT_AVAILABLE" => "N",
                "LABEL_PROP_2" => "-",
                "LABEL_PROP_3" => "-",
                "LINE_ELEMENT_COUNT" => "4",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "MESS_NOT_AVAILABLE" => "Нет в наличии",
                "PAGE_ELEMENT_COUNT" => "30",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PERIOD" => "180",
                "PRICE_CODE" => array(0=>"BASE",),
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "PRODUCT_SUBSCRIPTION" => "N",
                "PROPERTY_CODE_2" => array(0=>"KEYWORDS",1=>"META_DESCRIPTION",2=>",",3=>"",),
                "PROPERTY_CODE_3" => array(0=>",",),
                "SHOW_DISCOUNT_PERCENT" => "N",
                "SHOW_IMAGE" => "Y",
                "SHOW_NAME" => "Y",
                "SHOW_OLD_PRICE" => "Y",
                "SHOW_PRICE_COUNT" => "1",
                "SHOW_PRODUCTS_2" => "Y",
                "SHOW_PRODUCTS_3" => "N",
                "TEMPLATE_THEME" => "blue",
                "USE_PRODUCT_QUANTITY" => "Y"
            ),
        false,
        Array(
            'ACTIVE_COMPONENT' => 'Y'
        )
        );?>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>