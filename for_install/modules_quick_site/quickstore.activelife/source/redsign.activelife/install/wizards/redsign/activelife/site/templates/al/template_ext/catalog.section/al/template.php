<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Web\Uri;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();
$sCurrentUri = $request->getRequestUri();

$OFFER_IBLOCK_ID = 0;
if ($arResult['CATALOG']['PRODUCT_IBLOCK_ID'] == '0') {
	$IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
} else {
	$IBLOCK_ID = $arResult['CATALOG']['PRODUCT_IBLOCK_ID'];
	$OFFER_IBLOCK_ID = $arResult['CATALOG']['IBLOCK_ID'];
}
$this->SetViewTarget($arParams['TEMPLATE_AJAXID'].'_tab');
if (
    is_array($arParams['AJAX_FILTER_PROPS'][$IBLOCK_ID]) &&
    count($arParams['AJAX_FILTER_PROPS'][$IBLOCK_ID]) > 0
):
    if ($request->get('rs_ajax') == 'Y') {
        $sAjaxTabId = $request->get('ajax_filter');
    }
?>
    <?/*<li class="nav-tabs__name"><?=Loc::getMessage('RS_SLINE.BCS_AL.TAB_PROPS_SHOW')?>:</li>*/?>
    <li<?php if(!$sAjaxTabId): ?> class="active"<?php endif; ?>>
        <?php
        $uri = new Uri($sCurrentUri);

        $uri->addParams(
            array(
                //'rs_ajax' => 'Y',
                'ajax_id' => $arParams['TEMPLATE_AJAXID'],
                //'ajax_filter' => $arParams['TEMPLATE_AJAXID'].'_all'
            )
        );
        //$uri->deleteParams(array('ajax_filter'));
        ?>
        <a href="<?=$uri->getUri()?>" rel="nofollow"><?=Loc::getMessage('RS_SLINE.BCS_AL.ALL_PRODUCT')?></a>
    </li>
    <?php
    foreach ($arParams['AJAX_FILTER_PROPS'][$IBLOCK_ID] as $sPropCode):
        $uri = new Uri($sCurrentUri);

        $uri->addParams(
            array(
                //'rs_ajax' => 'Y',
                'ajax_id' => $arParams['TEMPLATE_AJAXID'],
            )
        );
/*
        global $rsFavoriteElements;
        if (
            $sPropCode == 'FAVORITE_PRODUCT' && is_array($rsFavoriteElements) && count($rsFavoriteElements) > 0 ||
            in_array($sPropCode, array('VIEWED_PRODUCT', 'BESTSELLER_PRODUCT', 'BIGDATA_PRODUCT'))
        ):
*/
        if (in_array($sPropCode, array('VIEWED_PRODUCT', 'FAVORITE_PRODUCT', 'BESTSELLER_PRODUCT', 'BIGDATA_PRODUCT'))):
            $uri->addParams(
                array(
                    'ajax_filter' => $sPropCode
                )
            );
            ?>
            <li<?php if($sAjaxTabId == $sPropCode): ?> class="active"<?php endif; ?>>
                <a href="<?=$uri->getUri()?>" rel="nofollow"><?=Loc::getMessage('RS_SLINE.BCS_AL.'.$sPropCode)?></a>
            </li>
            <?
        elseif (isset($arResult['ITEMS'][0]['PROPERTIES'][$sPropCode])):

            $uri->addParams(
                array(
                    'ajax_filter' => 'PROPERTY_'.$sPropCode.'_VALUE'
                )
            );
            ?>
            <li<?php if($sAjaxTabId == 'PROPERTY_'.$sPropCode.'_VALUE'): ?> class="active"<?php endif; ?>>
                <a href="<?=$uri->getUri()?>" rel="nofollow"><?=$arResult['ITEMS'][0]['PROPERTIES'][$sPropCode]['NAME']?></a>
            </li>
            <?
        endif;
    endforeach;
endif;
$this->EndViewTarget();
?>

<?php
$this->SetViewTarget('catalog_pager');
    if ($arParams['DISPLAY_TOP_PAGER'] == 'Y') {
        echo $arResult['NAV_STRING'];
    }
$this->EndViewTarget();
?>
<section id="<?=$arParams['TEMPLATE_AJAXID']?>">
    <?php
    if ($arParams['COMPOSITE_FRAME'] == 'Y') {
        $frame = $this->createFrame($arParams['TEMPLATE_AJAXID'], false)->begin();
    } else {
        $this->setFrameMode(true);
    }

    ob_start(); //TEMPLATE_HTML
    ?>
    <?php if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0): ?>

        <?php if ($arParams['SECTION_TITLE']): ?>
            <h2><?=$arParams['SECTION_TITLE']?></h2>
        <?php endif; ?>

        <?php
        $strEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
        $strDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
        $arDeleteParams = array('CONFIRM' => Loc::getMessage('RS_SLINE.BCS_AL.ELEMENT_DELETE_CONFIRM'));
        
        $sCatalogItemsClass = 'catalog_items row';
        
        if ($arParams['USE_SLIDER_MODE'] == 'Y') {
            $sCatalogItemsClass .= ' js-catalog_slider';
        }
        ?>

        <div class="<?=$sCatalogItemsClass?>" id="<?=$arParams['TEMPLATE_AJAXID']?>_items" itemscope itemtype="http://schema.org/ItemList">
        <?php ob_start(); //TEMPLATE_ITEMS ?>

            <?php include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/gallery.php'); ?>
            <?php if (
                $arParams['USE_AJAXPAGES'] == 'Y' &&
                $arParams['DISPLAY_BOTTOM_PAGER'] == 'Y' &&
                intval($arResult['NAV_RESULT']->NavPageNomer) < intval($arResult['NAV_RESULT']->NavPageCount)
            ): ?>

                <?php
                $uri = new Uri($sCurrentUri);

                $uri->addParams(
                    array(
                        //'ajaxpages' => 'Y',
                        'ajax_id' => $arParams['TEMPLATE_AJAXID'],
                        'PAGEN_'.$arResult['NAV_RESULT']->NavNum => $arResult['NAV_RESULT']->NavPageNomer + 1,
                        //'ajax_filter' => $arParams['TEMPLATE_AJAXID'].'_all'
                    )
                );
                $uri->deleteParams(
                    array(
                        'get',
                        'AJAX_CALL',
                    )
                );
                ?>
                <ul class="ajaxpages nav-tabs text-center col-xs-12 js-ajaxpages<?php if ('Y' == $arParams['USE_AUTO_AJAXPAGES']): ?> js-ajaxpages_auto<?php endif; ?>"
                id="<?=$this->getEditAreaId('more')?>">
                   <li>
                        <a href="<?=$uri->getUri()?>">
                            <?php
                            $iNavTo = ($arResult['NAV_RESULT']->NavPageNomer + 1) * $arResult['NAV_RESULT']->NavPageSize;
                            echo Loc::getMessage(
                                'RS_SLINE.BCS_AL.AJAXPAGES_LOAD_MORE',
                                array(
                                    '#FROM#' => ($arResult['NAV_RESULT']->NavPageNomer) * $arResult['NAV_RESULT']->NavPageSize + 1,
                                    '#TO#' => $iNavTo > $arResult['NAV_RESULT']->NavRecordCount ? $arResult['NAV_RESULT']->NavRecordCount : $iNavTo,
                                    '#TOTAL#' => $arResult['NAV_RESULT']->NavRecordCount
                                )
                            );
                            ?>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

        <?php $templateData['TEMPLATE_ITEMS'] = ob_get_flush(); ?>
        </div>

    <?php elseif ($arParams['ERROR_EMPTY_ITEMS'] == 'Y'): ?>
        <div id="<?=$arParams['TEMPLATE_AJAXID']?>_items">
            <?php ShowError(Loc::getMessage('RS_SLINE.BCS_AL.ERROR_EMPTY_ITEMS')); ?>
        </div>
    <?php endif; ?>

    <?php if ($arParams['USE_AJAXPAGES'] != 'Y' && $arParams['DISPLAY_BOTTOM_PAGER']): ?>
        <div class="catalog__pagenav js-catalog_refresh" id="<?=$arParams['TEMPLATE_AJAXID']?>_pager" data-ajax-id="<?=$arParams['TEMPLATE_AJAXID']?>">
            <?=$arResult['NAV_STRING']?>
        </div>
    <?php endif; ?>

    <?php
    $templateData['TEMPLATE_HTML'] = ob_get_flush();

    if ($arParams['TEMPLATE_AJAXID'] && $arParams['COMPOSITE_FRAME'] == 'Y') {
        
        $frame->beginStub(); 
            include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/html/load.html');
        $frame->end();
    }
    ?>
</section>

<script type="text/javascript">
BX.message({
	RS_SLINE_DAYSARTICLE: '<?=getMessageJS('RS_SLINE.BCS_AL.DAYSARTICLE')?>',
	RS_SLINE_QUICKBUY: '<?=getMessageJS('RS_SLINE.BCS_AL.QUICKBUY')?>',
});
</script>
