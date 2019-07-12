 <?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$prefix = $arParams['PREFIX'];

$arJSONResult = array();

?>
<div class="product-delivery media">
    <div class="media-left product-delivery__pic hidden-xs">
        <div class="product-delivery__icon"></div>
    </div>
    <div class="media-body product-delivery__body">
        <?php if ($arParams['AJAX_CALL'] != 'Y'): ?>
            <div id="<?=$prefix?>delivery_block" class="product-delivery__block">
                <div class="product-delivery__title"><?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY'); ?></div>
                <ul class="product-delivery__list">
                    <li><?=Loc::getMessage('RSDC_TEMPLATE_LOADING'); ?></li>
                </ul>
            </div>
            <?php
                $arAjaxParams = array(
                    'templateName' => $this->GetName(),
                    'siteId' => SITE_ID,
                    'arParams' => $arResult['ORIGINAL_PARAMS'],
                );
                $ajaxPath = $templateFolder.'/ajax.php';
            ?>
            <script>

            BX.addCustomEvent('rs_delivery_update', function(productId, quantity, beforeFn, afterFn) {
              var params = <?=CUtil::PhpToJSObject($arAjaxParams); ?>;
              params.arParams.ELEMENT_ID = productId || params.arParams.ELEMENT_ID;
              params.arParams.QUANTITY = quantity || params.arParams.QUANTITY;
              beforeFn = beforeFn || function() {};
              afterFn = afterFn || function() {};

              beforeFn();


              BX.ajax.post('<?=$ajaxPath?>', params, function(result) {

                var json = BX.parseJSON(result);

                afterFn();

                var deliveryBlock = BX("<?=$prefix?>delivery_block");
                if(deliveryBlock && result) {

                  if (json['SIMPLE']) {

                    deliveryBlock.innerHTML = json['SIMPLE'];

                    deliveryList = BX("<?=$prefix?>delivery_block_list");
                    deliveryList.parentElement.style.height = (deliveryList.offsetHeight + 35) + 'px';
                    deliveryList.style.left = '0px';

                    setTimeout(function() {
                      deliveryList.style.position = 'static';
                    }, 600);
                  }

                  if (json['EXTENDED']) {
                    var deliveryTab = BX("delivery-tab");

                    if (deliveryTab) {
                      deliveryTab.innerHTML = json['EXTENDED'];
                    }
                  }

                }
              });
            });
            BX.onCustomEvent('rs_delivery_update');

            </script>
        <?php elseif ($arParams['AJAX_CALL'] == 'Y'): $APPLICATION->RestartBuffer(); ?>
            <?php ob_start(); ?>
            <div class="product-delivery__title">
                <?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY'); ?>
                <?php
                    if (isset($arResult['LOCATION_TO']['LOCATION_NAME'])) {
                        echo Loc::getMessage('RSDC_TEMPLATE_DELIVERY_IN_CITY').' '.$arResult['LOCATION_TO']['LOCATION_NAME'].': ';
                    }
                ?>
            </div>
            <?php if (count($arResult['DELIVERIES']) > 0): ?>
                <ul class="product-delivery__list" id="<?=$prefix?>delivery_block_list" style="position: absolute; left: -9999999px;">
                <?php foreach ($arResult['DELIVERIES'] as $arDelivery): ?>
                    <?php if ($arDelivery['CALCULATION']['IS_SUCCESS']): ?>
                      <li>
                        <?=$arDelivery['NAME']?> - <?=$arDelivery['CALCULATION']['FORMAT_PRICE'] ?>
                        <?php if ($arDelivery['CALCULATION']['PERIOD']): ?>
                          (<?=$arDelivery['CALCULATION']['PERIOD']?>)
                        <?php endif; ?>
                      </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (isset($arParams['SHOW_DELIVERY_PAYMENT_INFO']) && $arParams['SHOW_DELIVERY_PAYMENT_INFO'] == 'Y'):?>
                    <?php
                    $deliveryData = Loc::getMessage('RSDC_TEMPLATE_DELIVERY_DATA');
                    $deliveryData = str_replace('#DELIVERY_LINK#', $arParams['DELIVERY_LINK'], $deliveryData);
                    $deliveryData = str_replace('#PAYMENT_LINK#', $arParams['PAYMENT_LINK'], $deliveryData);
                    ?>
                    <li><br><?=$deliveryData?></li>
                <?php endif; ?>
                </ul>
            <?php else: ?>
              <?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY_NOT_FOUND'); ?>
            <?php endif; ?>
            <?php $arJSONResult['SIMPLE'] = ob_get_clean(); ?>

            <?php if ($arParams['TAB_DELIVERY'] == 'Y' && count($arResult['DELIVERIES']) > 0): ob_start(); ?>
                <h2 class="product-content__title">
                    <?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY'); ?>
                    <?php
                    if (isset($arResult['LOCATION_TO']['LOCATION_NAME'])) {
                        echo Loc::getMessage('RSDC_TEMPLATE_DELIVERY_IN_CITY').' '.$arResult['LOCATION_TO']['LOCATION_NAME'].': ';
                    }
                    ?>
                </h2>
                <div>
                    <?=Loc::getMessage('RSDC_TEMPLATE_PRODUCT_PRICE') ?>: <b><?=$arResult['PRODUCT']['FULL_PRICE_FORMAT']?></b>
                </div>
                <div>
                    <?=Loc::getMessage('RSDC_TEMPLATE_PRODUCT_WIDTH') ?>: <b><?=$arResult['PRODUCT']['WEIGHT'] / 1000?><?=Loc::getMessage('RSDC_TEMPLATE_PRODUCT_KG'); ?></b>
                </div>
                <div>
                    <?php
                    $dimensions = unserialize($arResult['PRODUCT']['DIMENSIONS']);
                    ?>
                    <?=Loc::getMessage('RSDC_TEMPLATE_PRODUCT_DIMENSIONS'); ?>: <b><?=$dimensions['LENGTH']?> x <?=$dimensions['WIDTH']?> x <?=$dimensions['HEIGHT']?></b>
                </div><br>

                <div class="row p-delivery is-cart">
                    <div class="col col-md-9">
                        <div class="p-delivery__table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2"><?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY_SERVICE')?></th>
                                        <th class="p-delivery__price"><?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY_COST')?></th>
                                        <th class="p-delivery__period"><?=Loc::getMessage('RSDC_TEMPLATE_DELIVERY_TIME')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($arResult['DELIVERIES'] as $arDelivery):?>
                                        <?php if ($arDelivery['CALCULATION']['IS_SUCCESS']): ?>
                                            <tr>
                                                <td class="p-delivery__picture">
                                                  <? //\Bitrix\Main\Diag\Debug::dump($arDelivery); ?>
                                                    <?php if(strlen($arDelivery['PICTURE_PATH']) > 0): ?>
                                                        <img src="<?=$arDelivery['PICTURE_PATH']?>" alt="<?=$arDelivery['NAME']?>" src="<?=$arDelivery['NAME']?>">
                                                    <?php else: ?>
                                                        <img src="<?=$templateFolder.'/images/no-image.png'?>" alt="<?=$arDelivery['NAME']?>" src="<?=$arDelivery['NAME']?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-delivery__name"><?=$arDelivery['NAME']?></td>
                                                <td class="p-delivery__price"><?=$arDelivery['CALCULATION']['FORMAT_PRICE']?></td>
                                                <td class="p-delivery__period"><?=$arDelivery['CALCULATION']['PERIOD']?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php $arJSONResult['EXTENDED'] = ob_get_clean(); endif; ?>

            <?php  echo CUtil::PhpToJSObject($arJSONResult); ?>
        <?php die(); endif; ?>

    </div>
</div>
