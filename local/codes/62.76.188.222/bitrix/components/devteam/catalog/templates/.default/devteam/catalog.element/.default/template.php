<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="b-section-menu-container clearfix">
<? 
$APPLICATION->IncludeComponent(
	"devteam:section.menu.wrapper",
	"",
	Array(  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "SECTION_ID" => $arResult["IBLOCK_SECTION_ID"]
		));?>
<div class="b-section-menu-back"><a href="<?= $arResult['SECTION']["SECTION_PAGE_URL"] ?>">Вернуться к списку товаров</a></div>
</div>
<section class="b-detail">
<div class="b-detail-header clearfix">
<? if ($arResult['PICTURES']) { ?>                                    
<div class="b-detail-photo">
                    <?if(count($arResult['PICTURES']) > 1):?>
    <ul class="b-detail-photo_list clearfix">  
        <? foreach ($arResult['PICTURES'] as $k => $picture) { ?>
            <li class="b-detail-photo_list__item<? if ($k == 0) { ?> active<? } ?>"><a href="<?= $picture['SRC']; ?>" class="b-detail-photo_list__link"><img src="<?= $picture['src']; ?>" alt="" class="b-detail-photo_list__images" /></a></li>
        <? } ?>
    </ul>
    <?endif?>
    <div class="b-detail__zoom"><a href="<?= $arResult['PICTURES'][0]['SRC']; ?>" id="DETAIL_ZOOM_PHOTO"><img src="<?= $arResult['PICTURES'][0]['SRC']; ?>" alt="" class="b-detail-photo__image" id="DETAIL_IMAGE_BIG" /></a></div>
    <div><a href="#" class="b-detail-photo__zoom" id="DETAIL_ZOOM"><span>Увеличить</span></a></div>
</div>
<? } ?>	
<div class="b-detail-info">
<h2 class="b-h2"><?= $arResult['NAME']; ?></h2><?if($arResult["EXTERNAL_ID"]){?>
<div class="b-detail-article">Арт. <?=$arResult["EXTERNAL_ID"]?></div><?}?>
<div class="b-detail-price clearfix">
    <div class="b-detail-price__price">
    <? foreach ($arResult["PRICES"] as $code => $arPrice): ?>
        <? if ($arPrice["CAN_ACCESS"]): ?>
            <? if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
                <span class="b-price m-price__big"><?= $arPrice["PRINT_DISCOUNT_VALUE"] ?></span>
                <span class="b-detail-price__separator">/</span>
                <span class="b-price m-price__big m-price__old"><?= $arPrice["PRINT_VALUE"] ?></span>
            <? else: ?>
                <span class="b-price m-price__big"><?= $arPrice["PRINT_VALUE"] ?></span>
            <? endif; ?>
        <? endif; ?> 
    <? endforeach; ?> 
    </div>
    <div class="b-detail-price__buy">
        <?if($arResult['IN_BASKET'] != 'Y'){?>
        <button class="b-button buy_" data-id="<?= $arResult['ID'] ?>"><span class="b-catalog-list_item__cart">Купить</span></button>
     <?} else {?>
    <button class="b-button buy_ m-in_basket"><span class="b-catalog-list_item__cart">добавлен<br>в корзину</span></button>
    <?}?>
    </div>
</div>
<div class="b-detail-where clearfix">
    <div class="b-detail-where__left">
        <span class="b-detail-where__text">Наличие в магазинах:</span> 
        
        <?
        
        if(!is_array($arResult['PROPERTIES']['SHOP']["VALUE_XML_ID"]))
            $arResult['PROPERTIES']['SHOP']["VALUE_XML_ID"] = (array) $arResult['PROPERTIES']['SHOP']["VALUE_XML_ID"];
        
        foreach($arResult['PROPERTIES']['SHOP']["VALUE_XML_ID"] as $k=>$shop){?>
            <span  class="b-where__icon <?=$shop;?>"></span>
        <?}?>
    </div> 
</div>
<? if ($arResult['DETAIL_TEXT']) { ?><div class="b-detail__text"><?= $arResult['DETAIL_TEXT']; ?></div><? } ?>
</div>
</div>
<div class="b-detail-content"><?if($arResult["DISPLAY_PROPERTIES"]){?>
<h3 class="b-h3">Основные технические характеристики</h3>
<hr class="b-hr">
<table class="b-table m-features-table">
<tbody> 
    <? foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty): ?> 
        <tr>
            <td width="40%"><?= $arProperty["NAME"] ?></td>
            <td><?
    if (is_array($arProperty["DISPLAY_VALUE"])):
        echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
    elseif ($pid == "MANUAL"):
            ?><a href="<?= $arProperty["VALUE"] ?>"><?= GetMessage("CATALOG_DOWNLOAD") ?></a><?
        else:
            echo $arProperty["DISPLAY_VALUE"];
            ?>
      <? endif ?></td>
        </tr> 
    <? endforeach ?>
</tbody></table>
<?}?>