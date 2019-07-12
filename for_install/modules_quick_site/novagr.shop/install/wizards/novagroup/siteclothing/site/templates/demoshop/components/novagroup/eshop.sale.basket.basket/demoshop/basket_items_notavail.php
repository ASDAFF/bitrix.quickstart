<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="invest-ed">
<br clear="all"><h2><?=GetMessage('NO_ITEMS')?></h2>
<div class="tab-content">
<div class="tab-pane fade active in">
<?php
$countElems = count($arResult["ITEMS"]["nAnCanBuy"]);
if ($countElems>0) {
    $tableClass = 'table-striped';

} else {
    $tableClass = 'empty-basket';
}
?>

<table rules="rows" class="table table-bordered <?=$tableClass?> equipment">
    <thead>
    <tr>
        <?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
            <td></td>
            <td><?= GetMessage("SALE_NAME")?></td>

            <?$numCells += 2;?>
        <?endif;?>
        <?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
            <td><?= GetMessage("SALE_VAT")?></td>
            <?$numCells++;?>
        <?endif;?>
        <?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
            <td class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></td>
            <?$numCells++;?>
        <?endif;?>
        <?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
            <td class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></td>
            <?$numCells++;?>
        <?endif;?>
        <?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
            <td class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></td>
            <?$numCells++;?>
        <?endif;?>
        <?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
            <td class="cart-item-quantity"><?//= GetMessage("SALE_QUANTITY")?></td>
            <?$numCells++;?>
        <?endif;?>
        <?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
            <td class="cart-item-price"><?= GetMessage("SALE_PRICE")?></td>
            <?$numCells++;?>
        <?endif;?>
        <td>&nbsp;</td>
    </tr>
    </thead>
    <?
    if ($countElems>0) :
    ?>
    <tbody>
    <?
    $i=0;
    foreach($arResult["ITEMS"]["nAnCanBuy"] as $arBasketItems)
    {
        $params["sourceURL"] = $arBasketItems["DETAIL_PAGE_URL"];
        $params["colorID"] = $arBasketItems["COLOR"];
        $params["productID"] = $arBasketItems["ELEMENT_ID"]["ID"];
        $params["sizeID"] = $arBasketItems["SIZE"];
        $arBasketItems["DETAIL_PAGE_URL"] = Novagroup_Classes_General_Basket::makeDetailLink($params);
        ?>
        <tr>
            <?
            if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
                <td class="prev-img">
                    <?
                    // выводим превью
                    if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
                    ?><a target="_blank" href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
                        endif;
                        //$imgSrc = CFile::GetPath($arBasketItems["PREVIEW_PICTURE"]);
                        ?>
                        <img width="90" height="119"
                             alt="<?=htmlspecialcharsEx($arBasketItems["NAME"])?>"
                             src="<?$APPLICATION->IncludeComponent(
                                 "novagroup:catalog.element.photo",
                                 "path",
                                 Array(
                                     "CATALOG_IBLOCK_ID" => $arBasketItems['ELEMENT_ID']['IBLOCK_ID'],
                                     "CATALOG_ELEMENT_ID" => $arBasketItems['ELEMENT_ID']['ID'],
                                     "PHOTO_ID" => $arBasketItems['COLOR'],
                                     "PHOTO_WIDTH" => "90",
                                     "PHOTO_HEIGHT" => "119"
                                 ),
                                 false,
                                 Array(
                                     'ACTIVE_COMPONENT' => 'Y',
                                     "HIDE_ICONS"=>"Y"
                                 )
                             );?>">
                        <?php

                        if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
                        ?></a><?
                endif;
                /*if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
                        <a class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" onclick="//return DeleteFromCart(this);" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a>
                    <?endif;?>
                    <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
                        <a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
                    <?endif;?>
                    <?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
                        <img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
                    <?else:?>
                        <img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>"/>
                    <?endif?>
                    <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
                        </a>
                    <?endif;*/?>
                </td>
                <td class="cart-item-name">
                    <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
                    <p><a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
                            <?endif;?>
                            <?=$arBasketItems["NAME"] ?>
                            <?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
                        </a></p>
                <?endif;


                if (!empty($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"])) {
                    $colorPic = CFile::GetPath($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"]);
                } else {
                    $colorPic = SITE_TEMPLATE_PATH."/images/not-f.jpg";;
                }
                ?>
                    <p><span class="m-demo"><?=GetMessage("SIZE_PRODUCT")?>:</span> <span class="size-bas-demo"><?=$arResult['SIZES'][$arBasketItems["SIZE"]]?></span></p>
                    <p><span class="m-demo"><?=GetMessage("COLOR_PRODUCT")?>:</span> <span><img width="35" height="33" border="0" src="<?=$colorPic?>" alt="<?=$arResult['COLORS'][$arBasketItems["COLOR"]]["NAME"]?>"></span></p>
                    <?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
                    {
                        /*foreach($arBasketItems["PROPS"] as $val)
                        {
                            echo "<br />".$val["NAME"].": ".$val["VALUE"];
                        }*/
                    }?>
                </td>
            <?endif;?>
            <?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
                <td><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
            <?endif;?>
            <?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
                <td><?=$arBasketItems["NOTES"]?></td>
            <?endif;?>
            <?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
                <td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
            <?endif;?>
            <?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
                <td><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
            <?endif;?>
            <?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
                <td width="97">
                    <?//=$arBasketItems["QUANTITY"]?>
                </td>
            <?endif;?>
            <?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
                <td class="cart-item-price">
                    <?if(doubleval($arBasketItems["FULL_PRICE"]) > 0):?>
                        <div class="discount-price"><?=$arBasketItems["PRICE_FORMATED"]?></div>
                        <div class="old-price"><?=$arBasketItems["FULL_PRICE_FORMATED"]?></div>
                    <?else:?>
                        <div class="price"><?=$arBasketItems["PRICE_FORMATED"];?></div>
                    <?endif?>
                </td>
            <?endif;?>

            <?//if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
            <td width="103">
            <?=GetMessage('NOT_AVAILABLE')?>
                <p>
                <a title="<?=GetMessage("SALE_DELETE_PRD")?>" class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>"><i class="icon-remove-sign"></i><?=GetMessage("SALE_DELETE")?></a></a>
                </p>
            </td>
            <?//endif;?>
        </tr>
        <?
        $i++;
    } // end foreach
    ?>
    </tbody>
</table>
<?endif;
?>
</div>
</div>
<?
//deb($arParams["COLUMNS_LIST"]);
$numCells = 0;
/*
BX('QUANTITY_<?=$arBasketItems["ID"]?>').value++;
*/
?>
</div>