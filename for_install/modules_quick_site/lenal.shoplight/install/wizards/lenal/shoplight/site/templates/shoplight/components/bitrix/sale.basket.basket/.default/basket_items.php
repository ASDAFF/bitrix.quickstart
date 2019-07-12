<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn = false;

if ($normalCount > 0):
    ?>
    <div class="b-grid">
        <div class="b-grid__column b-grid__column-large">
            <ul class="b-cart-items">
                <?
                foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):

                    if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
                        ?>
                        <li id="item<?= $arItem["ID"] ?>" class="b-cart-items__item">
                            <?
                            if (strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0):
                                $url = $arItem["PREVIEW_PICTURE_SRC"];
                            elseif (strlen($arItem["DETAIL_PICTURE_SRC"]) > 0):
                                $url = $arItem["DETAIL_PICTURE_SRC"];
                            else:
                                $url = $templateFolder . "/images/no_photo.png";
                            endif;
                            ?>

                            <a class="b-cart_items__remove-link" title="<?= GetMessage("SALE_DELETE") ?>" href="<?= str_replace("#ID#", $arItem["ID"], $arUrls["delete"]) ?>" > &times; </a>	
                            <img class="b-cart-items__image" src="<?= $url ?>"/>
                            <div class="b-cart-items__item-details">
                                <? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?>
                                    <a class="b-cart-items__item-name" href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><? endif; ?>
                                    <?= $arItem["NAME"] ?>
                                    <? if (strlen($arItem["DETAIL_PAGE_URL"]) > 0): ?></a>
                                <? endif; ?>
                                <table class="b-cart-items__item-info">
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <?
                                    if ($bPropsColumn):
                                        foreach ($arItem["PROPS"] as $val):

                                            if (is_array($arItem["SKU_DATA"])) {
                                                $bSkip = false;
                                                foreach ($arItem["SKU_DATA"] as $propId => $arProp) {
                                                    if ($arProp["CODE"] == $val["CODE"]) {
                                                        $bSkip = true;
                                                        break;
                                                    }
                                                }
                                                if ($bSkip)
                                                    continue;
                                            }

                                            echo '<tr><td class="b-cart-items__item-info-term">' . $val["NAME"] . ':</td><td class="b-cart-items__item-info-description">' . $val["VALUE"] . '</td></tr>';
                                        endforeach;
                                    endif;
                                    ?>
                                    <tr>
                                        <td class="b-cart-items__item-info-right" colspan="2">
                                            <label class="b-cart-items__item-quantity-label" for="QUANTITY_INPUT_<?= $arItem["ID"] ?>"><?= GetMessage("SALE_QUANTITY") ?></label>
                                            <input
                                                type="text"
                                                size="3"
                                                id="QUANTITY_INPUT_<?= $arItem["ID"] ?>"
                                                name="QUANTITY_INPUT_<?= $arItem["ID"] ?>"
                                                size="2"
                                                maxlength="18"
                                                min="0"
                                                <?= $max ?>
                                                step="<?= $ratio ?>"
                                                style="max-width: 50px"
                                                value="<?= $arItem["QUANTITY"] ?>"
                                                onchange="updateQuantity('QUANTITY_INPUT_<?= $arItem["ID"] ?>', '<?= $arItem["ID"] ?>', '<?= $ratio ?>', '<?= $useFloatQuantity ?>')"
                                                >
                                        </td></tr>
                                    <tr>
                                        <td class="b-cart-items__item-info-right" colspan="2"><?= GetMessage("SALE_CONTENT_DISCOUNT") ?> <?= $arItem["DISCOUNT_PRICE_PERCENT_FORMATED"] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="b-cart-items__item-info-right" colspan="2">
                                            <? if (doubleval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0): ?>
                                                <div class="current_price b-cart-items__item-sum"><?= $arItem["PRICE_FORMATED"] ?></div>
                                                <div class="old_price"><?= $arItem["FULL_PRICE_FORMATED"] ?></div>
                                            <? else: ?>
                                                <div class="current_price b-cart-items__item-sum"><?= $arItem["PRICE_FORMATED"]; ?></div>
                                            <? endif ?>

                                            <? if (strlen($arItem["NOTES"]) > 0): ?>
                                                <div class="type_price"><?= GetMessage("SALE_TYPE") ?></div>
                                                <div class="type_price_value"><?= $arItem["NOTES"] ?></div>
                                            <? endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </li>
                    <? endif; ?>
                <? endforeach; ?>
            </ul>
        </div>
        <div class="b-grid__column b-grid__column-small">
            <div class="b-cart-form">
                <h2 class="b-cart-form__title"><?= GetMessage("SALE_NAME") ?></h2>
                <input type="hidden" id="column_headers" value="<?= CUtil::JSEscape(implode($arHeaders, ",")) ?>" />
                <input type="hidden" id="offers_props" value="<?= CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ",")) ?>" />
                <input type="hidden" id="QUANTITY_FLOAT" value="<?= $arParams["QUANTITY_FLOAT"] ?>" />
                <input type="hidden" id="COUNT_DISCOUNT_4_ALL_QUANTITY" value="<?= ($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N" ?>" />
                <input type="hidden" id="PRICE_VAT_SHOW_VALUE" value="<?= ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N" ?>" />
                <input type="hidden" id="HIDE_COUPON" value="<?= ($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N" ?>" />
                <input type="hidden" id="USE_PREPAYMENT" value="<?= ($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N" ?>" />


                <ul class="b-cart-form__check b-cart-check">
                    <? if ($arParams["HIDE_COUPON"] != "Y"): ?>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><?= GetMessage("STB_COUPON_PROMT") ?></span>
                            <span class="b-cart-check__item-value"><input type="text" id="COUPON" name="COUPON" value="<?= $arResult["COUPON"] ?>" size="21" class="good"></span>
                        </li>
                    <? endif; ?>
                    <? if ($bWeightColumn): ?>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><?= GetMessage("SALE_TOTAL_WEIGHT") ?></span>
                            <span class="b-cart-check__item-value"><?= $arResult["allWeight_FORMATED"] ?></span>
                        </li>
                    <? endif; ?>
                    <? if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y"): ?>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><? echo GetMessage('SALE_VAT_EXCLUDED') ?></span>
                            <span class="b-cart-check__item-value"><?= $arResult["allSum_wVAT_FORMATED"] ?></span>
                        </li>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><? echo GetMessage('SALE_VAT_INCLUDED') ?></span>
                            <span class="b-cart-check__item-value"><?= $arResult["allVATSum_FORMATED"] ?></span>
                        </li>
                    <? endif; ?>

                    <? if (doubleval($arResult["DISCOUNT_PRICE_ALL"]) > 0): ?>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><?= GetMessage("SALE_TOTAL") ?></span>
                            <span class="b-cart-check__item-value"><?= str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"]) ?></span>
                        </li>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"></span>
                            <span class="b-cart-check__item-value"><?= $arResult["PRICE_WITHOUT_DISCOUNT"] ?></span>
                        </li>
                    <? else: ?>
                        <li class="b-cart-check__item">
                            <span class="b-cart-check__item-name"><?= GetMessage("SALE_TOTAL") ?></span>
                            <span class="b-cart-check__item-value"><?= $arResult["allSum_FORMATED"] ?></span>
                        </li>
                    <? endif; ?>
                </ul>



                <div class="bx_ordercart_order_pay_center">
                    <input type="submit" class="b-button_grey" name="BasketRefresh" value="<?= GetMessage('SALE_REFRESH') ?>">
                    <a href="javascript:void(0)" onclick="checkOut();" class="b-button"><?= GetMessage("SALE_ORDER") ?></a>
                </div>
            </div>
        </div>
    </div>
    <?
else:
    ?>
    <div id="basket_items_list">
        <table>
            <tbody>
                <tr>
                    <td colspan="<?= $numCells ?>" style="text-align:center">
                        <div class=""><?= GetMessage("SALE_NO_ITEMS"); ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?
endif;
?>