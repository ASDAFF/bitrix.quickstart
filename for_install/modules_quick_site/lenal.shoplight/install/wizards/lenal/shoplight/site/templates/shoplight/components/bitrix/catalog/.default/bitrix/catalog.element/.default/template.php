<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<div class="b-item">
    <div class="b-grid">
        <div class="b-grid__column b-grid__column-left">
            <div class="b-item__main-image">
                <?
                reset($arResult['MORE_PHOTO']);
                $arFirstPhoto = current($arResult['MORE_PHOTO']);
                ?>
                <div class="zoomWrapper" style="height:475px;width:475px;">
                    <img id="b-item__main-image__img" data-zoom-image="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>">
                </div>
            </div>
            <div class="b-item__left-column__separator"></div>    
        </div>
        <div class="b-grid__column b-grid__column-right">
            <div class="b-item__title"><?= $arResult["NAME"] ?></div>
            <div id="item2cart" class="b-item__price-buy" data-item-pk="<?= $arResult["ID"] ?>">
                <div class="b-item__price-buy__price">
                    <div class="b-item__price-buy__price__old-price" id="<? echo $arItemIDs['OLD_PRICE']; ?>" style="display: <? echo ($boolDiscountShow ? '' : 'none'); ?>"><? echo ($boolDiscountShow ? $arResult['MIN_PRICE']['PRINT_VALUE'] : ''); ?></div>
                    <div class="b-item__price-buy__price__new-price-only" id="<? echo $arItemIDs['PRICE']; ?>"><? echo $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></div>
                </div>
                <div class="b-item__price-buy__quantity">
                    <div class="b-item__price-buy__quantity-label"><?= GetMessage('CATALOG_QUANTITY'); ?></div>
                    <div class="b-item__price-buy__quantity-select">
                        <input type="text" size="6" id="quantity_select_<?= $arResult["ID"] ?>" value="1"/>
                    </div>
                </div>
                <div class="b-item__price-buy__buy">
                    <a class="b-button" onclick="add2cart(<?= $arResult["ID"] ?>);
                            return false;" href="<?echo $arResult["ADD_URL"]?>" rel="nofollow"><?= GetMessage('CATALOG_BUY'); ?></a>
                </div>
            </div>
            <?
            if ('' != $arResult['DETAIL_TEXT']) {
                ?>
                <div class="item__description">
                    <?
                    if ('html' == $arResult['DETAIL_TEXT_TYPE']) {
                        echo $arResult['DETAIL_TEXT'];
                    } else {
                        ?><p><? echo $arResult['DETAIL_TEXT']; ?></p><?
                    }
                    ?>
                </div>
                <?
            }
            ?>

            <?
            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
                ?>
                <div class="b-item__details">
                    <div class="b-item__details-label"><?= GetMessage('FULL_DESCRIPTION'); ?></div>
                    <?
                    if (!empty($arResult['DISPLAY_PROPERTIES'])) {
                        ?>
                        <?
                        foreach ($arResult['DISPLAY_PROPERTIES'] as &$arOneProp) {
                            ?>
                            <div class="b-item__details__item">
                                <div class="b-item__details__item-label"><? echo $arOneProp['NAME']; ?></div><?
                                echo '<div class="b-item__details__item-value">', (
                                is_array($arOneProp['DISPLAY_VALUE']) ? implode(' / ', $arOneProp['DISPLAY_VALUE']) : $arOneProp['DISPLAY_VALUE']
                                ), '</div></div><div class="b-separator-h"></div>';
                            }
                            unset($arOneProp);
                            ?>
                            <? };
                        ?>
                    </div><? }?>


            </div>
            <hr class="b-content-separator-h">
        </div>
    </div>