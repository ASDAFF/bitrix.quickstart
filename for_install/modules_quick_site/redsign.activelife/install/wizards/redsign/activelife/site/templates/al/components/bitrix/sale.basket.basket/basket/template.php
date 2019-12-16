<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixBasketComponent $component */
$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
    "delete" => $curPage."delete&id=#ID#",
    "delay" => $curPage."delay&id=#ID#",
    "add" => $curPage."add&id=#ID#",
);
unset($curPage);

$arBasketJSParams = array(
    'SALE_DELETE' => GetMessage("SALE_DELETE"),
    'SALE_DELAY' => GetMessage("SALE_DELAY"),
    'SALE_TYPE' => GetMessage("SALE_TYPE"),
    'TEMPLATE_FOLDER' => $templateFolder,
    'DELETE_URL' => $arUrls["delete"],
    'DELAY_URL' => $arUrls["delay"],
    'ADD_URL' => $arUrls["add"],
    'SALE_WEIGHT' => getMessage('SALE_WEIGHT'),
    'SALE_DISCOUNT' => getMessage('SALE_DISCOUNT'),
    'SALE_PRICE_DIFF' => getMessage('RS_SLINE.BSBB_SLINE.PRICE_DIFF'),

    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH,
    'ARTICLE_PROP' => $arParams['ARTICLE_PROP'],
    'OFFER_TREE_COLOR_PROPS' => $arParams['OFFER_TREE_COLOR_PROPS'],
    'OFFER_TREE_BTN_PROPS' => $arParams['OFFER_TREE_BTN_PROPS'],
    'HEADERS' => array(),
    'COLORS_TABLE' => $arResult['COLORS_TABLE']
);
?>
<?
$APPLICATION->AddHeadScript($templateFolder."/script.js");

if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP')
{
    $APPLICATION->IncludeComponent(
        "bitrix:sale.gift.basket",
        ".default",
        array(
            "SHOW_PRICE_COUNT" => 1,
            "PRODUCT_SUBSCRIPTION" => 'N',
            'PRODUCT_ID_VARIABLE' => 'id',
            "PARTIAL_PRODUCT_PROPERTIES" => 'N',
            "USE_PRODUCT_QUANTITY" => 'N',
            "ACTION_VARIABLE" => "actionGift",
            "ADD_PROPERTIES_TO_BASKET" => "Y",

            "BASKET_URL" => $APPLICATION->GetCurPage(),
            "APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
            "FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],

            "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_SHOW_VALUE"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

            'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
            'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
            'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
            'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
            'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
            'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
            'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
            'SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
            'SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
            'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
            'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
            'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
            'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
            'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],

            "LINE_ELEMENT_COUNT" => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
        ),
        false
    );
}

if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
{
    ?>
    <p id="warning_message"<?if(empty($arResult['WARNING_MESSAGE'])){?> style="display:none"<?}?>>
        <?
        if (!empty($arResult["WARNING_MESSAGE"]) && is_array($arResult["WARNING_MESSAGE"]))
        {
            foreach ($arResult["WARNING_MESSAGE"] as $v)
            {
                echo ShowError($v);
            }
        }
        ?>
    </p>
    <?

    $normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
    $normalHidden = ($normalCount == 0) ? 'style="display:none;"' : '';

    $delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
    $delayHidden = ($delayCount == 0) ? 'style="display:none;"' : '';

    $subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
    $subscribeHidden = ($subscribeCount == 0) ? 'style="display:none;"' : '';

    $naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
    $naHidden = ($naCount == 0) ? 'style="display:none;"' : '';

    ?>
        <div id="go_basket" class="go_basket multimage_bottom remove_for_tablet" style="display:none;"><?echo getMessage('RS_SLINE.BSBB_BASKET.EDIT_ORDER')?></div>
        <form class="cart" method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
                <?php
                if ($arResult['ShowReady']=='Y') {
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
                }
                if ($arResult['ShowDelay']=='Y') {
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
                }
                if ($arResult['ShowNotAvail']=='Y') {
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_not_available.php");
                }
                if ($arResult['ShowSubscribe'] == 'Y') {
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");
                }
                ?>
            <input type="submit" class="btn2_180 none1 refresher_delay_delete" name="BasketRefresh">
            <input type="hidden" name="BasketOrder" value="BasketOrder" />
            <!-- <input type="hidden" name="ajax_post" id="ajax_post" value="Y"> -->
        </form>
    <?




    if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
    {
        ?>
        <div style="margin-top: 35px;"><? $APPLICATION->IncludeComponent(
            "bitrix:sale.gift.basket",
            ".default",
            array(
                "SHOW_PRICE_COUNT" => 1,
                "PRODUCT_SUBSCRIPTION" => 'N',
                'PRODUCT_ID_VARIABLE' => 'id',
                "PARTIAL_PRODUCT_PROPERTIES" => 'N',
                "USE_PRODUCT_QUANTITY" => 'N',
                "ACTION_VARIABLE" => "actionGift",
                "ADD_PROPERTIES_TO_BASKET" => "Y",

                "BASKET_URL" => $APPLICATION->GetCurPage(),
                "APPLIED_DISCOUNT_LIST" => $arResult["APPLIED_DISCOUNT_LIST"],
                "FULL_DISCOUNT_LIST" => $arResult["FULL_DISCOUNT_LIST"],

                "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_SHOW_VALUE"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],

                'BLOCK_TITLE' => $arParams['GIFTS_BLOCK_TITLE'],
                'HIDE_BLOCK_TITLE' => $arParams['GIFTS_HIDE_BLOCK_TITLE'],
                'TEXT_LABEL_GIFT' => $arParams['GIFTS_TEXT_LABEL_GIFT'],
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['GIFTS_PRODUCT_QUANTITY_VARIABLE'],
                'PRODUCT_PROPS_VARIABLE' => $arParams['GIFTS_PRODUCT_PROPS_VARIABLE'],
                'SHOW_OLD_PRICE' => $arParams['GIFTS_SHOW_OLD_PRICE'],
                'SHOW_DISCOUNT_PERCENT' => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                'SHOW_NAME' => $arParams['GIFTS_SHOW_NAME'],
                'SHOW_IMAGE' => $arParams['GIFTS_SHOW_IMAGE'],
                'MESS_BTN_BUY' => $arParams['GIFTS_MESS_BTN_BUY'],
                'MESS_BTN_DETAIL' => $arParams['GIFTS_MESS_BTN_DETAIL'],
                'PAGE_ELEMENT_COUNT' => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
                'CONVERT_CURRENCY' => $arParams['GIFTS_CONVERT_CURRENCY'],
                'HIDE_NOT_AVAILABLE' => $arParams['GIFTS_HIDE_NOT_AVAILABLE'],

                "LINE_ELEMENT_COUNT" => $arParams['GIFTS_PAGE_ELEMENT_COUNT'],
            ),
            false
        ); ?>
        </div><?
    }
}
else
{
    ShowError($arResult["ERROR_MESSAGE"]);
}
?><script>
var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>,
    kolbas=<?echo ($normalCount) ? $normalCount : 0;?>;
$(document).ready(function(){
    if (!kolbas)
    {
        $('#over_fon2, #go_order').hide();
    }
    else
    {
        $('#over_fon2').show();
    }
});
</script><?