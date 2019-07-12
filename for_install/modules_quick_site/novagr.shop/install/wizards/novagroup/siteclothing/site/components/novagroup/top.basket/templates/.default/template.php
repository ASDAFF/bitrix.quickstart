<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); //$this->setFrameMode(true);
?>
<div id="cart_line_1" class="basket new">

    <a href="#" id="speed-hide-l" class="hide-1">
        <?$createFrame = $this->createFrame("speed-hide-l", false)->begin(); ?>
        <i class="icon-arrow-basket"></i> <?= GetMessage('YOUR_CART_EMPTY') ?> <span
            class="number-basket">(<?= $arResult["NUM_PRODUCTS"] ?>)</span> <span
            class="result-basket"><?= $arResult["SUM"] ?> <?= $arResult["CURRENCY"] ?>.</span>
        <?$createFrame->end()?>
    </a>

    <div class="list-basket" id="<? echo $speedID = "speed-".$this->randString()?>">
        <?$createFrame = $this->createFrame($speedID, false)->begin(); ?>
        <form method="post" action="<?= POST_FORM_ACTION_URI ?>" name="basket_form_2">
            <?
            if (count($arResult["ITEMS"]["AnDelCanBuy"]) < 1) {
                ?>
                <?= GetMessage('CART_EMPTY') ?>
                <? if (!empty($arResult["DELAY"])): ?>
                    <a class="btn bt2 viewCart" href="<?= SITE_DIR ?>cabinet/?t=<?=rand(10,99)?>#id-02"><span><?=GetMessage("DELAY")?></span></a>
                    <!-- Ведет в корзину на вкладку отложенные -->
                <?
                endif;
                //return;
            } else {


                if (count($arResult["ITEMS"]["AnDelCanBuy"]) < 3) {
                    $height = 429 - 143 * (3 - count($arResult["ITEMS"]["AnDelCanBuy"]));
                    $height = 'height:' . $height . 'px;';
                } else {
                    $height = '';
                }

                ?>
                <div id="slider5">

                    <input class="btn bt3" type="button"
                           onclick="$('form[name=basket_form_2] input[name=TopBasketOrder]').click();return false;"
                           value="<?= GetMessage('OFORMIT_ZAKAZ') ?>">
                    <a class="btn bt2 quickBuyButton" data-toggle="modal"
                       href="#oneClickCart"><?= GetMessage('ONE_CLICK') ?></a>
                    <a class="btn bt2 viewCart" href="<?= SITE_DIR ?>cabinet/"><?= GetMessage('VIEW_CART') ?></a>
                    <a href="#" class="buttons prev disable"><span class="icon-chevron-up"></span></a>

                    <div class="viewport" style="<?= $height ?>">

                        <ul class="overview">
                            <?
                            $i = 0;
                            foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arBasketItems) {
                                $sizeID = $arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["SIZE"];

                                $colorID = $arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["COLOR"];

                                $productID = $arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["CML2_LINK"];

                                /*deb("dddd=========");
                                deb($arParams["CATALOG_IBLOCK_ID"]);
                                deb($arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["CML2_LINK"]);
                                deb($arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["COLOR"]);*/
                                $params["sourceURL"] = $arBasketItems["DETAIL_PAGE_URL"];
                                $params["colorID"] = $colorID;
                                $params["productID"] = $productID;
                                $params["sizeID"] = $sizeID;
                                $arBasketItems["DETAIL_PAGE_URL"] = Novagroup_Classes_General_Basket::makeDetailLink($params);
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
                "CATALOG_IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
                "CATALOG_ELEMENT_ID" => $productID,
                "PHOTO_ID" => $arResult["PROPS"][$arBasketItems["PRODUCT_ID"]]["COLOR"],
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
                            <?


                            if (!empty($arResult['COLORS'][$colorID]["PIC"])) {
                                $colorPic = CFile::GetPath($arResult['COLORS'][$colorID]["PIC"]);
                            } else {
                                $colorPic = SITE_TEMPLATE_PATH . "/images/not-f.jpg";;
                            }
                            ?>
                            <span class="color-basket">
                    <span
                        class="button-color-button-12 color-min active-color">
                        <div class="b-C">
                            <img width="10" height="10" border="0" src="<?= $colorPic ?>"
                                 alt="<?= $arResult['COLORS'][$colorID]["NAME"] ?>">
                        </div>
                    </span>
                    <span><?

                        echo $arResult['SIZES'][$sizeID];?>
                        (<?= (int)$arBasketItems["QUANTITY"] . " " . GetMessage('SHTUK') ?>)</span>
                </span>
            </span>
                                    <div class="clear"></div>
                                </li>
                            <?
                            }
                            ?>
                        </ul>

                    </div>
                    <a href="#" class="buttons next"><span class="icon-chevron-down"></span></a>
                    <? if (!empty($arResult["DELAY"])): ?>
                        <a class="btn bt2 viewCart" href="<?= SITE_DIR ?>cabinet/?t=<?=rand(10,99)?>#id-02"><span><?=GetMessage("DELAY")?></span></a>
                        <!-- Ведет в корзину на вкладку отложенные -->
                    <? endif; ?>
                </div>
                <input style="display: none" type="submit" value="<?=GetMessage("OFORMIT_ZAKAZ")?>" name="TopBasketOrder">
            <?
            }
            ?>
        </form>
        <?$createFrame->end()?>
    </div>

</div>
