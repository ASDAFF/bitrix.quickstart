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
<div class="p-item" id="<?=$strMainID?>" style="">
<div class="row">
    <div class="col-xs-12 pt-20" >
        <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="p-title"><? echo $arItem['NAME']; ?></a>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 pt-15">

        <a id="<?=$arItemIDs['PICT']?>" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="thumbnail thumb"><img alt="<?=trim($arItem['NAME'])?>" src="<? echo $arItem['PREVIEW_PICTURE']['SRC']; ?>"/></a>
        <a href="<? echo $arItem['DETAIL_PAGE_URL']; ?>" class="btn btn-blue detailed-link "><i class="glyphicon glyphicon-align-justify"></i> Подробнее</a>
        <? if ($arItem['PROPERTIES']['WARRANTY_2YEARS']['VALUE'] == 'Да'):?>
        <a href="/service/rasshirennaya-garantiya/"><img src="<?=SITE_TEMPLATE_PATH?>/images/warranty.png" /></a><img src="<?=SITE_TEMPLATE_PATH?>/images/ce.png" style="width: 38%; margin-left: 5px;" />
        <?endif?>
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
						<div class="av">Есть в наличии</div>
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
							<style>
					.btn-buy {background: #179917;font-size: 17px;padding:8px 25px 8px 10px;text-align:center;border-radius:4px;-moz-border-radius:4px 4px 4px 4px;-o-border-radius:4px 4px 4px 4px}
					.btn-buy:hover {background: #118811}
					</style>
								<? if ($arItem['CATALOG_QUANTITY']>0):?>
<a href="javascript:void(0)" onclick="Add2Cart(<?=$arItem['ID']?>, '<?=$arItem['DETAIL_PAGE_URL']?>')" rel="nofollow" style="color:#eee;text-decoration:none"><div onclick="ym(4916878, 'reachGoal', 'click_buy'); return true;" class="btn-buy"> <span class="ico-main ico-main-basket-off"></span> Купить</div></a>
									<div class="clearfix"></div>

                                <?else:?>
                                    <div class="good-buttons">
                                        <a href="#analogue-form" class="btn-analogue" data-good-title="<?=$arItem["NAME"]?>" data-good-id="<?=$arItem["ID"]?>" data-good-picture="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" onclick="ym(4916878, 'reachGoal', 'btn_analogue'); return true;">Подобрать аналог</a>

                                        <a href="#announce-form" class="lin-announce-good" data-good-title="<?=$arItem["NAME"]?>" data-good-id="<?=$arItem["ID"]?>" data-good-picture="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" onclick="ym(4916878, 'reachGoal', 'lin-announce-good'); return true;"><i class="fa fa-envelope"></i> <span>Сообщить о поступлении</span></a>
                                    </div>
								<?endif?>
<a class="btn-main" rel="nofollow" href="javascript:void(0);" onclick="BuyOnCredit(<?php echo $arItem['ID']; ?>, '<?=$arItem['DETAIL_PAGE_URL']?>', 2);ym(4916878, 'reachGoal', 'click_credit'); return true;" title="Купить в кредит"><img src="/upload/resize_cache/medialibrary/304/140_105_1/calculator.png" alt="Калькулятор" width="17" style="margin-right: 6px;"/>В кредит</a>

								<a href="javascript:void(0)" onclick="Add2Compare(<?=$arItem['ID']?>);ym(4916878, 'reachGoal', 'click_compare'); return true;" rel="nofollow"><span class="ico-main ico-main-compare-off"></span>Сравнить</a>
								<? if ($arItem['CATALOG_QUANTITY']>0):?>

								<? else:?>
									<span title="Нет в наличии"></span>
								<? endif;?>
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

<?/* if ($arResult['SECTION']['ADD_DESCRIPTION']):?>
<div class="add-desc">
<?=$arResult['SECTION']['ADD_DESCRIPTION'];?>

</div>
<? endif*/?>
<? if ($arParams["DISPLAY_BOTTOM_PAGER"]):?>
<div class="clearfix"></div>
<? echo $arResult["NAV_STRING"]; ?>
<?endif?>
<?}?>
<?if($_GET['test']):?>
<?/*$arPagePropertySection = CIBlockSection::GetList(
            Array("SORT"=>"ASC"),
            Array('ID'=>$arResult['ID'], 'IBLOCK_ID'=>$arResult["IBLOCK_ID"]),
            false,
            Array('ID','NAME','UF_H1','UF_TITLE_TEXT','UF_DESCRIPTION','UF_KEYWORDS'),
            false
        );
   echo '<pre>';
   print_r($arResult);
  while($pagePropertySection = $arPagePropertySection->GetNext())
   {
        echo '<pre>';
        print_r($pagePropertySection);

        $H1 = htmlspecialchars_decode($pagePropertySection['UF_H1']);
        $TITLE = htmlspecialchars_decode($pagePropertySection['UF_TITLE_TEXT']);
        $DESC = htmlspecialchars_decode($pagePropertySection['UF_DESCRIPTION']);
        $KEYWORDS = htmlspecialchars_decode($pagePropertySection['UF_KEYWORDS']);
   }
   //echo ' -- ' . $H1 . ' <br>-- ' . $TITLE . ' <br>-- ' . $DESC . '<br>-- ' .$KEYWORDS . '<br><br>';
    $APPLICATION->SetPageProperty("title",$TITLE);
    $APPLICATION->SetPageProperty("description",$DESC);
    $APPLICATION->SetPageProperty("keywords", $KEYWORDS);
    */?>
<?endif;?>