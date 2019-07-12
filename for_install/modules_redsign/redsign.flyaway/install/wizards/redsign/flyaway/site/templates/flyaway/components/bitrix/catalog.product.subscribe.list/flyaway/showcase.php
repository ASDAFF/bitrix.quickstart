<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>


<script type="text/javascript">
	BX.message({
		CPSL_MESS_BTN_DETAIL: '<?=('' != $arParams['MESS_BTN_DETAIL']
            ? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL')); ?>',

		CPSL_MESS_NOT_AVAILABLE: '<?=('' != $arParams['MESS_BTN_DETAIL']
            ? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL')); ?>',
		CPSL_BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
		CPSL_BASKET_URL: '<?=$arParams['BASKET_URL']; ?>',
		CPSL_TITLE_ERROR: '<?=GetMessageJS('CPSL_CATALOG_TITLE_ERROR') ?>',
		CPSL_TITLE_BASKET_PROPS: '<?=GetMessageJS('CPSL_CATALOG_TITLE_BASKET_PROPS') ?>',
		CPSL_BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CPSL_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		CPSL_BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
		CPSL_BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		CPSL_STATUS_SUCCESS: '<?=GetMessageJS('CPSL_STATUS_SUCCESS'); ?>',
		CPSL_STATUS_ERROR: '<?=GetMessageJS('CPSL_STATUS_ERROR') ?>'
	});
</script>
<?php
if (!empty($_GET['result']) && !empty($_GET['message'])):
    $successNotify = strpos($_GET['result'], 'Ok') ? true : false;
    $postfix = $successNotify ? 'Ok' : 'Fail';
    $popupTitle = Loc::getMessage('CPSL_SUBSCRIBE_POPUP_TITLE_'.strtoupper(str_replace($postfix, '', $_GET['result'])));
    $arJSParams = array(
        'NOTIFY_USER' => true,
        'NOTIFY_POPUP_TITLE' => $popupTitle,
        'NOTIFY_SUCCESS' => $successNotify,
        'NOTIFY_MESSAGE' => urldecode($_GET['message']),
    );
    ?>
    <script type="text/javascript">
      var <?='jaClass_'.$randomString; ?> = new JCCatalogProductSubscribeList(<?=CUtil::PhpToJSObject($arJSParams, false, true); ?>);
    </script>
    <?php
?>
<?php endif; ?>

<?php
if (!empty($arResult['ITEMS'])):
    if ($arParams['IS_AJAX'] == 'Y') {
        $this->SetViewTarget('products');
    }
?>
<?php if ($arParams['IS_AJAX'] != 'Y' || ($arParams['IS_AJAX'] == 'Y' && $arParams['AJAX_ONLY_ELEMENTS'] != 'Y')): ?>
<div class="row products <?=$arResult['TEMPLATE_DEFAULT']['CSS']?>"id="<?=$arParams['AJAX_ID_ELEMENTS']?>">
<?php endif; ?>

    <?php
    foreach ($arResult['ITEMS'] as $key1 => $arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strDelete, $arDeleteParams);
        $strMainID = $this->GetEditAreaId($arItem['ID']);
    ?>

        <div
          class="view-case products__item col js-element  js-elementid<?=$arItem['ID']?> js-compare js-toggle"
          id="<?=$strMainID?>"
          data-elementid="<?=$arItem['ID']?>"
          data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>"
          data-toggle="{'classActive': 'products__item_active', 'onevent': 'mouseover focus active', 'unevent': 'mouseout'}"
        >
                <div class="products__in">
                    <?php
                    // PICTURE
                    $strTitle = (
                        isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
                        : $arItem['NAME']
                    );
                    $strAlt = (
                        isset($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']
                        : $arItem['NAME']
                    );
                    ?>
                    <div class="products__pic">
                        <a class="js-compare-label js-detail_page_url" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <?php if (isset($arItem['FIRST_PIC'][0])): ?>
                                <img class="products__img js-preview" src="<?=$arItem['FIRST_PIC'][0]['RESIZE'][0]['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>">
                            <?php else: ?>
                                <img class="products__img js-preview" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>">
                            <?php endif; ?>
                        </a>
                        <div class="stickers">
                            <div class="da2_icon hidden-xs"><?=Loc::getMessage('DA2_ICON_TITLE')?></div>
                            <div class="qb_icon hidden-xs"><?=Loc::getMessage('QB_ICON_TITLE')?></div>
                            <?php if ($arItem['OUT_PRICE']['DISCOUNT_DIFF'] > 0): ?>
                                <div class="discount_icon hidden-xs"><?='-'.$arItem['OUT_PRICE']['DISCOUNT_DIFF_PERCENT'].'%'?></div>
                            <?php endif; ?>
                        </div>
                        <div class="marks">
                            <?php if ($arItem['PROPERTIES']['ACTION_ITEM']['VALUE'] == 'Y'): ?>
                                <span class="marks__item marks__item_action"><?=Loc::getMessage('RS_ACTION_ITEM');?></span>
                            <?php endif; ?>
                            <?php if ($arItem['PROPERTIES']['BEST_SELLER']['VALUE'] == 'Y'): ?>
                                <span class="marks__item marks__item_hit"><?=Loc::getMessage('RS_BESTSELLER_ITEM');?></span>
                            <?php endif; ?>
                            <?php if ($arItem['PROPERTIES']['NEW_ITEM']['VALUE'] == 'Y'): ?>
                                <span class="marks__item marks__item_new"><?=Loc::getMessage('RS_NEW_ITEM');?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="products__data">
                        <div class="products__name">
                            <a class="products-title js-compare-name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a><br>
                        </div>
                        <div class="products__subscribe">
                            <a class="btn btn2" href="#" onclick="productUnsuscribe(event, <?=$arItem['ID']?>, <?=CUtil::PhpToJSObject($arParams['LIST_SUBSCRIPTIONS'], false, true);?>)"><?=Loc::getMessage('CPSL_TPL_MESS_BTN_UNSUBSCRIBE');?></a>
                        </div>
                    </div>


                </div>
        </div>
    <?php endforeach; ?>

<?php if ($arParams['IS_AJAX'] != 'Y' || ($arParams['IS_AJAX'] == 'Y' && $arParams['AJAX_ONLY_ELEMENTS'] != 'Y')): ?>
</div>
<?php endif; ?>

<?php
if ($arParams['IS_AJAX'] == 'Y') {
    $this->EndViewTarget();
    $cssId = ($arParams['AJAX_ONLY_ELEMENTS'] == 'Y' ? $arParams['AJAX_ID_ELEMENTS'] : $arParams['AJAX_ID_SECTION']);
    $templateData[$cssId] = $APPLICATION->GetViewContent('products');
}

$this->SetViewTarget('ajaxpages');
?>
<div class="row">
  <?php if (intval($arResult['NAV_RESULT']->NavPageNomer) < intval($arResult['NAV_RESULT']->NavPageCount)): ?>
    <div class="col col-xs-10 visible-xs">
        <div class="ajaxpages" <?=($arParams['USE_AUTO_AJAXPAGES'] == 'Y' ? 'auto' : '')?>>
          <a class="btn btn-default btn-button btn-button_wide" rel="nofollow" href="#" <?php
              ?>data-ajaxurl="<?=$arResult['AJAXPAGE_URL']?>" <?php
              ?>data-ajaxpagesid="<?=$arParams['AJAX_ID_ELEMENTS']?>" <?php
              ?>data-navpagenomer="<?=($arResult['NAV_RESULT']->NavPageNomer)?>" <?php
              ?>data-navpagecount="<?=($arResult['NAV_RESULT']->NavPageCount)?>" <?php
              ?>data-navnum="<?=($arResult['NAV_RESULT']->NavNum)?>"<?php
              ?>data-loading-text=<?=Loc::getMessage('AJAXPAGES_LOADING'); ?><?php
          ?>><span><?=Loc::getMessage('AJAXPAGES_LOAD_MORE')?></span></a>
        </div>
    </div>
  <?php endif; ?>
    <div class="col col-xs-2 visible-xs">
        <div class="loss-menu-right loss-menu-right_top views js-top"><a class="selected" href="#"><i class="fa fa-arrow-up"></i></a></div>
    </div>
</div>
<?php
$this->EndViewTarget();
$templateData['ajaxpages'] = $APPLICATION->GetViewContent('ajaxpages');
if ($arParams['DISPLAY_BOTTOM_PAGER'] == 'Y') {
    $this->SetViewTarget('paginator');
    echo $arResult['NAV_STRING'];
    $this->EndViewTarget();
    $templateData['paginator'] = $APPLICATION->GetViewContent('paginator');
}
?>

<?php endif; ?>
