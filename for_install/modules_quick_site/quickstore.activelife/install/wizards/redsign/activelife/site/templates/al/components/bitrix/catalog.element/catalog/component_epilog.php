<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

use Bitrix\Main\Application;

//$arResult = $templateData;
$bHaveOffer = false;
if (empty($arResult['OFFERS_CACHE'])) {
	$arItemShow = &$arResult;
} else {
	$bHaveOffer = true;
	$arItemShow = &$arResult['OFFERS_CACHE'][$arResult['OFFERS_SELECTED']];
}

$request = Application::getInstance()->getContext()->getRequest();

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $request->get('rs_ajax__page') == 'Y') {
    $APPLICATION->RestartBuffer();
    echo $templateData['TEMPLATE_ELEMENT'];
    ?>
    </div><? // .detail ?>
    <script>
    if (window.jQuery) {
        $.ajax({url:'<?=$templateFolder?>/script.js',type:'GET',dataType:'script',cache:true});
    }
    </script>
    <?php
    die();
}
?>
            <?php if ($arResult['TABS']['REVIEW']): ?>
                <div id="detail_reviews" class="tab-pane fade<?if(!$arResult['TABS']['DETAIL_TEXT'] && !$arResult['TABS']['DISPLAY_PROPERIES'] && !$arResult['TABS']['TAB_PROPERTIES']):?> in active<?endif;?>">
                    <?$APPLICATION->IncludeComponent(
                        'bitrix:forum.topic.reviews',
                        'al',
                        array(
                            "URL_TEMPLATES_DETAIL" => $arParams["REVIEWS_URL_TEMPLATES_DETAIL"],
                            "PAGE_NAVIGATION_TEMPLATE" => $arParams['PAGER_TEMPLATE'],
                            "CACHE_TYPE" => "N",
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
                            "USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
                            "PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
                            "FORUM_ID" => $arParams["FORUM_ID"],
                            "URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
                            "SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
                            "ELEMENT_ID" => $arResult['ID'],
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
                            //'AJAX_POST' => 'N',
                            'AJAX_MODE' => $arParams["REVIEW_AJAX_POST"],
                            "POST_FIRST_MESSAGE" => $arParams["POST_FIRST_MESSAGE"],
                            "SHOW_AVATAR" => "N",
                            "SHOW_RATING" => "N",
                            "RATING_TYPE" => "standart",
                            "PREORDER" => "N",
                            "SHOW_MINIMIZED" => "Y",
                            /*"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
                            "NAME_TEMPLATE" => "",
                            "EDITOR_CODE_DEFAULT" => "Y",
                            "FILES_COUNT" => "2"*/
                        ),
                        $component,
                        array('HIDE_ICONS' => 'Y')
                    );?>
                </div>
            <?php endif; ?>
        </div><? //.nav-tabs ?>
    </div><? //.col-xs-12 ?>

</div><? // .detail ?>

<?php
if ($bHaveOffer && 0 < $arResult['OFFERS_IBLOCK_ID']):
    foreach ($arResult['OFFERS_CACHE'] as $arOffer):
        if ($arOffer['HAVE_SET']): ?>
            <div id="rs_set_<?=$arOffer['ID']?>"<?if($arItemShow['ID'] != $arOffer['ID']):?> style="display: none;"<?endif?>>
                <?$APPLICATION->IncludeComponent('bitrix:catalog.set.constructor',
                    'al',
                    array(
                        'IBLOCK_ID' => $arResult['OFFERS_IBLOCK_ID'],
                        'ELEMENT_ID' => $arOffer['ID'],
                        'PRICE_CODE' => $arParams['PRICE_CODE'],
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
                        'ARTICLE_PROP' => $arParams['ARTICLE_PROP'],
                        'DELIVERY_PROP' => $arParams['DELIVERY_PROP'],
                        'BRAND_LOGO_PROP' => $arParams['BRAND_LOGO_PROP'],
                        'BRAND_PROP' => $arParams['BRAND_PROP'],
                        'ACCESSORIES_PROP' => $arParams['ACCESSORIES_PROP'],
                        'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
                        'USE_QUANTITY_AND_STORES' => $arParams['USE_QUANTITY_AND_STORES'],
                        'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                        'USE_SHARE' => $arParams['USE_SHARE'],
                        'USE_KREDIT' => $arParams['USE_KREDIT'],
                        'USE_LIKES' => $arParams['USE_LIKES'],
                        'USE_COMPARE' => $arParams['USE_COMPARE'],
                        'KREDIT_URL' => $arParams['KREDIT_URL'],
                        'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
                        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        "ADDITIONAL_PICT_PROP_".$arResult['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP'],
                        "ADDITIONAL_PICT_PROP_".$arOffer['IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID']
                    ),
                    null,
                    array('HIDE_ICONS' => 'Y')
                );?>
            </div>
        <?php
        endif;
    endforeach;

elseif ($arResult['HAVE_SET']):
    
    $APPLICATION->IncludeComponent(
        'bitrix:catalog.set.constructor',
        'al',
        array(
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ELEMENT_ID' => $arResult['ID'],
            'PRICE_CODE' => $arParams['PRICE_CODE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'OFFER_ADDITIONAL_PICT_PROP' => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
            "ADDITIONAL_PICT_PROP_".$arResult['IBLOCK_ID'] => $arParams['ADDITIONAL_PICT_PROP'],
            "ADDITIONAL_PICT_PROP_".$arOffer['IBLOCK_ID'] => $arParams['OFFER_ADDITIONAL_PICT_PROP'],
            'ARTICLE_PROP' => $arParams['ARTICLE_PROP'],
            'DELIVERY_PROP' => $arParams['DELIVERY_PROP'],
            'BRAND_LOGO_PROP' => $arParams['BRAND_LOGO_PROP'],
            'BRAND_PROP' => $arParams['BRAND_PROP'],
            'ACCESSORIES_PROP' => $arParams['ACCESSORIES_PROP'],
            'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
            'USE_QUANTITY_AND_STORES' => $arParams['USE_QUANTITY_AND_STORES'],
            'USE_PRODUCT_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'USE_SHARE' => $arParams['USE_SHARE'],
            'USE_KREDIT' => $arParams['USE_KREDIT'],
            'USE_LIKES' => $arParams['USE_LIKES'],
            'USE_COMPARE' => $arParams['USE_COMPARE'],
            'KREDIT_URL' => $arParams['KREDIT_URL'],
            'POPUP_DETAIL_VARIABLE' => $arParams['POPUP_DETAIL_VARIABLE'],
            'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
            'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
            'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID']
        ),
        null,
        array('HIDE_ICONS' => 'Y')
    );

endif;
