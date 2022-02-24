<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

foreach($arResult["GRID"]["HEADERS"] as $headerId)
    $arHeaders[] = $headerId["id"];
?>

<div class="row main_order row-no-margin">
    <div class="col-12">
        <div id="basket_rows" class="main_order_block">
            <div class="main_order_block__top_line new_color">
                <?
                foreach($arResult["GRID"]["HEADERS"] as $header)
                {
                    if($header["id"] == "PREVIEW_PICTURE")
                        continue;
                    ?>
                    <div class="<?=$header["id"]?>">
                        <?=$header["name"]?>
                    </div>
                    <?
                }
                ?>
            </div>
            <?
            $i = 0;
            foreach($arResult["GRID"]["ROWS"] as $arData)
            {
                $arItem = $arData["data"];
                $i++;
                ?>
                <div class="main_order_block__item">
                    <?
                    /*if(strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0)
                        $url = $arItem["PREVIEW_PICTURE_SRC"];
                    elseif(strlen($arItem["DETAIL_PICTURE_SRC"]) > 0)
                        $url = $arItem["DETAIL_PICTURE_SRC"];
                    else
                        $url = $templateFolder."/images/no_photo.png";*/

                    if(strlen($arItem["DETAIL_PAGE_URL"]) > 0)
                        $detailPageUrl = $arItem["DETAIL_PAGE_URL"];
                    $name = substr($arItem["NAME"], 0, 37);
                    ?>

                    <?/*<a class="main_order_block__item_img" <?=$detailPageUrl ? 'href="'.$detailPageUrl.'"' : '' ?>>
                        <img src="<?=$url?>" alt="<?=$arItem["NAME"]?>">
                    </a>*/?>

                    <div class="main_order_block__item_information">

                        <div class="title_block">
                            <span><?=$i?>.</span>
                            <a class="main_order_block__item_info__name" <?=$detailPageUrl ? 'href="'.$detailPageUrl.'"' : '' ?>>
                                <?=$name?><?if(iconv_strlen($arItem["NAME"]) > 37){ echo "...";}?>
                            </a>
                        </div>

                        <?if(in_array("PROPS", $arHeaders)):?>
                            <div class="properties_block">
                                <span class="main_order_block__item_info__property_one fonts__middle_comment">
                                    <?
                                    foreach($arItem["PROPS"] as $val)
                                        echo '<span class="main_order_block__property_one_color">' . $val["NAME"] . ':</span> ' . $val["VALUE"] . '<br>';
                                    ?>
                                </span>
                            </div>
                        <?endif?>

                        <?if(in_array("NOTES", $arHeaders)):?> <!-- price type -->
                            <?if(strlen($arItem["NOTES"]) > 0):?>
                                <div class="main_order_block__item_price">
                                    <div><?=Loc::getMessage("SALE_TYPE")?></div>
                                    <div><?=$arItem["NOTES"]?></div>
                                </div>
                            <?endif;?>
                        <?endif?>

                        <?if(in_array("DISCOUNT_PRICE_PERCENT_FORMATED", $arHeaders)):?>
                            <div class="discount_block">
                                <span class="discount_block_hidden fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_DISCOUNT_TEXT")?>&nbsp;</span>
                                <div class="kit_soa_discount_price_<?=$arItem["ID"]?>">
                                    <?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
                                </div>
                            </div>
                        <?endif?>

                        <?if(in_array("PRICE_FORMATED", $arHeaders)):?>
                            <div class="kit_soa_price_block_<?=$arItem["ID"]?> price_block">
                                <span class="price_block_hidden fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_PRICE_TEXT")?>&nbsp;</span>
                                <div class="kit_soa_price_<?=$arItem["ID"]?> main_order_block__item_price_new fonts__middle_text">
                                    <?=$arItem["PRICE_FORMATED"]?>
                                </div>
                                <?if(doubleval($arItem["DISCOUNT_PRICE"]) > 0):?>
                                    <div class="kit_soa_old_price_<?=$arItem["ID"]?> main_order_block__item_price_old fonts__middle_comment">
                                        <?=SaleFormatCurrency($arItem["PRICE"] + $arItem["DISCOUNT_PRICE"], $arItem["CURRENCY"]);?>
                                    </div>
                                <?endif?>
                            </div>
                        <?endif?>

                        <div class="number_block">
                            <span class="number_block_hidden fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_QUANTITY_TEXT")?>&nbsp;</span>
                            <?=$arItem["QUANTITY"]?> <?=$arItem["MEASURE_NAME"]?>
                        </div>

                        <div class="kit_soa_product_sum_block_<?=$arItem["ID"]?> sum_block">
                            <span class="sum_block_hidden fonts__middle_comment"><?=Loc::getMessage("KIT_SOA_SUM_TEXT")?>&nbsp;</span>
                            <div class="kit_soa_product_sum_<?=$arItem["ID"]?> main_order_block__item_price_new fonts__main_text">
                                <?=$arItem["SUM"]?>
                            </div>
                            <?if(doubleval($arItem["DISCOUNT_PRICE"]) > 0):?>
                                <div class="kit_soa_old_product_sum_<?=$arItem["ID"]?> main_order_block__item_price_old fonts__middle_comment">
                                    <?=$arItem["SUM_BASE_FORMATED"]?>
                                </div>
                            <?endif?>
                        </div>

                    </div>
                </div>
                <?
            }
            ?>
        </div>
        <script>
            function setWidthForTable() {
                var length = $("#basket_rows .main_order_block__top_line").children().length - 4;
                var w = (30 / length) + '%'; // 100 - (30 + 10 + 15 + 15) = 30
                var items = "#basket_rows .main_order_block__item .main_order_block__item_information";
                var headers = "#basket_rows .main_order_block__top_line";
                var windowWidth = $(window).width();

                if(windowWidth >= "768") {
                    $(items + " .title_block").width("30%");
                    $(items + " .number_block").width("10%");
                    $(items + " .sum_block").width("15%");
                    $(items + " .properties_block").width("15%");
                    $(items + " > div:not(.title_block, .number_block, .sum_block, .properties_block, .main_order_block__item_slide)").width(w);

                    $(headers + " .NAME").width("30%");
                    $(headers + " .QUANTITY").width("10%").css("text-align", "center");
                    $(headers + " .SUM").width("15%").css("text-align", "right");
                    $(headers + " .PROPS").width("15%");
                    $(headers + "  div:not(.NAME, .QUANTITY, .SUM, .PROPS)").width(w).css("text-align", "center");
                }
                else
                {
                    $(items + " .title_block").removeAttr("style");
                    $(items + " .number_block").removeAttr("style");
                    $(items + " .sum_block").removeAttr("style");
                    $(items + " .properties_block").removeAttr("style");
                    $(items + " > div:not(.title_block, .number_block, .sum_block, .properties_block, .main_order_block__item_slide)").removeAttr("style");

                    $(headers + " .NAME").removeAttr("style");
                    $(headers + " .QUANTITY").removeAttr("style");
                    $(headers + " .SUM").removeAttr("style");
                    $(headers + " .PROPS").removeAttr("style");
                    $(headers + "  div:not(.NAME, .QUANTITY, .SUM, .PROPS)").removeAttr("style");
                }
            }
        </script>
    </div>
    <div class="col-12 main_order_coupon">
        <div class="main_order_block_feedback">
            <div id="coupon_block">
                <span class="main_order_block__title fonts__middle_text"><?=Loc::getMessage("COUPON_DEFAULT");?></span>
                <div class="bx-soa-section-content"></div>
            </div>
            <span class="main_order_block__col_buy">
                <b><?=$quantity?></b> <?=$quantityLabel?> <?=Loc::getMessage("SOA_TEMPL_SUM_SUMMARY")?> <b class="kit_soa_order_price main_order_block__link"><?=$arResult["ORDER_PRICE_FORMATED"]?></b>
            </span>
            <div id="kit_soa_total" style="display: none;"></div>
        </div>
    </div>
</div>
<div class="order_block__basket_link">
    <a class="order_block__ordering_link fonts__middle_comment" href="<?=$arParams["PATH_TO_BASKET"]?>">
        <i class="fas fa-angle-double-left"></i>
        <?=Loc::getMessage("KIT_SOA_BACK_TO_BASKET");?>
    </a>
</div>
