<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$dDiscount = getDealerDiscount();

$useBrands = ('Y' == $arParams['BRAND_USE']);

//print_r($arResult);
?>
<div itemscope itemtype="//schema.org/Product" class="c-item" id="<? echo $arResult['ID']; ?>">
<meta itemprop="brand" content="Atis" />
<div class="modal fade phts-modal" id="phtsModal" tabindex="-1" role="dialog" aria-labelledby="phtsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="phtsModalLabel">Фотогалерея <?=$arResult['NAME']?></h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
                <div class="ph-border">
                <a class="thumbnail s-thumb" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" data-lightbox="p-photo"><img alt="<?=trim($arResult['NAME'])?>" src="<?=$arResult['DETAIL_PICTURE']['SRC'];?>" /></a>
                <a style="display: block;" href="/save.php?r=<?=$arResult['DETAIL_PICTURE']['SRC']?>" target="_blank"><i class="glyphicon glyphicon-floppy-save"></i> Сохранить</a>
                </div>

                    <? foreach ($arResult['MORE_PHOTO'] as $pht):?>
                    <div class="ph-border">
                        <a class="thumbnail s-thumb" href="<?=$pht['SRC']?>" data-lightbox="p-photo" title='<a href="/save.php?r=<?=$pht['SRC']?>"><i class="glyphicon glyphicon-floppy-save"></i> Сохранить</a>'><img src="<?=$pht['SRC']?>" /></a>
                        <a style="display: block;" href="/save.php?r=<?=$pht['SRC']?>"><i class="glyphicon glyphicon-floppy-save"></i> Сохранить</a>
                    </div>
                    <? endforeach?>

            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-red" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<h1 itemprop="name"><? echo $arResult['NAME']; ?></h1>
<div class="row">
    <div class="col-xs-4">
        <a class="thumbnail thumb" href="<? echo $arResult['DETAIL_PICTURE']['SRC']?>" data-lightbox="p-photo" title='<a href="/save.php?r=<?=$pht['SRC']?>"><i class="glyphicon glyphicon-floppy-save"></i> Сохранить</a>'><img src="<? echo $arResult['DETAIL_PICTURE']['SRC']; ?>" /></a>
        <?/*<a class="thumbnail thumb" href="javascript:void(0)" onclick="$('#phtsModal').modal();"><img alt="<?=trim($arResult['NAME'])?>" src="<?=$arResult['PREVIEW_PICTURE']['SRC']; ?>" itemprop="image" /></a>*/?>


            <div class="owl-carousel">
            <? foreach ($arResult['MORE_PHOTO'] as $pht):?>
                <a class="thumbnail o-thumb" href="<?=$pht['SRC']?>" data-lightbox="p-photo" title='<a href="/save.php?r=<?=$pht['SRC']?>"><i class="glyphicon glyphicon-floppy-save"></i> Сохранить</a>'><img src="<?=$pht['SRC']?>" /></a>
            <?endforeach;?>
            </div>

        <a class="all-pht" href="javascript:void(0);" data-toggle="modal" data-target=".phts-modal"><span class="glyphicon glyphicon-camera"></span>Все фото</a><br /><br />

        <div class="clearfix"></div>
        <ul class="c-lmenu">
            <li><a target="_blank" href="/service/payment/"><span class="ico-main ico-main-card-on"></span> Оплата</a></li>
            <li><a target="_blank" href="/service/shipping/"><span class="ico-main ico-main-car-on"></span> Доставка</a></li>
            <li><a target="_blank" href="/service/guarantee/"><span class="ico-main ico-main-shield-on"></span> Гарантии</a></li>
            <li><a target="_blank" href="/service/service/"><span class="ico-main ico-main-gears-on"></span> Сервис</a></li>
        </ul>

        <?
        $arrJson = array(
            'name' => $arResult['NAME'],
            'description' => strip_tags($arResult['DETAIL_TEXT']),
            'photo' => 'https://atis-auto.ru'.$arResult['PREVIEW_PICTURE']['SRC'],
            'price' => $arResult['MIN_PRICE']['PRINT_VALUE'],
        );
        $props = array();
        if (!empty($arResult['DISPLAY_PROPERTIES'])):
        foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp):
            $props[] = array('name' => $arOneProp['NAME'], 'value' => $arOneProp['DISPLAY_VALUE']);
        endforeach;
        endif;
        $arrJson['properties'] = $props;
        $arrJson['options'] = getGoodOptions($arResult['PROPERTIES']['OPTIONS']['VALUE']);
        $arrJson['features'] = $arResult['GOOD_FEATURES'];
        ?>
        <form action="/export/generator.php?action=card" method="post">
            <input name="good" type="hidden" value="<?=base64_encode(json_encode($arrJson));?>" />
            <button type="submit" class="btn btn-blue"><i class="fa fa-file-word-o"></i> Коммерческое предложение</button>
        </form>

        <? if ($arResult['PROPERTIES']['WARRANTY_2YEARS']['VALUE'] == 'Да'):?>
        <br>
        <a href="/infocenter/garantiya-2-goda/"><img src="<?=SITE_TEMPLATE_PATH?>/images/warranty.png" /></a> <img src="<?=SITE_TEMPLATE_PATH?>/images/ce.png" style="width: 38%; margin-left: 5px;" />
        <?endif?>
</div>
    <div class="col-xs-8 pl-0" itemprop="offers" itemscope itemtype="//schema.org/Offer">
        <div class="row">
            <div class="col-xs-5">
                <meta itemprop="price" content="<?=$arResult['MIN_PRICE']['DISCOUNT_VALUE']?>" />
                <meta itemprop="priceCurrency" content="RUB" />

                <? if ($arResult['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                    <div class="price-old"><? if ($dDiscount):?>Розн.: <?endif?><?=$arResult['MIN_PRICE']['PRINT_VALUE'];?><? if ($arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] != $dDiscount):?><span>АКЦИЯ!</span><? endif; ?></div>
                <? endif ?>

                <div class="price-yours"><? if ($dDiscount):?>Опт: <?endif?><?=$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>

                <?/*<?if ($userDiscount < intval($arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'])):?>
                    <div class="price-old"><del><?=$arResult['MIN_PRICE']['PRINT_VALUE'];?></del></div>
                <?endif?>
                <?if ($userDiscount == 0 && intval($arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']) > 0):?>
                    <div class="price"><?=$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                <?elseif ($userDiscount == 0 && intval($arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']) == 0):?>
                    <div class="price"><?=$arResult['MIN_PRICE']['PRINT_VALUE'];?></div>
                <?else:?>
                    <div class="price"><?=number_format(floatval($arResult['MIN_PRICE']['DISCOUNT_VALUE'])/(100-$userDiscount)*100,  2, '.', ' ');?> руб.</div>
                <?endif?>
                <? if ($userDiscount > 0):?>
                    <div class="price-yours" title="Ваша цена"><?=$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?><span>Ваша цена</span></div>
                <?endif?>*/?>
                <? if (!$dDiscount && $arResult['PROPERTIES']['CHECK_AVAIL_R']['VALUE'] == 'Да'): ?>
                            <div class="na">Наличие уточняйте</div>
                <? elseif ($dDiscount && $arResult['PROPERTIES']['CHECK_AVAIL_D']['VALUE'] == 'Да'): ?>
                    <div class="na">Наличие уточняйте</div>
                <? elseif ($arResult["CATALOG_QUANTITY"]>0):?>
                    <div class="av"><strong>Есть в наличии</strong></div>
                <? else: ?>
                    <? if ($arResult['PROPERTIES']['PREORDER']['VALUE'] == 'Y'):?>
                        <div class="na">Под заказ</div>
                    <? else:?>
                        <div class="na">Ожидается</div>
                    <?endif?>
                <?endif;?>
            </div>
            <div class="col-xs-7">
                <div class="c-buttons">
                    <noindex>
					<style>
					.btn-buy {width:180px;background: #179917;font-size: 17px;padding:8px 25px 8px 41px;border-radius:4px;-moz-border-radius:4px 4px 4px 4px;-o-border-radius:4px 4px 4px 4px}
					.btn-buy:hover {background: #118811}

					</style>
                      <?php if ($arResult["CATALOG_QUANTITY"] > 0): ?>
						<a class="btn-main btn-main-add-to-cart" rel="nofollow" href="javascript:void(0);" onclick="Add2Cart(<?=$arResult['ID']?>, '<?=$arResult['DETAIL_PAGE_URL']?>');" style="color:#eee;text-decoration:none"><div onclick="ym(4916878, 'reachGoal', 'click_buy'); return true;" class="btn-buy"> <span class="ico-main ico-main-basket-off"></span> Купить</div></a>

                        <div class="clearfix" style="margin:8px 0"></div>
						<a class="btn-main" rel="nofollow" href="javascript:void(0);" onclick="BuyOnCredit(<?php echo $arResult['ID']; ?>, '<?=$arResult['DETAIL_PAGE_URL']?>', 2);ym(4916878, 'reachGoal', 'click_credit'); return true;" title="Купить в кредит"><img src="/upload/resize_cache/medialibrary/304/140_105_1/calculator.png" alt="Калькулятор" width="17" style="margin-right: 6px;"/>В кредит</a>

						<a class="btn-main btn-main-add-to-compare" rel="nofollow" href="javascript:void(0);" onclick="Add2Compare(<?=$arResult['ID']?>);ym(4916878, 'reachGoal', 'click_compare'); return true;"><span class="ico-main ico-main-compare-off"></span> Сравнить</a>
                      <?php else: ?>
                        <div class="good-buttons">
                            <a href="#analogue-form" class="btn-analogue" data-good-title="<?=$arResult["NAME"]?>" data-good-id="<?=$arResult["ID"]?>" data-good-picture="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" onclick="ym(4916878, 'reachGoal', 'btn_analogue'); return true;">Подобрать аналог</a>

                            <a href="#announce-form" class="lin-announce-good" data-good-title="<?=$arResult["NAME"]?>" data-good-id="<?=$arResult["ID"]?>" data-good-picture="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" onclick=" ym(4916878, 'reachGoal', 'lin-announce-good'); return true;"><i class="fa fa-envelope"></i> <span>Сообщить о поступлении</span></a>
                        </div>

                        <a class="btn-main btn-main-add-to-compare" rel="nofollow" href="javascript:void(0);" onclick="Add2Compare(<?=$arResult['ID']?>);"><span class="ico-main ico-main-compare-off"></span> Сравнить</a>
                      <?php endif; ?>
                    </noindex>
                </div>
            </div>
        </div>
        <div class="row m-0"><div class="col-xs-12 h-line"></div></div>
        <?if ($arParams['PECOM_TEXT']):?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="delivery-container">
                        <span class="ico-main ico-main-car-on"></span>
                        <?=$arParams['~PECOM_TEXT']?>
                    </div>
                </div>
            </div>
            <div class="row m-0"><div class="col-xs-12 h-line"></div></div>
        <?endif?>
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs c-tabs">
                    <li class="active"><a href="#general" data-toggle="tab">Описание</a></li>
                    <li><a href="#manuals" data-toggle="tab">Документация</a></li>
                    <li><a href="#video" data-toggle="tab">Видео</a></li>
                    <li><a href="#options" data-toggle="tab">Опции</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="general" itemprop="description">
                        <p><?=$arResult['DETAIL_TEXT']?></p>
                        <h2>Характеристики</h2>
                        <table class="table table-striped table-striped-c">
                        <? if (!empty($arResult['DISPLAY_PROPERTIES'])):?>
                        <? foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp):?>
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
                    <div class="tab-pane fade" id="manuals">
                        <? if (is_array($arResult['MANUALS_FILES'])):?>
                            <h2>Инструкции</h2>
                            <table class="table table-striped-c manuals">
                                <tr><th>Название</th><th>Размер</th></tr>
                                <?foreach($arResult['MANUALS_FILES'] as $file):?>
                                <tr>
                                <td><a href="<?=$file['SRC']?>" title="<?=$file['FILE_NAME']?>"><span class="glyphicon glyphicon-floppy-save"></span> <?=(!empty($file['DESCRIPTION']) ? $file['DESCRIPTION'] : $file['FILE_NAME'])?></a></td>
                                <td class="size"><?=HelperUser::b2mb($file['FILE_SIZE'])?> Мб</td>
                                </tr>
                                <?endforeach?>
                            </table>
                        <?endif?>
                        <? if (is_array($arResult['CERTS'])):?>
                            <h2>Сертификаты</h2>
                            <table class="table table-striped-c manuals">
                                <tr><th>Название</th><th>Размер</th></tr>
                                <?foreach($arResult['CERTS'] as $file):?>
                                <tr>
                                <td><a href="<?=$file['SRC']?>" title="<?=$file['FILE_NAME']?>"><span class="glyphicon glyphicon-floppy-save"></span> <?=(!empty($file['DESCRIPTION']) ? $file['DESCRIPTION'] : $file['FILE_NAME'])?></a></td>
                                <td class="size"><?=HelperUser::b2mb($file['FILE_SIZE'])?> Мб</td>
                                </tr>
                                <?endforeach?>
                            </table>
                        <?endif?>
                    </div>
                    <div class="tab-pane fade" id="video">
                        <? if (count($arResult['PROPERTIES']['VIDEO']['VALUE'])):?>
                            <? foreach ($arResult['PROPERTIES']['VIDEO']['~VALUE'] as $video):?>
                            <?=$video['TEXT']?>
                            <? endforeach ?>
                        <? endif ?>
                    </div>
                    <div class="tab-pane fade" id="options">
                        <?php
                        $GLOBALS['arroFilter'] = array('ID'=>array(0));

                        foreach ($arResult['PROPERTIES']['OPTIONS']['VALUE'] as $value) {
                            $GLOBALS['arroFilter']['ID'][] = $value;
                        }
                        //$arrFilter = array();

                        $APPLICATION->IncludeComponent(
                            "bitrix:catalog.section",
                            "options",
                            Array(
                                "BY_LINK" => "Y",
                                "AJAX_MODE" => "N",
                                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "ELEMENT_SORT_FIELD" => "sort",
                                "ELEMENT_SORT_ORDER" => "asc",
                                "ELEMENT_SORT_FIELD2" => "id",
                                "ELEMENT_SORT_ORDER2" => "desc",
                                "FILTER_NAME" => "arroFilter",
                                "SECTION_URL" => $arParams["SECTION_URL"],
                                "DETAIL_URL" => $arParams["DETAIL_URL"],
                                "BASKET_URL" => $arParams["BASKET_URL"],
                                "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                                "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                                "SET_TITLE" => $arParams["SET_TITLE"],
                                "PAGE_ELEMENT_COUNT" => "100",
                                "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                                "PRICE_CODE" => $arParams["PRICE_CODE"],
                                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                "CACHE_TIME" => $arParams["CACHE_TIME"],
                                "CACHE_FILTER" => "Y",
                                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                "PAGER_TEMPLATE" => "atis",
                                "DISPLAY_TOP_PAGER" => "N",
                                "DISPLAY_BOTTOM_PAGER" => "N",
                                "PAGER_TITLE" => "Товары",
                                "PAGER_SHOW_ALWAYS" => "N",
                                "PAGER_DESC_NUMBERING" => "N",
                                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                "PAGER_SHOW_ALL" => "Y",
                                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                "AJAX_OPTION_JUMP" => "N",
                                "AJAX_OPTION_STYLE" => "Y",
                                "AJAX_OPTION_HISTORY" => "N"
                            ), $component
                        );?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-0"><div class="col-xs-12 h-line-blue"></div></div>
        <?if ($useBrands):?>
            <?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", ".default", array(
                "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                "ELEMENT_ID" => $arResult['ID'],
                "ELEMENT_CODE" => "",
                "PROP_CODE" => $arParams['BRAND_PROP_CODE'],
                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                "CACHE_TIME" => $arParams['CACHE_TIME'],
                "WIDTH" => "135",
                "HEIGHT" => "135",
                "WIDTH_SMALL" => "135",
                "HEIGHT_SMALL" => "135",
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );?>
        <?endif?>
    </div>
</div>
</div>
<?php

$GLOBALS['arrrFilter'] = array('ID'=>array(0));

foreach ($arResult['PROPERTIES']['RELATED']['VALUE'] as $value) {
    $GLOBALS['arrrFilter']['ID'][] = $value;
}

//$arrFilter = array();

$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
    "list",
	Array(
		"BY_LINK" => "Y",
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrrFilter",
		"SECTION_URL" => $arParams["SECTION_URL"],
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"PAGE_ELEMENT_COUNT" => "100",
		"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"PAGER_TEMPLATE" => "atis",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N"
	), $component
);?>
