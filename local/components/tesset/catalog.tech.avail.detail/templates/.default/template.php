<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/css/skinDetalno.css"/>');?>
<?
$this->AddEditAction($arResult["ITEM"]['ID'], $arResult["ITEM"]['EDIT_LINK'], CIBlock::GetArrayByID($arResult["ITEM"]["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($arResult["ITEM"]['ID'], $arResult["ITEM"]['DELETE_LINK'], CIBlock::GetArrayByID($arResult["ITEM"]["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
?>
<div class="container tehnikaDetalno" id="<?=$this->GetEditAreaId($arResult["ITEM"]['ID']);?>">
    <h1>
        <?if (!$arResult["ITEM"]["TYPE"] || !$arResult["ITEM"]["MODEL"]) : ?>
                <?=$arResult["ITEM"]["NAME"]?>
        <?else : ?>
            <?=$arResult["ITEM"]["TYPE"]?>, <span>МОДЕЛЬ <?=$arResult["ITEM"]["MODEL"]?></span>
        <?endif;?>
    <span class="print"><a href="#"><img src="/images/print.png"/> Версия для печати</a></span></h1>
    <div class="detalnoLeft">
        <div class="gallery"><a href="<?=$arResult["ITEM"]["PICTURE"]["BIG"]?>"><img src="<?=$arResult["ITEM"]["PICTURE"]["BIG"]?>" width="460px" class="detalnoMainPhoto"/></a></div>
        <img class="saleLable" src="/images/saleLableBig.png"/>
        <ul id="myCarousel"  class="jcarousel-skin-detalno gallery">
            <?foreach ($arResult["ITEM"]["PICTURES"] as $picture) : ?>
                <li><a href="<?=$picture["BIG"]?>"><img src="<?=$picture["THUMB"]?>"/></a></li>
            <?endforeach;?>
        </ul>
    </div>
    <div class="detalnoRight">
        <p><?=$arResult["ITEM"]["PREVIEW_TEXT"]?></p>
        <div class="presentPrise sale">
            <p class="lastPrice"><?=$arResult["ITEM"]["PRICE_OLD"]?></p>
            <p class="presentNominal lastNominal"><span>руб.</span></p><br />
                    <?if ($arResult["ITEM"]["DISCOUNT"]) : ?>
                        <img src="/images/priceSaleLable.png"/>
                        <p><?=$arResult["ITEM"]["PRICE_NEW"]?></p>
                        <p class="presentNominal"><span>руб.</span></p>
                        <div class="saleDate">до<span>&nbsp;<?=$arResult["ITEM"]["DISCOUNT_DATE_END"]?></span></div>
                    <?endif;?>
                </div>
        <div class="detalnoButton"><a rel="order" name="signup" href="#order"class="sliderButton">СДЕЛАТЬ ЗАКАЗ</a></div>
        <p class="orderDesc">
            По всем вопросам, связанным с наличием или покупкой техники с наработкой, обращайтесь к вашему специалисту по продажам:
            <br /><span><span>+7 (988)</span> 234-56-78</span> или <span class="email">lex@tehnomir.com</span>
        </p>
    </div>
    <div class="clear"></div>
        <ul class="subPage subPageCatalog">
            <li id="subPage1" class="subPageActive">ТЕХНИЧЕСКИЕ ХАРАКТЕРИСТИКИ <img src="/images/catArrActive.jpg"/></li>
            <li id="subpage2" class="subPageM">СХЕМА <img src="/images/catArr.png"/></li>
        </ul>
    <div class="supContainer" id="subPage1Body">
        <p><?=$arResult["ITEM"]["DETAIL_TEXT"]?></p>
        <hr class="dottedLine"/>
        <table class="detalnoTable">
            <?foreach ($arResult["PROPS"] as $type => $name) : ?>
                <tr class="darkRow">
                    <td class="firstDetalnoCol"><?=$name?></td>
                    <td><?=$arResult["ITEM"][$type]?></td>
                </tr>
            <?endforeach;?>
        </table>
    </div>
    <div class="supContainer" id="subPage2Body">
    </div>
</div>
<script>
$('document').ready(function(){
            $('#myCarousel').jcarousel({
                scroll: 1,
                visible: 3
            });
            $('.gallery a').lightBox();
    $('a[rel*=contakty]').leanModal({ top : 20, closeButton: ".modal_close" });
    $('a[rel*=order]').leanModal({ top : 20, closeButton: ".modal_close" });
    
});
</script>