<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="catalog-section">
    <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?><br/>
    <? endif; ?>
    <section>
        <? foreach ($arResult["ITEMS"] as $cell => $arElement): ?>
            <?
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
            ?>

                <article id="<?= $this->GetEditAreaId($arElement['ID']); ?>">

                        <? if (is_array($arElement["PREVIEW_PICTURE"])): ?>
                                <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>">
                                    <img
                                            src="<?= $arElement["PREVIEW_PICTURE"]["SRC"] ?>"
                                            width="<?= $arElement["PREVIEW_PICTURE"]["WIDTH"] ?>"
                                            height="<?= $arElement["PREVIEW_PICTURE"]["HEIGHT"] ?>"
                                            alt="<?= $arElement["PREVIEW_PICTURE"]["ALT"] ?>"
                                            title="<?= $arElement["PREVIEW_PICTURE"]["TITLE"] ?>"
                                    />
                                </a>
                        <? elseif (is_array($arElement["DETAIL_PICTURE"])): ?>
                                <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>">
                                    <img
                                            src="<?= $arElement["DETAIL_PICTURE"]["SRC"] ?>"
                                            width="<?= $arElement["DETAIL_PICTURE"]["WIDTH"] ?>"
                                            height="<?= $arElement["DETAIL_PICTURE"]["HEIGHT"] ?>"
                                            alt="<?= $arElement["DETAIL_PICTURE"]["ALT"] ?>"
                                            title="<?= $arElement["DETAIL_PICTURE"]["TITLE"] ?>"
                                    />
                                </a>
                        <? endif ?>
                        <div>
                            <a href="<?= $arElement["DETAIL_PAGE_URL"] ?>"><b><?= $arElement["NAME"] ?></b></a>
                            <div>
                            <?
                            $pub_date = '';
                            if ($arElement["ACTIVE_FROM"])
                                $pub_date = FormatDate($GLOBALS['DB']->DateFormatToPhp(CSite::GetDateFormat('FULL')), MakeTimeStamp($arElement["ACTIVE_FROM"]));
                            elseif ($arElement["DATE_CREATE"])
                                $pub_date = FormatDate($GLOBALS['DB']->DateFormatToPhp(CSite::GetDateFormat('FULL')), MakeTimeStamp($arElement["DATE_CREATE"]));

                            if ($pub_date)
                                echo '<b>' . GetMessage('PUB_DATE') . '</b>&nbsp;' . $pub_date . '<br />';
                            ?>
                            </div>
                                <? foreach ($arElement["DISPLAY_PROPERTIES"] as $pid => $arProperty):
                                echo '<b>' . $arProperty["NAME"] . ':</b>&nbsp;';

                                if (is_array($arProperty["DISPLAY_VALUE"]))
                                    echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
                                else
                                    echo $arProperty["DISPLAY_VALUE"];
                                ?><br/>
                            <? endforeach ?>
                            <br/>
                            <?= $arElement["PREVIEW_TEXT"] ?>
                        </div>



                <? foreach ($arElement["PRICES"] as $code => $arPrice): ?>
                    <? if ($arPrice["CAN_ACCESS"]): ?>
                        <p><?= $arResult["PRICES"][$code]["TITLE"]; ?>:&nbsp;&nbsp;
                            <? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                                <s><?= $arPrice["PRINT_VALUE"] ?></s> <span
                                        class="catalog-price"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span>
                            <? else: ?><span class="catalog-price"><?= $arPrice["PRINT_VALUE"] ?></span><? endif; ?>
                        </p>
                    <? endif; ?>
                <? endforeach; ?>

                <? if (is_array($arElement["PRICE_MATRIX"])): ?>
                    <table class="data-table">
                        <thead>
                        <tr>
                            <? if (count($arElement["PRICE_MATRIX"]["ROWS"]) >= 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)): ?>
                                <td valign="top" nowrap><?= GetMessage("CATALOG_QUANTITY") ?></td>
                            <? endif ?>
                            <? foreach ($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType): ?>
                                <td valign="top" nowrap><?= $arType["NAME_LANG"] ?></td>
                            <? endforeach ?>
                        </tr>
                        </thead>
                        <? foreach ($arElement["PRICE_MATRIX"]["ROWS"] as $ind => $arQuantity): ?>
                            <tr>
                                <? if (count($arElement["PRICE_MATRIX"]["ROWS"]) > 1 || count($arElement["PRICE_MATRIX"]["ROWS"]) == 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)): ?>
                                    <th nowrap><?
                                        if (IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
                                            echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
                                        elseif (IntVal($arQuantity["QUANTITY_FROM"]) > 0)
                                            echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
                                        elseif (IntVal($arQuantity["QUANTITY_TO"]) > 0)
                                            echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
                                        ?></th>
                                <? endif ?>
                                <? foreach ($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType): ?>
                                    <td><?
                                        if ($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"]):?>
                                            <s><?= FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]) ?></s>
                                            <span class="catalog-price"><?= FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]); ?></span>
                                        <? else:?>
                                            <span class="catalog-price"><?= FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]); ?></span>
                                        <? endif ?>&nbsp;
                                    </td>
                                <? endforeach ?>
                            </tr>
                        <? endforeach ?>
                    </table>
                <? endif ?>
                <? if ($arParams["DISPLAY_COMPARE"]): ?>
                    <noindex>
                        <a href="<? echo $arElement["COMPARE_URL"] ?>"
                           rel="nofollow"><? echo GetMessage("CATALOG_COMPARE") ?></a>&nbsp;
                    </noindex>
                <? endif ?>

                    <a href="<?echo $arElement["ADD_URL"]?>"  class="catalog-item-buy input-basket-submit"
                       rel="nofollow" id="ajaxaction=add&ajaxaddid=<?=$arElement['ID'];?>"><?echo GetMessage("CATALOG_ADD")?></a> /* Добавление в корзину ссылкой */

                <? if ($arElement["CAN_BUY"]): ?>
                    <? if ($arParams["USE_PRODUCT_QUANTITY"] || count($arElement["PRODUCT_PROPERTIES"])): ?>
                        <div>
                        <form action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
                            <table>
                                <? if ($arParams["USE_PRODUCT_QUANTITY"]): ?>
                                    <tr valign="top">
                                        <td><? echo GetMessage("CT_BCS_QUANTITY") ?>:</td>
                                        <td>
                                            <input type="text" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"] ?>"
                                                   value="1" size="5">
                                        </td>
                                    </tr>
                                <? endif; ?>
                                <? foreach ($arElement["PRODUCT_PROPERTIES"] as $pid => $product_property): ?>
                                    <tr valign="top">
                                        <td><? echo $arElement["PROPERTIES"][$pid]["NAME"] ?>:</td>
                                        <td>
                                            <? if (
                                                $arElement["PROPERTIES"][$pid]["PROPERTY_TYPE"] == "L"
                                                && $arElement["PROPERTIES"][$pid]["LIST_TYPE"] == "C"
                                            ): ?>
                                                <? foreach ($product_property["VALUES"] as $k => $v): ?>
                                                    <label><input type="radio"
                                                                  name="<? echo $arParams["PRODUCT_PROPS_VARIABLE"] ?>[<? echo $pid ?>]"
                                                                  value="<? echo $k ?>" <? if ($k == $product_property["SELECTED"]) echo '"checked"' ?>><? echo $v ?>
                                                    </label><br>
                                                <? endforeach; ?>
                                            <? else: ?>
                                                <select name="<? echo $arParams["PRODUCT_PROPS_VARIABLE"] ?>[<? echo $pid ?>]">
                                                    <? foreach ($product_property["VALUES"] as $k => $v): ?>
                                                        <option value="<? echo $k ?>" <? if ($k == $product_property["SELECTED"]) echo '"selected"' ?>><? echo $v ?></option>
                                                    <? endforeach; ?>
                                                </select>
                                            <? endif; ?>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </table>
                            <input type="hidden" name="<? echo $arParams["ACTION_VARIABLE"] ?>" value="BUY">
                            <input type="hidden" name="<? echo $arParams["PRODUCT_ID_VARIABLE"] ?>"
                                   value="<? echo $arElement["ID"] ?>">
                            <input type="submit" name="<? echo $arParams["ACTION_VARIABLE"] . "BUY" ?>"
                                   value="<? echo GetMessage("CATALOG_BUY") ?>">
                            <input type="submit" name="<? echo $arParams["ACTION_VARIABLE"] . "ADD2BASKET" ?>"
                                   value="<? echo GetMessage("CATALOG_ADD") ?>">
                        </form>
                        </div>
                    <? else: ?>
                        <noindex>
                            <a href="<? echo $arElement["BUY_URL"] ?>"
                               rel="nofollow"><? echo GetMessage("CATALOG_BUY") ?></a>&nbsp;<a
                                    href="<? echo $arElement["ADD_URL"] ?>"
                                    rel="nofollow"><? echo GetMessage("CATALOG_ADD") ?></a>
                        </noindex>
                    <? endif ?>
                <? elseif ((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])): ?>
                    <?= GetMessage("CATALOG_NOT_AVAILABLE") ?>
                    <? $APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
                        "NOTIFY_ID" => $arElement['ID'],
                        "NOTIFY_URL" => htmlspecialcharsback($arElement["SUBSCRIBE_URL"]),
                        "NOTIFY_USE_CAPTHA" => "N"
                    ),
                        $component
                    ); ?>
                <? endif ?>
                </article>
        <? endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

    </section>
    <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <br/><?= $arResult["NAV_STRING"] ?>
    <? endif; ?>
</div>
