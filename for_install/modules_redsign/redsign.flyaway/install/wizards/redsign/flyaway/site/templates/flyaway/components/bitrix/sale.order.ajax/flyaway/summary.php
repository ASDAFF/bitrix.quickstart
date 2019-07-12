<?php

use \Bitrix\Main\Localization\Loc;
use Bitrix\Sale\DiscountCouponsManager;
?>

<div class="panel panel-default personal-panel">
    <div class="panel-body">
        <div class="row">
            <div class="col col-md-12 clearfix personal-panel__allinfo">
                <div class="personal-panel__allinfo-block clearfix">
                    <div>
                        <div class="personal-panel__allinfo-summary"><?=Loc::getMessage('SALE_PRODUCTS_COUNT');?>: <?=count($arResult['BASKET_ITEMS'])?></div>
                        <div class="personal-panel__allinfo-summary"><?=Loc::getMessage('SALE_SUM')?>: <?=$arResult['ORDER_PRICE_FORMATED']?></div>
                        <div class="personal-panel__allinfo-summary"><?=Loc::getMessage('SOA_TEMPL_SUM_WEIGHT_SUM')?>: <?=$arResult['ORDER_WEIGHT_FORMATED']?></div>
                        <div class="personal-panel__allinfo-summary"><?=Loc::getMessage('SOA_TEMPL_SUM_DELIVERY')?>: <?=$arResult['DELIVERY_PRICE_FORMATED']?></div>
                    </div>
                    <br>
                    <div style="float: right">
                        <div>
                            <i class="small"><?=Loc::getMessage('SOA_TEMPL_SUM_IT');?></i>:
                            <span class="prices prices__val_cool"><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></span>
                        </div>
						<?php if(!empty($arResult['VAT_SUM'])): ?>
						<div>
                            <i class="small"><?=Loc::getMessage('SOA_TEMPL_SUM_VAT'); ?></i>:
                            <?=$arResult['VAT_SUM_FORMATED']?>
                        </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col col-xs-6">
                <a href="<?=$arParams['PATH_TO_BASKET']?>" class="personal-panel__editbasket-link"><?=Loc::getMessage('SOA_TEMPL_EDIT_BASKET'); ?></a>
            </div>
            <div class="col col-xs-6">
                <div class="pull-right">
                    <a href="javascript:void();" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON"  class="btn btn-default btn2">
                        <?=Loc::getMessage('SOA_TEMPL_BUTTON'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
