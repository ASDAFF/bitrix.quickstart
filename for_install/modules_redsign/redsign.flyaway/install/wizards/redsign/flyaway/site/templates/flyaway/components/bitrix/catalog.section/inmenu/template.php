<?php
if(empty($arResult['ITEMS'])) {
    return;
}

use \Bitrix\Main\Localization\Loc;

foreach($arResult['ITEMS'] as $arItem):
$arItemShow = !empty($arItem['OFFERS']) && count($arItem['OFFERS']) > 0 ? $arItem['OFFERS'][0] : $arItem;
?>
<div class="mainmenu__product js-mainmenu__column">
    <div class="mainmenu-product">
        <div class="mainmenu-product__pic">
            <?php if(!empty($arItem['DETAIL_PAGE_URL'])): ?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?php endif; ?>

            <?php if(!empty($arItem['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']) > 0): ?>
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
            <?php elseif(!empty($arItem['DETAIL_PICTURE']) && strlen($arItem['DETAIL_PICTURE']['SRC']) > 0): ?>
                <img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>">
            <?php else: ?>
                <img src="<?=$arResult['NO_PHOTO']['src']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>">
            <?php endif; ?>

            <?php if(!empty($arItem['DETAIL_PAGE_URL'])): ?></a><?php endif; ?>
        </div>
        <div class="mainmenu-product__data">
            <div class="mainmenu-product__name">
                <?php if(!empty($arItem['DETAIL_PAGE_URL'])): ?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="element"><?php endif; ?><?=$arItem['NAME']?><?php if(!empty($arItem['DETAIL_PAGE_URL'])): ?></a><?php endif; ?>
            </div>
            <div class="separator"></div>
            <div class="mainmenu-product__price">
                <span class="prices__val prices__val_cool"><?=Loc::getMessage('PRICE_FROM'); ?> <?=$arItemShow['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];?></span>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
