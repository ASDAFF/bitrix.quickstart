<div class="b-catalog-section-product">
    <div class="b-catalog-section-product__image"><a href="<?= $arResult['DETAIL_PAGE_URL'] ?>"><img alt="<?= $arResult['NAME'] ?>" title="<?= $arResult['NAME'] ?>" src="<?= $arResult['PREVIEW_PICTURE']['SRC'] ?>"></a></div>
    <div class="b-catalog-section-product__title">
        <a href="<?= $arResult['DETAIL_PAGE_URL'] ?>" title="<?= $arResult['NAME'] ?>"><?= $arResult['NAME'] ?></a>
        <span class="b-catalog-list_item__line"></span>
    </div>
    <div class="b-catalog-section-product__price">
        <? foreach ($arResult["PRICES"] as $code => $arPrice): ?>
            <? if ($arPrice["CAN_ACCESS"]): ?>
                <? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                    <span class="b-price"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span> 
                <? else: ?> 
                    <span class="b-price"><?= $arPrice["PRINT_VALUE"] ?></span>
                <? endif; ?>
            <? endif; ?> 
        <? endforeach; ?>    
    </div>
</div>
