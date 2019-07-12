<?

use \Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
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
/** @var CBitrixComponent $component */

$isAjax = (
    isset($_REQUEST['ajax_id']) && $_REQUEST['ajax_id'] == $arParams['TEMPLATE_AJAXID'] &&
    isset($_REQUEST["ajax_action"]) && $_REQUEST["ajax_action"] == "Y"
);
$sJsObjName = $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $arParams['TEMPLATE_AJAXID'])));

if ($isAjax):
	$APPLICATION->restartBuffer();
else:
?>
    <div class="cmp_page" id="<?=$arParams['TEMPLATE_AJAXID']?>">
    <?php
	$frame = $this->createFrame($arParams['TEMPLATE_AJAXID'], false)->begin('');
endif;

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS'])):
	$iTableCol = count($arResult['ITEMS']);
	$arFieldsHide = array('NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');
    
    $iMinColumsCount = 4;
	/*if (!empty($arResult['ALL_FIELDS']) || !empty($arResult['ALL_PROPERTIES']) || !empty($arResult['ALL_OFFER_FIELDS']) || !empty($arResult['ALL_OFFER_PROPERTIES']))
	{
		?><p class="head4"><?=getMessage('CATALOG_COMPARE_PARAMS')?></p><?
		?><p class="rs_tiles grid-wrap clearfix"><?
		if (!empty($arResult['ALL_FIELDS']))
		{
			foreach ($arResult['ALL_FIELDS'] as $sPropCode => $arProp)
			{
				if (in_array($sPropCode, $arFieldsHide))
				{
					continue;
				}
				else if (!isset($arResult['FIELDS_REQUIRED'][$sPropCode]))
				{
					?><label class="rs_tiles-item grid-cell rs_checkbox"><?
						?><input type="checkbox"<?
							?> onchange="<?=$sJsObjName?>.MakeAjaxAction('<?=CUtil::JSEscape($arProp['ACTION_LINK'])?>')"<?
							if ($arProp['IS_DELETED'] == 'N') echo ' checked="checked"';
						?> /><?
						?><span class="rs_icon-checkbox"></span>&nbsp;<?
						echo getMessage('IBLOCK_FIELD_'.$sPropCode);
					?></label><?
				}
			}
		}
		if (!empty($arResult['ALL_OFFER_FIELDS']))
		{
			foreach($arResult['ALL_OFFER_FIELDS'] as $sPropCode => $arProp)
			{
				if (in_array($sPropCode, $arFieldsHide))
				{
					continue;
				}
				?><label class="rs_tiles-item grid-cell rs_checkbox"><?
					?><input type="checkbox"<?
						?> onchange="<?=$sJsObjName?>.MakeAjaxAction('<?=CUtil::JSEscape($arProp['ACTION_LINK'])?>')"<?
						if ($arProp['IS_DELETED'] == 'N') echo ' checked="checked"';
					?> /><?
					?><span class="rs_icon-checkbox"></span>&nbsp;<?
					echo getMessage('IBLOCK_OFFER_FIELD_'.$sPropCode);
				?></label><?
			}
		}
		if (!empty($arResult['ALL_PROPERTIES']))
		{
			foreach($arResult['ALL_PROPERTIES'] as $sPropCode => $arProp)
			{
				?><label class="rs_tiles-item grid-cell rs_checkbox"><?
					?><input type="checkbox"<?
						?> onchange="<?=$sJsObjName?>.MakeAjaxAction('<?=CUtil::JSEscape($arProp['ACTION_LINK'])?>')"<?
						if ($arProp['IS_DELETED'] == 'N') echo ' checked="checked"';
					?> /><?
					?><span class="rs_icon-checkbox"></span>&nbsp;<?
					echo $arProp['NAME'];
				?></label><?
			}
		}
		if (!empty($arResult['ALL_OFFER_PROPERTIES']))
		{
			foreach($arResult['ALL_OFFER_PROPERTIES'] as $sPropCode => $arProp)
			{
			?><label class="rs_tiles-item grid-cell rs_checkbox"><?
				?><input type="checkbox"<?
					?> onchange="<?=$sJsObjName?>.MakeAjaxAction('<?=CUtil::JSEscape($arProp['ACTION_LINK'])?>')"<?
					if ($arProp['IS_DELETED'] == 'N') echo ' checked="checked"';
				?> /><?
				?><span class="rs_icon-checkbox"></span>&nbsp;<?
				echo $arProp['NAME'];
			?></label><?
			}
		}
		?></p><?
	}*/
	$sTableHTML = '';
	?>

    <ul class="nav-tabs text-center" role="tablist">
        <li<?php if (!$arResult['DIFFERENT']):?> class="active"<?php endif; ?>><a class="" href="<?=$arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N'?>" rel="nofollow"><?=Loc::getMessage('CATALOG_ALL_CHARACTERISTICS')?></a></li>
        <li<?php if ($arResult['DIFFERENT']):?> class="active"<?php endif; ?>><a href="<?=$arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y'?>" rel="nofollow"><?=Loc::getMessage('CATALOG_ONLY_DIFFERENT')?></a></li>
    </ul>

    <div class="row">
        <div class="cmp_page__names col-md-3 col-lg-2d4">
            <table class="rs_table">
                <thead>
                <tr>
                    <td></td>
                    
                    <?php ob_start(); ?>
                    <thead>
                    <tr>
                    <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                        <?php
                        $bHaveOffer = $arItem['ID'] != $arItem['PARENT_ID'];
                      
                        //$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strEdit);
                        //$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strDelete, $arDeleteParams);
                        $bHaveOffer = false;
                        if ($arItem['ID'] != $arItem['PARENT_ID']) {
                            $arItemShow = $arItem;
                        } else  {
                            $bHaveOffer = true;
                            $arItemShow = $arItem;//$arItem['OFFERS'][$arItem['OFFERS_SELECTED']];
                        }
                        ?>
                        <td class="catalog_item product js-product
                            <?php if (isset($arItem['DAYSARTICLE2'])): ?>da2<? endif; ?>
                            <?php if (isset($arItem['QUICKBUY'])): ?>qb<? endif; ?>
                            " data-product-id="<?=$arItem['ID']?>"
                            <?php if ($bHaveOffer): ?>data-offer-id="<?=$arItemShow['ID']?>"<?php endif; ?>
                        itemprop="itemListElement" itemscope itemtype="http://schema.org/Product">
                            <div class="catalog_item__inner">
                                <a class="catalog_item__close badge" onclick="<?=$sJsObjName?>.MakeAjaxAction('<?=CUtil::JSEscape($arItem['~DELETE_URL'])?>');" href="javascript:void(0)" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>">
                                    <svg class=" icon-close icon-svg"><use xlink:href="#svg-close"></use></svg>
                                </a>
                                <a class="catalog_item__pic bx_rcm_view_link" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                    <?php if (isset($arItem['FIRST_PIC'][0])): ?>
                                        <img class="catalog_item__img" src="<?=$arItem['FIRST_PIC'][0]['RESIZE'][0]['src']?>" alt="<?=$arItem['FIRST_PIC'][0]['ALT']?>" title="<?=$arItem['FIRST_PIC'][0]['TITLE']?>">
                                    <?php else: ?>
                                        <img class="catalog_item__img" src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>">
                                    <?php endif; ?>
                                    
                                    <?php if ($arParams['POPUP_DETAIL_VARIABLE'] == 'ON_LUPA'): ?>
                                        <i class="catalog_item__zoom icon multimage_icons js_popup_detail fancybox.ajax" data-fancybox-href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=Loc::getMessage('RS_SLINE.BCS_AL.CLICK_FOR_DETAIL_POPUP')?>" rel="nofollow"></i>
                                    <?php endif; ?>

                                    <?php if (
                                        isset($arItem['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arItem['IBLOCK_ID']]]) && $arItem['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] != '' ||
                                        isset($arItem['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arItem['IBLOCK_ID']]]) && $arItem['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] != ''
                                    ): ?>
                                        <span class="catalog_item__gender">
                                        <?php if ($arItem['PROPERTIES'][$arParams['ICON_MEN_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] != ''): ?>
                                            <svg class="icon-men icon-svg"><use xlink:href="#svg-men"></use></svg>
                                        <?php endif; ?>
                                        <?php if ($arItem['PROPERTIES'][$arParams['ICON_WOMEN_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] != ''): ?>
                                            <svg class="icon-women icon-svg"><use xlink:href="#svg-women"></use></svg>
                                        <?php endif; ?>
                                        </span>
                                    <?php endif; ?>

                                    <span class="catalog_item__stickers js_swap_hide"
                                        <?php if (isset($arItem['DAYSARTICLE2']) || isset($arItem['QUICKBUY'])): ?>
                                            style="display:none;"
                                        <?php endif; ?>
                                    >
                                        <?php
                                        if (
                                            $arParams['ICON_NOVELTY_PROP'][$arItem['IBLOCK_ID']] &&
                                            $arItem['PROPERTIES'][$arParams['ICON_NOVELTY_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] == 'Y' ||
                                            $arParams['NOVELTY_TIME'] && $arParams['NOVELTY_TIME'] >= (floor($_SERVER['REQUEST_TIME'] - MakeTimeStamp($arItem['DATE_ACTIVE_FROM']))/3600)
                                        ):
                                        ?>
                                            <span class="sticker new">
                                                <span class="sticker__text">
                                                    <?=$arItem['PROPERTIES'][$arParams['ICON_NOVELTY_PROP'][$arItem['IBLOCK_ID']]]['NAME']?>
                                                </span>
                                            </span>
                                        <?php endif; ?>

                                        <?php
                                        if (
                                            $arParams['ICON_DISCOUNT_PROP'][$arItem['IBLOCK_ID']] &&
                                            $arItem['PROPERTIES'][$arParams['ICON_DISCOUNT_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] == 'Y' ||
                                            $arItemShow['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']
                                        ):
                                        ?>
                                            <span class="sticker discount">
                                                <span class="sticker__text">
                                                    <?=$arItem['PROPERTIES'][$arParams['ICON_DISCOUNT_PROP'][$arItem['IBLOCK_ID']]]['NAME']?>
                                                </span>
                                            </span>
                                        <?php endif; ?>

                                        <?php
                                        if (
                                            $arParams['ICON_DEALS_PROP'][$arItem['IBLOCK_ID']] &&
                                            $arItem['PROPERTIES'][$arParams['ICON_DEALS_PROP'][$arItem['IBLOCK_ID']]]['VALUE'] == 'Y'
                                        ):
                                        ?>
                                            <span class="sticker action">
                                                <span class="sticker__text">
                                                    <?=$arItem['PROPERTIES'][$arParams['ICON_DEALS_PROP'][$arItem['IBLOCK_ID']]]['NAME']?>
                                                </span>
                                            </span>
                                        <?php endif; ?>
                                    </span>

                                    <?php
                                    // TIMERS
                                    $arTimers = array();
                                    if (isset($arItem['DAYSARTICLE2'])) {
                                        $arTimers[] = $arItem['DAYSARTICLE2'];
                                    }
                                    if (isset($arItem['QUICKBUY'])) {
                                        $arTimers[] = $arItem['QUICKBUY'];
                                    }

                                    if (is_array($arItem['OFFERS'])) {
                                        foreach ($arItem['OFFERS'] as $arOffer) {
                                            if (isset($arOffer['DAYSARTICLE2'])) {
                                                $arTimers[] = $arOffer['DAYSARTICLE2'];
                                            }
                                            if (isset($arOffer['QUICKBUY'])) {
                                                $arTimers[] = $arOffer['QUICKBUY'];
                                            }
                                        }
                                    }
                                    $have_vis = false;
                                    ?>

                                    <?php if (is_array($arTimers) && 0 < count($arTimers)): ?>

                                        <?php foreach ($arTimers as $arTimer): ?>
                                            <?php
                                            $KY = 'TIMER';
                                            if (isset($arTimer['DINAMICA_EX'])) {
                                                $KY = 'DINAMICA_EX';
                                            }
                                            $jsTimer = array(
                                                'DATE_FROM' => $arTimer[$KY]['DATE_FROM'],
                                                'DATE_TO' => $arTimer[$KY]['DATE_TO'],
                                                'AUTO_RENEWAL' => $arTimer['AUTO_RENEWAL'],
                                            );
                                            if (isset($arTimer['DINAMICA'])) {
                                                $jsTimer['DINAMICA_DATA'] = $arTimer['DINAMICA'] == 'custom' ? array_flip(unserialize($arTimer['DINAMICA_DATA'])) : $arTimer['DINAMICA'];
                                            }
                                            ?>
                                            <span class="catalog_item-timer js_timer timer_bg" style="display:
                                                <?=((($arItem['ID'] == $arTimer['ELEMENT_ID'] || $arItemShow['ID'] == $arTimer['ELEMENT_ID']) && !$have_vis) ? 'block' : 'none')?>
                                            " data-offer-id="<?=$arTimer['ELEMENT_ID']?>" data-timer='<?=json_encode($jsTimer)?>'>
                                                <span class="catalog_item-timer-val">
                                                    <span class="value js_timer-d">00</span>
                                                    <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.QB_DAY')?></span>
                                                </span>
                                                <span class="catalog_item-timer-val">
                                                    <span class="value js_timer-H">00</span>
                                                    <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.QB_HOUR')?></span>
                                                </span>
                                                <span class="catalog_item-timer-val">
                                                    <span class="value js_timer-i">00</span>
                                                    <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.QB_MIN')?></span>
                                                </span>
                                                <span class="catalog_item-timer-val">
                                                    <span class="value js_timer-s">00</span>
                                                    <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.QB_SEC')?></span>
                                                </span>
                                                <span class="catalog_item-timer-separator"></span>
                                                <span class="catalog_item-timer-val">
                                                    <?php if (isset($arTimer['TIMER'])): ?>
                                                        <span class="value"><?=($arTimer['QUANTITY'] > 99 ? $arTimer['QUANTITY'] : sprintf('%02d', $arTimer['QUANTITY']))?></span>
                                                        <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.QB_QUANTITY')?></span>
                                                    <?php elseif (isset($arTimer['DINAMICA_EX'])): ?>
                                                        <span class="value js_timer-progress">0%</span>
                                                        <span class="podpis"><?=Loc::getMessage('RS_SLINE.BCS_AL.DA_PRODANO')?></span>
                                                    <?php endif; ?>
                                                </span>
                                            </span>
                                        <?php endforeach?>
                                    <?php endif; ?>
                                </a>

                                <div class="catalog_item__head clearfix">
                                    <a class="catalog_item__name text_fade js-product__name" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                        <?php
                                        echo (isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != '')
                                            ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
                                            : $arItem["NAME"];
                                        ?>
                                    </a>
                                    <?php
                                    $sBrandPropCode = $arParams['BRAND_PROP'][$arItem['IBLOCK_ID']];
                                    if (isset($arItem['PROPERTIES'][$sBrandPropCode]['VALUE'])): ?>
                                        <div class="catalog_item__brand b-text_fade">
                                        <?php
                                        if (is_array($arItem['PROPERTIES'][$sBrandPropCode]['VALUE'])):
                                            echo implode(' / ', array_map(
                                                function($sName, $sLink) {
                                                    return '<a href="' . $sLink . '">' . $sName . '</a>';
                                                },
                                                $arItem['PROPERTIES'][$sBrandPropCode]['VALUE'],
                                                $arItem['PROPERTIES'][$sBrandPropCode]['FILTER_URL']
                                            ));
                                        else: ?>
                                            <a href="<?=$arItem['PROPERTIES'][$sBrandPropCode]['FILTER_URL']?>">
                                                <?php
                                                if (isset($arItem['DISPLAY_PROPERTIES'][$sBrandPropCode]['DISPLAY_VALUE'])) {
                                                    echo $arItem['DISPLAY_PROPERTIES'][$sBrandPropCode]['DISPLAY_VALUE'];
                                                } else {
                                                    echo $arItem['PROPERTIES'][$sBrandPropCode]['VALUE'];
                                                }
                                                ?>
                                            </a>
                                        <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="catalog_item__price price clearfix" itemprop="offers" itemscope itemtype="http://schema.org/<?=($bHaveOffer ? 'AggregateOffer' : 'Offer')?>">
                                    <?php if ($arParams['USE_PRICE_COUNT']):?>
                                        <?php foreach ($arItemShow['PRICE_MATRIX']['COLS'] as $typeID => $arType):
                                            $arPrice = $arItemShow['PRICE_MATRIX']['MATRIX'][$typeID][0];
                                        ?>
                                            <div class="price__pdv<?php if ($arPrice['DISCOUNT_DIFF']): ?> disc<?php endif; ?> js-price_pdv-<?=$arType['ID'];?>" itemprop="<?=($bHaveOffer ? 'lowPrice' : 'price')?>">
                                                <?=$arPrice['PRINT_DISCOUNT_VALUE']?>
                                            </div>
                                            <?php if ($arParams['SHOW_OLD_PRICE'] == 'Y'): ?>
                                                <div class="price__pv js-price_pv-<?=$arType['ID'];?>">
                                                    <?php
                                                    if ($arPrice['DISCOUNT_DIFF']) {
                                                        echo $arPrice['PRINT_VALUE'];
                                                    }
                                                    ?>
                                                </div>

                                                <div class="price__pdd js-price_pdd-<?=$arType['ID'];?>"<?php if ($arPrice['DISCOUNT_DIFF'] <= 0): ?> style="visibility:hidden"<?php endif; ?>>
                                                    - <?=$arPrice['PRINT_DISCOUNT_DIFF']?>
                                                </div>
                                            <?php endif; ?>
                                            <?/*<meta itemprop="<?=($bHaveOffer ? 'lowPrice' : 'price')?>" content="<?=$arPrice['DISCOUNT_PRICE']?>">*/?>
                                            <meta itemprop="priceCurrency" content="<?=$arPrice['CURRENCY']?>">
                                        <?php
                                            break;
                                        endforeach;
                                        ?>

                                    <?php else: ?>

                                        <?php foreach ($arResult['PRICES'] as $sPriceCode => $arResPrice):
                                            if (
                                                !$arResult['PRICES'][$sPriceCode]['CAN_VIEW'] ||
                                                 $arItemShow['PRICES'][$sPriceCode]['MIN_PRICE'] != 'Y'
                                            ) {
                                                continue;
                                            }
                                            $arPrice = $arItemShow['PRICES'][$sPriceCode];
                                        ?>
                                            <div class="price__pdv<?php if ($arPrice['DISCOUNT_DIFF']): ?> disc<?php endif; ?> js-price_pdv-<?=$arPrice['PRICE_ID']?>">
                                                <?=$arPrice['PRINT_DISCOUNT_VALUE']?>
                                            </div>
                                            <?php if ($arParams['SHOW_OLD_PRICE'] == 'Y'): ?>
                                                <div class="price__pv js-price_pv-<?=$arPrice['PRICE_ID']?>">
                                                    <?php
                                                    if ($arPrice['DISCOUNT_DIFF']) {
                                                        echo $arPrice['PRINT_VALUE'];
                                                    }
                                                    ?>
                                                </div>

                                                <div class="price__pdd js-price_pdd-<?=$arPrice['PRICE_ID']?>"<?php if ($arPrice['DISCOUNT_DIFF'] <= 0): ?> style="display:none"<?php endif; ?>>
                                                    - <?=$arPrice['PRINT_DISCOUNT_DIFF']?>
                                                </div>
                                            <?php endif; ?>
                                            <meta itemprop="lowPrice" content="<?=$arPrice['DISCOUNT_VALUE']?>">
                                            <meta itemprop="priceCurrency" content="<?=$arPrice['CURRENCY']?>">
                                        <?php
                                            break;
                                        endforeach;
                                        ?>
                                    <?php endif; ?>  
                                </div>
                                <?php
                                /*if ($arItem['CAN_BUY'])
                                {
                                    ?><!--noindex--><?
                                        ?><a class="rs_form-btn btn2" href="<?=$arItem['BUY_URL']?>" rel="nofollow"><?
                                            ?><svg class="rs_svg rs_icon-cart"><use xlink:href="#rs_icon-cart"></use></svg><?
                                            echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? $arParams['MESS_BTN_ADD_TO_BASKET'] : getMessage('CATALOG_COMPARE_BUY'));
                                        ?></a><?
                                    ?><!--/noindex--><?
                                }
                                elseif (!empty($arResult['PRICES']) || is_array($arItem['PRICE_MATRIX']))
                                {
                                    ?><br /><?=getMessage('CATALOG_NOT_AVAILABLE');
                                }*/
                                ?>
                            </div>
                        </td>
                    <?php endforeach; ?>
                    <?php
                    if ($iMinColumsCount > $iTableCol) {
                        echo str_repeat('<td></td>', $iMinColumsCount - $iTableCol);
                    }
                    ?>
                    </tr>
                    </thead>
                    <?php $sTableHTML .= ob_get_clean() ?>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($arResult['SHOW_FIELDS'])) {
                    foreach ($arResult['SHOW_FIELDS'] as $sPropCode => $arProp) {
                        if (in_array($sPropCode, $arFieldsHide)) {
                            continue;
                        }
                        $showRow = true;
                        if (!isset($arResult['FIELDS_REQUIRED'][$sPropCode]) || $arResult['DIFFERENT']) {
                            $arCompare = array();
                            foreach($arResult['ITEMS'] as &$arItem) {
                                $arPropertyValue = $arItem['FIELDS'][$sPropCode];
                                if (is_array($arPropertyValue)) {
                                    sort($arPropertyValue);
                                    $arPropertyValue = implode(' / ', $arPropertyValue);
                                }
                                $arCompare[] = $arPropertyValue;
                            }
                            unset($arItem);
                            $showRow = (count(array_unique($arCompare)) > 1);
                        }
                        if ($showRow) {
                            ?><tr><?
                                ?><td><?=getMessage('IBLOCK_FIELD_'.$sPropCode)?></td><?
                                ob_start();
                                    ?><tr><?
                                    foreach ($arResult['ITEMS'] as &$arItem) {
                                        ?><td><?echo $arItem['FIELDS'][$sPropCode]?></td><?
                                    }
                                    if ($iMinColumsCount > $iTableCol) {
                                        echo str_repeat('<td></td>', $iMinColumsCount - $iTableCol);
                                    }
                                    ?></tr><?
                                $sTableHTML .= ob_get_clean();
                                unset($arItem);
                            ?></tr><?
                        }
                    }
                }

                if (!empty($arResult['SHOW_OFFER_FIELDS'])) {
                    foreach ($arResult['SHOW_OFFER_FIELDS'] as $sPropCode => $arProp) {
                        if (in_array($sPropCode, $arFieldsHide)) {
                            continue;
                        }
                        $showRow = true;
                        if ($arResult['DIFFERENT']) {
                            $arCompare = array();
                            foreach ($arResult['ITEMS'] as &$arItem){
                                $Value = $arItem['OFFER_FIELDS'][$sPropCode];
                                if (is_array($Value)) {
                                    sort($Value);
                                    $Value = implode(' / ', $Value);
                                }
                                $arCompare[] = $Value;
                            }
                            unset($arItem);
                            $showRow = (count(array_unique($arCompare)) > 1);
                        }
                        if ($showRow) {
                            ?><tr><?
                                ?><td><?=getMessage('IBLOCK_OFFER_FIELD_'.$sPropCode)?></td><?
                                ob_start();
                                    ?><tr><?
                                    foreach ($arResult['ITEMS'] as &$arItem) {
                                        ?><td><?
                                            echo (is_array($arItem['OFFER_FIELDS'][$sPropCode])? implode('/ ', $arItem['OFFER_FIELDS'][$sPropCode]): $arItem['OFFER_FIELDS'][$sPropCode]);
                                        ?></td><?
                                    }
                                    if ($iMinColumsCount > $iTableCol) {
                                        echo str_repeat('<td></td>', $iMinColumsCount - $iTableCol);
                                    }
                                    ?></tr><?
                                $sTableHTML .= ob_get_clean();
                                unset($arItem);
                            ?></tr><?
                        }
                    }
                }
                
                if (!empty($arResult['PROPERTIES_GROUPS']) && (!empty($arResult['SHOW_PROPERTIES']) || !empty($arResult['SHOW_OFFER_PROPERTIES']))) {
                    foreach ($arResult['PROPERTIES_GROUPS'] as $arGroup) {
                        if ($arGroup['IS_SHOW']) {
                            ?><tr><th class="cmp_page__group"><?= isset($arGroup['NAME']) ? $arGroup['NAME'] : getMessage('RS_SLINE.BCCR_AL.NOT_GRUPED_PROPS')?></th></tr><?
                            $sTableHTML .= '<tr><th class="cmp_page__group" colspan="'.($iMinColumsCount > $iTableCol ? $iMinColumsCount : $iTableCol).'"></th></tr>';
                            if (!empty($arGroup['BINDS'])) {
                                foreach ($arGroup['BINDS'] as $sPropCode) {
                                    if (isset($arResult['SHOW_PROPERTIES'][$sPropCode]) && $arResult['SHOW_PROPERTIES'][$sPropCode]['IS_SHOW']) {
                                        ?><tr><?
                                            ?><td><?=$arResult['SHOW_PROPERTIES'][$sPropCode]['NAME']?></td><?
                                            ob_start();
                                                ?><tr><?
                                                foreach($arResult['ITEMS'] as &$arItem) {
                                                    ?><td><?
                                                        echo (is_array($arItem['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE'])? implode('/ ', $arItem['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']): $arItem['DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']);
                                                    ?></td><?
                                                }
                                                if ($iMinColumsCount > $iTableCol) {
                                                    echo str_repeat('<td></td>', $iMinColumsCount - $iTableCol);
                                                }
                                                ?></tr><?
                                            $sTableHTML .= ob_get_clean();
                                            unset($arItem);
                                        ?></tr><?
                                    }
                                    
                                    if (isset($arResult['SHOW_OFFER_PROPERTIES'][$sPropCode]) && $arResult['SHOW_OFFER_PROPERTIES'][$sPropCode]['IS_SHOW']) {
                                        ?><tr><?
                                            ?><td><?=$arResult['SHOW_OFFER_PROPERTIES'][$sPropCode]['NAME']?></td><?
                                            ob_start();
                                                ?><tr><?
                                                foreach ($arResult['ITEMS'] as &$arItem) {
                                                    ?><td><?
                                                        echo (is_array($arItem['OFFER_DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE'])? implode('/ ', $arItem['OFFER_DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']): $arItem['OFFER_DISPLAY_PROPERTIES'][$sPropCode]['DISPLAY_VALUE']);
                                                    ?></td><?
                                                }
                                                if ($iMinColumsCount > $iTableCol) {
                                                    echo str_repeat('<td></td>', $iMinColumsCount - $iTableCol);
                                                }
                                                ?></tr><?
                                            $sTableHTML .= ob_get_clean();
                                            unset($arItem);
                                        ?></tr><?
                                    }
                                }
                            }
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="cmp_page__items col-md-9 col-lg-9d6">
            <table class="cmp_page__table rs_form rs_table"><?=$sTableHTML?></table>
        </div>
    </div>
    <?php
else:
	echo getMessage('RS_SLINE.BCCR_AL.LIST_EMPTY');
endif;

if ($isAjax):
	die();
else:
	$frame->end();
		?>
        <script>var <?=$sJsObjName?> = new BX.Iblock.Catalog.CompareClass('<?=$arParams['TEMPLATE_AJAXID']?>');</script>
	</div>
    <?php
endif;

