<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<?php
if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP')
{
	$APPLICATION->IncludeComponent(
		"bitrix:sale.gift.basket",
		"mini",
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
?>

<?php
$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
$normalHidden = ($normalCount == 0) ? 'style="display:none;"' : '';

$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
$delayHidden = ($delayCount == 0) ? 'style="display:none;"' : '';

$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
$subscribeHidden = ($subscribeCount == 0) ? 'style="display:none;"' : '';

$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
$naHidden = ($naCount == 0) ? 'style="display:none;"' : '';
?>

<?php 
    if(
        $normalCount == 0 &&
        $delayCount == 0 &&
        $subscribeCount == 0 &&
        $naCount == 0
    ):
?>
    <div class="alert alert-info" role="alert"><?=Loc::getMessage('SALE_NO_ITEMS');?></div>
<?php else: ?>
<div class="row personal-basket">
    
    <div class="col col-xs-12 col-md-9 personal-basket__sort" role="tablist">
        <ul role="tablist" class="nav nav-basket js-basket-tabs">
            
            <li <?=$normalHidden?>>
                <a class="btn btn-default btn-button" aria-controls="sale_basket_items"  data-toggle="tab" href="#sale_basket_items">
                    <?=Loc::getMessage('SALE_BASKET_ITEMS')?> (<?=$normalCount?>)
                </a>
            </li>
            
            <li <?=$delayHidden?>>
                <a class="btn btn-default btn-button" aria-controls="sale_basket_items_delayed"  data-toggle="tab" href="#sale_basket_items_delayed">
                    <?=Loc::getMessage('SALE_BASKET_ITEMS_DELAYED')?> (<?=$delayCount?>)
                </a>
            </li>
            
            <li <?=$subscribeHidden?>>
                <a class="btn btn-default btn-button" aria-controls="sale_basket_items_subscribed"  data-toggle="tab" href="#sale_basket_items_subscribed">
                    <?=Loc::getMessage('SALE_BASKET_ITEMS_SUBSCRIBED')?> (<?=$subscribeCount?>)
                </a>
            </li>
            
            <li <?=$naHidden?>>
                <a class="btn btn-default btn-button" aria-controls="sale_basket_items_not_available"  data-toggle="tab" href="#sale_basket_items_not_avalaible">
                    <?=Loc::getMessage('SALE_UNAVAIL_TITLE')?> (<?=$naCount?>)
                </a>
            </li>
            
        </ul>
    </div>
    
    <div class="col col-md-3 hidden-sm hidden-xs text-right">
        <a href="javascript:void(0)" class="personal-basket__clear-link" onclick="clearBasket()"><?=Loc::getMessage('SALE_DELETE_ALL')?></a>
    </div>
    
    <div class="col col-xs-12 personal-basket__basket-items tab-content">
            <div role="tabpanel" class="tab-pane" id="sale_basket_items">
                <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder.'/basket_items.php';?>
                    <input type="hidden" name="BasketOrder" value="BasketOrder">
                    <input class="hidden" type="submit" name="BasketRefresh">
                </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="sale_basket_items_delayed">
                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder.'/basket_items_delayed.php';?>
            </div>
            <div role="tabpanel" class="tab-pane" id="sale_basket_items_subscribed">
                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder.'/basket_items_subscribed.php';?>
            </div>
            <div role="tabpanel" class="tab-pane" id="sale_basket_items_not_avalaible">
                <?php include $_SERVER["DOCUMENT_ROOT"].$templateFolder.'/basket_items_not_available.php';?>
            </div>
        
    </div>
    
</div>

<?php
if($arParams['USE_GIFTS'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
{
	?>
	<div style="margin-top: 35px;"><? $APPLICATION->IncludeComponent(
		"bitrix:sale.gift.basket",
		"mini",
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
		    
		    "SHOW_SECTION_URL" => "Y",
		),
		$component,
	    array("HIDE_ICONS" => "Y")
	); ?>
	</div><?
}
?>
<script>
    $(".nav.nav-basket li:visible:eq(0) a").tab('show');
</script>
<?php endif; ?>