<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

if(is_array($arResult["ITEMS"]) && count($arResult["ITEMS"])>0): ?>
<div class="row shop-panel">
    <div class="col col-md-3 js-search_city">
        <div class="form-group">
            <input type="text" class="form-control back1" placeholder="<?=Loc::getMessage('SHOP_SEARCH_PLACEHOLDER');?>">
        </div>
    </div>
    <div class="col col-md-9 js-shops">
        <div class="shop-panel__filters js-filter">
            <?php foreach($arResult['FILTER_TYPES'] as $arFilterType): ?>
                <div class="btn btn3 js-btn" data-filter="<?=htmlspecialcharsbx($arFilterType['XML_ID'])?>"><?=$arFilterType['VALUE']?></div>
            <?php endforeach; ?>
            <div class="btn btn3 active js-btn"  data-filter=""><?=Loc::getMessage('SHOP_FILTER_ALL');?></div>
        </div>
    </div>
</div>
<div class="row shops">
    <div class="col col-md-3">
        <div class="shops__list js-shops_list">
            <?php foreach($arResult["ITEMS"] as $arItem): ?>
                <div
                class="shop-item js-item"
                data-coords="<?=$arItem['COORDINATES']?>"
                data-id="<?=$arItem['ID']?>"
                data-type="<?=$arItem['TYPE']?>"
                >
                <div class="shop-item__name js-item__name"><?=$arItem['NAME']?></div>
                    <div class="shop-item__descr js-item__descr"><?=$arItem['PREVIEW_TEXT']?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col col-md-9">
        <div class="map"><div id="rsYMapShops" style="width:100%;height:350px;"></div></div>
    </div>
</div>
<?php endif; ?>
