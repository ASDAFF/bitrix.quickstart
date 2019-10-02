<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$dDiscount = getDealerDiscount();

if (!empty($arResult['ITEMS']))
{
	CJSCore::Init(array("popup"));?>
    
    <?
    $strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

    foreach ($arResult['ITEMS'] as $key => $arItem):
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
    $strMainID = $this->GetEditAreaId($arItem['ID']);

    $arItemIDs = array( // Lot of useful things stored here (From original template)
        'ID' => $strMainID,
        'PICT' => $strMainID.'_pict',
        'MAIN_PROPS' => $strMainID.'_main_props',

        'QUANTITY' => $strMainID.'_quantity',
        'QUANTITY_DOWN' => $strMainID.'_quant_down',
        'QUANTITY_UP' => $strMainID.'_quant_up',
        'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
        'BUY_LINK' => $strMainID.'_buy_link',
        'SUBSCRIBE_LINK' => $strMainID.'_subscribe',

        'PRICE' => $strMainID.'_price',

        'PROP_DIV' => $strMainID.'_sku_tree',
        'PROP' => $strMainID.'_prop_',
        'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop'

    );

    $strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/i", "x", $strMainID);

    ?>
<div class="p-item" id="<?=$strMainID?>">
<div class="row">
    <div class="col-xs-12 pt-20">
        <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="p-title"><? echo $arItem['NAME']; ?></a>
    </div>
</div>
<div class="row"> 
    <div class="col-xs-4 pt-15">
        <a id="<?=$arItemIDs['PICT']?>" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="thumbnail thumb"><img alt="<?=trim($arItem['NAME'])?>" src="<? echo $arItem['PREVIEW_PICTURE']['SRC']; ?>"/></a>
        <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="detailed-link"><i class="glyphicon glyphicon-align-justify"></i> Подробнее</a>
    </div>
    <div class="col-xs-8">
        <div class="row">
            <div class="col-xs-12 pl-0 pt-15 p-descr">
                <div class="row">
                    <div class="col-xs-6">
                        <?/**
                           * Parameters revealed:
                           * $arItem['MIN_PRICE']['VALUE'] - Цена без скидки
                           * $arItem['MIN_PRICE']['DISCOUNT_VALUE'] - Цена со скидкой
                           * $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] - процент разницы (т.е. размер скидки)
                           */
                        ?>
                        <div id="<? echo $arItemIDs['PRICE']; ?>"></div> <? /* Added for the sake of AJAX*/?>
                        <? if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                            <div class="price-old"><? if ($dDiscount):?>Розн.: <?endif?><?=$arItem['MIN_PRICE']['PRINT_VALUE'];?><? if ($arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] != $dDiscount):?><span>АКЦИЯ!</span><? endif; ?></div>
                        <? endif ?>
                        <div class="price-yours"><? if ($dDiscount):?>Опт: <?endif?><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?/*if ($userDiscount < intval($arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'])):?>
                            <div class="price-old"><del><?=$arItem['MIN_PRICE']['PRINT_VALUE'];?></del></div>
                        <?endif?>
                        <?if ($userDiscount == 0 && intval($arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']) > 0):?>
                            <div class="price"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?elseif ($userDiscount == 0 && intval($arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']) == 0):?>
                            <div class="price"><?=$arItem['MIN_PRICE']['PRINT_VALUE'];?></div>
                        <?else:?>
                            <div class="price"><?=number_format(floatval($arItem['MIN_PRICE']['DISCOUNT_VALUE'])/(100-$userDiscount)*100,  2, '.', ' ');?> руб.</div>
                        <?endif?>
                        <? if ($userDiscount > 0):?>
                            <div class="price-yours" title="Ваша цена"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                        <?endif*/?>
                        <? if (!$dDiscount && $arItem['PROPERTIES']['CHECK_AVAIL_R']['VALUE'] == 'Да'): ?>
                            <div class="na">Наличие уточняйте</div>
                        <? elseif ($dDiscount && $arItem['PROPERTIES']['CHECK_AVAIL_D']['VALUE'] == 'Да'): ?>
                            <div class="na">Наличие уточняйте</div>
                        <? elseif ($arItem['CATALOG_QUANTITY']>0):?>
                        <div class="av">В наличии <strong>(<? if($arItem['CATALOG_QUANTITY']>5):?>&gt;5<?else:?>&lt;5<?endif?> шт.)</strong></div>
                        <? else: ?>
                            <? if ($arItem['PROPERTIES']['PREORDER']['VALUE'] == 'Y'):?>
                                <div class="na">Под заказ</div>
                            <? else:?>
                                <div class="na">Ожидается</div>
                            <?endif?>
                        <?endif;?>
                    </div>
                    <div class="col-xs-6 p-buttons">
                        <div class="holder">
                        <noindex>
                        <? if ($arItem['CATALOG_QUANTITY']>0):?>
                            <a href="javascript:void(0)" onclick="Add2Cart(<?=$arItem['ID']?>, '<?=$arItem['DETAIL_PAGE_URL']?>')" rel="nofollow"><span class="ico-main ico-main-basket-on"></span>Купить</a>
                        <? else:?>
                            <span title="Нет в наличии"><span class="ico-main ico-main-basket-off ico-nolink"></span>Купить</span>
                        <? endif;?>
                        <a href="javascript:void(0)" onclick="Add2Compare(<?=$arItem['ID']?>)" rel="nofollow"><span class="ico-main ico-main-compare-off"></span>Сравнить</a>
                        </noindex>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 taj">
                <?=$arItem['PREVIEW_TEXT']?>
                <table class="table">
                    <? if (!empty($arItem['DISPLAY_PROPERTIES'])):?>
                    <? foreach ($arItem['DISPLAY_PROPERTIES'] as &$arOneProp):?>
                    <tr><td class="title"><?=$arOneProp['NAME'];?>:</td><td><?
                        echo (
                            is_array($arOneProp['DISPLAY_VALUE'])
                            ? implode(' / ', $arOneProp['DISPLAY_VALUE'])
                            : $arOneProp['DISPLAY_VALUE']
                        );?></td></tr>
                    <? endforeach; ?>
                    <? endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row m-0">
    <div class="col-xs-12 h-line"></div>
</div>
<?$arJSParams = array(
    'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
    'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
    'SHOW_ADD_BASKET_BTN' => false,
    'SHOW_BUY_BTN' => true,
    'SHOW_ABSENT' => true,
    'PRODUCT' => array(
        'ID' => $arItem['ID'],
        'NAME' => $arItem['~NAME'],
        'PICT' => $arItem['PREVIEW_PICTURE'],
        'CAN_BUY' => $arItem["CAN_BUY"],
        'SUBSCRIPTION' => ('Y' == $arItem['CATALOG_SUBSCRIPTION']),
        'CHECK_QUANTITY' => $arItem['CHECK_QUANTITY'],
        'MAX_QUANTITY' => $arItem['CATALOG_QUANTITY'],
        'STEP_QUANTITY' => $arItem['CATALOG_MEASURE_RATIO'],
        'QUANTITY_FLOAT' => is_double($arItem['CATALOG_MEASURE_RATIO']),
        'ADD_URL' => $arItem['~ADD_URL'],
        'SUBSCRIBE_URL' => $arItem['~SUBSCRIBE_URL']
    ),
    'VISUAL' => array(
        'ID' => $arItemIDs['ID'],
        'PICT_ID' => $arItemIDs['PICT'],
        'QUANTITY_ID' => $arItemIDs['QUANTITY'],
        'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
        'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
        'PRICE_ID' => $arItemIDs['PRICE'],
        'BUY_ID' => $arItemIDs['BUY_LINK'],
    ),
    'AJAX_PATH' => POST_FORM_ACTION_URI
);
?><script type="text/javascript">
var <? echo $strObName; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>
</div>
<?endforeach?>
<script type="text/javascript">
BX.message({
	setButtonBuyName: '<? echo GetMessageJS("CATALOG_SET_BUTTON_BUY"); ?>',
	setButtonBuyUrl: '<? echo $arParams["BASKET_URL"]; ?>',
	ADD_TO_BASKET_OK: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>'
});
</script>
<? if ($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<div class="clearfix"></div>
<? echo $arResult["NAV_STRING"]; ?>
<?endif?>
<?}?>