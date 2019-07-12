<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arParams = (isset($arParams)) ? $arParams : array();
$arResult = (isset($arResult)) ? $arResult : array();
?>
<div class="basket new" id="cart_line_1">
    <a href="#" class="hide-1"><i class="icon-arrow-basket"></i> <?= GetMessage('YOUR_CART_EMPTY') ?> <span
            class="number-basket">(<?= $arResult["NUM_PRODUCTS"] ?>)</span> <span
            class="result-basket"><?= $arResult["SUM"] ?> <?= $arResult["CURRENCY"] ?>.</span></a>


    <div class="list-basket">
        <?
        if($_REQUEST['ajax_buy']!=="1" and !isset($_REQUEST['bxajaxid']))
        {
            $APPLICATION->IncludeComponent("novagroup:eshop.sale.basket.basket", "basket", array(
                    "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
                    "COLUMNS_LIST" => array(
                        0 => "NAME",
                        1 => "PROPS",
                        2 => "PRICE",
                        3 => "QUANTITY",
                        4 => "DELETE",
                        5 => "DELAY",
                        /*6 => "DISCOUNT",*/
                    ),
                    "PATH_TO_ORDER" => SITE_DIR . "cabinet/order/make/",
                    "HIDE_COUPON" => "N",
                    "QUANTITY_FLOAT" => "N",
                    "PRICE_VAT_SHOW_VALUE" => "Y",
                    "SET_TITLE" => "N",
                )
            );
        }
        ?>

    </div>
    <script>
        $(document).click(function (event) {
            if ($(event.target).closest(".list-basket").length)
                return;
            $(".list-basket").slideUp("slow");
            event.stopPropagation();
        });
        $('.hide-1').click(function () {
            $(this).siblings(".list-basket").slideToggle("slow");
            return false;
        });
    </script>
</div>
