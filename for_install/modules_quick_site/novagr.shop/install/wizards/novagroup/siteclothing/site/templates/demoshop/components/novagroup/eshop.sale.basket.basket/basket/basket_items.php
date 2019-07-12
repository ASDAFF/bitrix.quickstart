<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if (count($arResult["ITEMS"]["AnDelCanBuy"]) < 1) {
    ?>
        <?= GetMessage('CART_EMPTY') ?>
    <?
    return;
}
?>
<div id="slider5">

    <?
    if (count($arResult["ITEMS"]["AnDelCanBuy"]) < 3) {
        $height = 429 - 143 * (3 - count($arResult["ITEMS"]["AnDelCanBuy"]));
        $height = 'height:' . $height . 'px;';
    } else {
        $height = '';
    }
    ?>
    <input class="btn bt3" type="button"
           onclick="$('form[name=basket_form_2] input[name=BasketOrder]').click();return false;"
           value="<?= GetMessage('OFORMIT_ZAKAZ') ?>">
    <a class="btn bt2 quickBuyButton" data-toggle="modal" href="#oneClickCart"><?= GetMessage('ONE_CLICK') ?></a>
    <a class="btn bt2 viewCart" href="<?= SITE_DIR ?>cabinet/"><?= GetMessage('VIEW_CART') ?></a>
    <a href="#" class="buttons prev disable"><span class="icon-chevron-up"></span></a>

    <div class="viewport" style="<?= $height ?>">
        <ul class="overview">
            <?
            $i = 0;
            foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arBasketItems) {
                //deb($arBasketItems);
                $arBasketItems["DETAIL_PAGE_URL"] = (trim($arBasketItems["DETAIL_PAGE_URL"]) <> "") ? $arBasketItems["DETAIL_PAGE_URL"] : "#";
                ?>
                <li id="hideme-<?= $key ?>">
                    <input type="hidden" name="QUANTITY_<?= $arBasketItems["ID"] ?>"
                           value="<?= $arBasketItems["QUANTITY"] ?>">
                <span class="generabl">
                    <span class="close" onclick="hideBasketItem('<?= $arBasketItems["ID"] ?>')">&times;</span>
                    <div class="depiction">
                        <a href="<?= $arBasketItems["DETAIL_PAGE_URL"] ?>">
                            <span class="block-img-b">
                                <?$APPLICATION->IncludeComponent(
                                    "novagroup:catalog.element.photo",
                                    "",
                                    Array(
                                        "CATALOG_IBLOCK_ID" => $arBasketItems['ELEMENT_ID']['IBLOCK_ID'],
                                        "CATALOG_ELEMENT_ID" => $arBasketItems['ELEMENT_ID']['ID'],
                                        "PHOTO_ID" => $arBasketItems['COLOR'],
                                        "PHOTO_WIDTH" => "68",
                                        "PHOTO_HEIGHT" => "76"
                                    ),
                                    false,
                                    Array(
                                        'ACTIVE_COMPONENT' => 'Y',
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>
                            </span>
                        </a>
                        <a class="wrapped-depiction" href="<?= $arBasketItems["DETAIL_PAGE_URL"] ?>">
                            <span class="name-mini"><?= $arBasketItems["NAME"] ?></span>
                        </a>
                    </div>
                    <? if (in_array("PRICE", $arParams["COLUMNS_LIST"])): ?>
                        <span class="price-basket">
                            <? if (doubleval($arBasketItems["FULL_PRICE"]) > 0): ?>
                                <span class="actual discount">
                                    <?= $arBasketItems["PRICE_FORMATED"] ?>
                                </span>
                                <span class="old-price">
                                    <?= $arBasketItems["FULL_PRICE_FORMATED"] ?>
                                </span>
                            <? else: ?>
                                <span class="actual">
                                  <?= $arBasketItems["PRICE_FORMATED"] ?>
                                </span>
                            <?endif ?>
                       </span>
                    <? endif; ?>
                    <?
                    if (!empty($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"])) {
                        $colorPic = CFile::GetPath($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"]);
                    } else {
                        $colorPic = SITE_TEMPLATE_PATH."/images/not-f.jpg";;
                    }
                            ?>
                            <span class="color-basket">
                        <span
                            class="button-color-button-12 color-min active-color">
                            <div class="b-C">
                                <img width="10" height="10" style="width:10px;height:10px;display:block;" border="0" src="<?=$colorPic?>" alt="<?=$arResult['COLORS'][$arBasketItems["COLOR"]]["NAME"]?>">
                            </div>
                        </span>
                        <span><?= $arResult['COLORS'][$arBasketItems["STD_SIZE"]]["NAME"];?>
                            (<?= $arBasketItems["QUANTITY"] . " " . GetMessage('SHTUK') ?>)</span>
                    </span>
                        <?
                    ?>
                </span>

                    <div class="clear"></div>
                </li>
            <?
            }
            ?>
        </ul>
    </div>
    <a href="#" class="buttons next"><span class="icon-chevron-down"></span></a>
</div>
<input style="display: none" type="submit" value="<? echo GetMessage("SALE_ORDER") ?>" name="BasketOrder">