<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//print_r($arResult);?>
<div id="slides">
    <ul class="navigation">
    <?foreach($arResult["ITEMS"] as $counter => $arElement){
        $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));?>
        <li id="<?=$this->GetEditAreaId($arElement['ID']);?>"><a href="#slide-<?=$counter?>" class="png_bg"><span><?=$arElement["NAME"]?></span></a></li>
    <?}?>
    </ul>

<?foreach($arResult["ITEMS"] as $counter => $arElement){?>
    <div id="slide-<?=$counter?>" class="noscript">
        <?if(is_array($arElement["DETAIL_PICTURE"])):?>
        <img src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
        <?endif?>
        <?if (!empty($arElement["OFFERS"])) {?>
        <div class="products">
            <ul>
            <?foreach($arElement["OFFERS"] as $arOffer){?>
                <li itemscope itemtype="http://schema.org/Product">
                    <a href="<?=$arOffer["DETAIL_PAGE_URL"]?>"><span itemprop="name"><?=$arOffer["NAME"]?></span></a>
                    <span class="price" itemprop="offerDetails" itemscope itemtype="http://schema.org/Offer">
                        <span itemprop="price"><?=number_format($arOffer["PRICE"], 0, '.', ' ')?></span> <span class="rub"><?=GetMessage("RUB")?></span>
                        <meta itemprop="currency" content="<?=$arOffer["CURRENCY"]?>" />
                    </span>
                </li>
            <?}?>
            </ul>
        </div>
        <?}?>
    </div>
<?}?>
</div><!-- #slides -->