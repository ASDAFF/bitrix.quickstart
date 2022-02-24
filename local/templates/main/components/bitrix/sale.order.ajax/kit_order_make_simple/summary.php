<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

foreach($arResult["GRID"]["HEADERS"] as $headerId)
    $arHeaders[] = $headerId["id"];
?>

<div class="row main_order row-no-margin">
    <div class="col-xl-9 col-lg-9 col-md-12">
        <div id="basket_rows" class="main_order_block">
            <div class="main_order_block__top_line">
                <span class="main_order_block__title fonts__main_text">
                    <?=Loc::getMessage("SALE_PRODUCTS")?>
                </span>
                <span class="main_order_block__col_buy">
                    <b><?=$quantity?></b> <?=$quantityLabel?> <?=Loc::getMessage("SOA_TEMPL_SUM_SUMMARY")?> <b class="kit_soa_order_price"><?=$arResult["ORDER_PRICE_FORMATED"]?></b>
                </span>
            </div>
            <?
            $i = 0;
            foreach($arResult["GRID"]["ROWS"] as $arData)
            {
                $arItem = $arData["data"];
                $i++;
                ?>
                <div class="main_order_block__item">
<!--                    <div class="main_order_block__item_number">--><?////=$i?><!--.</div>-->

                    <?
                    if(strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0)
                        $url = $arItem["PREVIEW_PICTURE_SRC"];
                    elseif(strlen($arItem["DETAIL_PICTURE_SRC"]) > 0)
                        $url = $arItem["DETAIL_PICTURE_SRC"];
                    else
                        $url = '/upload/kit.origami/no_photo_small.svg';

                    if(strlen($arItem["DETAIL_PAGE_URL"]) > 0)
                        $detailPageUrl = $arItem["DETAIL_PAGE_URL"];
                    $name = substr($arItem["NAME"], 0, 30);
                    ?>

                    <a class="main_order_block__item_img" <?=$detailPageUrl ? 'href="'.$detailPageUrl.'"' : '' ?>>
                        <img src="<?=$url?>" alt="<?=$arItem["NAME"]?>">
                    </a>

                    <div class="main_order_block__item_info__and_price">
                        <div class="main_order_block__item_info">
                            <div class="main_order_block__item_info__title">
                                <a class="main_order_block__item_info__name fonts__main_text" <?=$detailPageUrl ? 'href="'.$detailPageUrl.'"' : '' ?>>
                                    <?=$name?><?if(iconv_strlen($arItem["NAME"]) > 30){ echo "...";}?>
                                </a>
                            </div>
                            <?if(in_array("PROPS", $arHeaders)):?>
                                <div class="main_order_block__item_info__property">
                                    <span class="main_order_block__item_info__property_one fonts__middle_comment">
                                        <?
                                        foreach($arItem["PROPS"] as $val)
                                            echo $val["NAME"] . ": " . $val["VALUE"] . "<br>";
                                        ?>
                                    </span>
                                </div>
                            <?endif?>
                        </div>

                        <?if(in_array("PRICE_FORMATED", $arHeaders)):?>
                            <div class="kit_soa_price_block_<?=$arItem["ID"]?> main_order_block__item_price">
                                <div class="kit_soa_price_<?=$arItem["ID"]?> main_order_block__item_price_new fonts__main_text">
                                    <?=$arItem["PRICE_FORMATED"]?>
                                </div>
                                <?if(doubleval($arItem["DISCOUNT_PRICE"]) > 0):?>
                                    <div class="kit_soa_old_price_<?=$arItem["ID"]?> main_order_block__item_price_old fonts__middle_comment">
                                        <?=SaleFormatCurrency($arItem["PRICE"] + $arItem["DISCOUNT_PRICE"], $arItem["CURRENCY"]);?>
                                    </div>
                                <?endif?>
                            </div>
                        <?endif?>

                        <?if(in_array("DISCOUNT_PRICE_PERCENT_FORMATED", $arHeaders)):?>
                            <div class="main_order_block__item_price">
                                <div class="kit_soa_discount_price_<?=$arItem["ID"]?>">
                                    <?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
                                </div>
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

                        <div class="main_order_block__item_col fonts__middle_comment">
                            <?=$arItem["QUANTITY"]?> <?=$arItem["MEASURE_NAME"]?>
                        </div>

                        <div class="kit_soa_product_sum_block_<?=$arItem["ID"]?> main_order_block__item_price">
                            <div class="kit_soa_product_sum_<?=$arItem["ID"]?> main_order_block__item_price_new fonts__main_text">
                                <?=$arItem["SUM"]?>
                            </div>
                            <?if(doubleval($arItem["DISCOUNT_PRICE"]) > 0):?>
                                <div class="kit_soa_old_product_sum_<?=$arItem["ID"]?> main_order_block__item_price_old fonts__middle_comment">
                                    <?=$arItem["SUM_BASE_FORMATED"]?>
                                </div>
                            <?endif?>
                        </div>

                        <div class="main_order_block__item_slide">
                            <div class="main_order_block__item_col fonts__middle_comment">
                                <span class="main_order_block__item_col_name main_order_block__property_one_color">
                                    <?=Loc::getMessage("KIT_SOA_QUANTITY_TEXT")?>
                                </span>
                                <?=$arItem["QUANTITY"]?> <?=$arItem["MEASURE_NAME"]?>
                            </div>
                            <div class="main_order_block__item_info__property">
                                <span class="main_order_block__item_info__property_one fonts__middle_comment">
                                    <?
                                    foreach($arItem["PROPS"] as $val)
                                        echo '<span class="main_order_block__property_one_color">' . $val["NAME"] . ':</span> ' . $val["VALUE"] . '<br>';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="main_order_block__item_slide_open">
                        <div class="main_order_block__item_slide_open_arrow"></div>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-12 main_order_coupon">
        <div class="main_order_block__top_line">
            <span class="main_order_block__title fonts__main_text"><?=Loc::getMessage("COUPON_DEFAULT");?></span>
        </div>
        <div id="coupon_block" class="main_order_block_feedback">
            <div class="bx-soa-section-content"></div>
        </div>
        <div id="kit_soa_total" style="display: none;"></div>
    </div>
</div>
<div class="order_block__basket_link">
    <a class="order_block__ordering_link fonts__middle_comment" href="<?=$arParams["PATH_TO_BASKET"]?>">
        <i class="fas fa-angle-double-left"></i>
        <?=Loc::getMessage("KIT_SOA_BACK_TO_BASKET");?>
    </a>
</div>
