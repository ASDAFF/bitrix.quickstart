<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if (count($arResult["ITEMS"]) > 0): ?>

    <section class="b-new-sales" id="sales">
        <? if ($arParams["FLAG_PROPERTY_CODE"] == "NEWPRODUCT"): ?>
            <h2 class="b-new-sales__title"><?= GetMessage("CR_TITLE_" . $arParams["FLAG_PROPERTY_CODE"]) ?></h2>
        <? elseif (strlen($arParams["FLAG_PROPERTY_CODE"]) > 0): ?>
            <h2 class="b-new-sales__title"><?= GetMessage("CR_TITLE_" . $arParams["FLAG_PROPERTY_CODE"]) ?></h2>
        <? endif ?>
        <ul class="b-item-list">
            <?
            foreach ($arResult["ITEMS"] as $key => $arItem):
                if (is_array($arItem)) {
                    $bPicture = is_array($arItem["PREVIEW_IMG"]);
                    ?>

                    <li class="b-item-list__item">
                        <div class="b-item-list__item-frame"><a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="b-item-list__item-link">

                            <? if ($bPicture): ?>
                                <img class="b-item-list__item-image" itemprop="image" src="<?= $arItem["PREVIEW_IMG"]["SRC"] ?>" width="<?= $arItem["PREVIEW_IMG"]["WIDTH"] ?>" height="<?= $arElement["PREVIEW_IMG"]["HEIGHT"] ?>" alt="<?= $arElement["NAME"] ?>" />
                            <? else: ?>
                                <div class="no-photo-div-big" style="height:130px; width:130px;"></div>
                            <? endif ?>
                            <div class="b-item-list__item-bottom">
                                <div class="b-item-list__item-name"> <?= $arItem["NAME"] ?> </div>
                                <div class="b-item-list__item-price">
                                <?
                                    $numPrices = count($arParams["PRICE_CODE"]);
                                    foreach ($arItem["PRICES"] as $code => $arPrice):
                                        if ($arPrice["CAN_ACCESS"]):
                                            ?>
                                            <? if ($numPrices > 1): ?><p style="padding: 0; margin-bottom: 5px;"><?= $arResult["PRICES"][$code]["TITLE"]; ?>:</p><? endif ?>
                    <? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                                                <span itemprop = "price" class="item_price discount-price"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span><br>
                                                <span itemprop = "price" class="old-price"><?= $arPrice["PRINT_VALUE"] ?></span>
                                            <? else: ?>
                                                <span itemprop = "price" class="item_price price"><?= $arPrice["PRINT_VALUE"] ?></span>
                                            <?
                                            endif;
                                        endif;
                                    endforeach;
                                    ?>    
                                </div>
                                <div id="add2cart_button<?=$arResult["ID"]?>" class="b-item-list__item-buy b-yellow-button"> <? echo GetMessage("CATALOG_ADD") ?> </div>
                            </div>
                            </a></div>
                    </li>

                    <?
                }
            endforeach;
            ?>
        </ul>
    </section>
<? elseif ($USER->IsAdmin()): ?>
    <h3 class="hitsale"><span></span><?= GetMessage("CR_TITLE_" . $arParams["FLAG_PROPERTY_CODE"]) ?></h3>
    <div class="listitem-carousel">
    <?= GetMessage("CR_TITLE_NULL") ?>
    </div>
<? endif; ?>