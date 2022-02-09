<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>\
 <div class="presentGood">
    <div class="container">
        <div class="presentHeader">
            <img src="/images/presentLable.png"/>
            <p>СПЕЦТЕХНИКА В НАЛИЧИИ</p>
        </div>
        <div class="presentGoods">
        <?foreach ($arResult["ITEMS"] as $id => $item) : ?>
        <?
        $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
            <div class="presentGoodItem"  id="<?=$this->GetEditAreaId($item['ID']);?>">
                <div class="presentPhoto saleFoto">
                    <a href="<?=$item["DETAIL_PAGE_URL"]?>"><img src="<?=$item["PICTURE"]?>"/></a>
                    <?if ($item["DISCOUNT"]) :?>
                        <img class="saleLable" src="/images/SaleLable.png"/>
                    <?endif;?>
                </div>
                <p><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></p>
                <div class="presentPrise <?=($item["DISCOUNT"]) ? 'sale' : '' ?>">
                    <?if ($item["DISCOUNT"]) :?>
                        <img src="/images/priceSaleLable.png"/>
                    <?else : ?>
                        <img src="/images/priceLable.png"/>
                    <?endif;?>
                    <p><?=$item["PRICE_NEW"]?></p>
                    <p class="presentNominal"><span>руб.</span></p>
                    <div class="saleDate">до<span>&nbsp;<?=$item["DESCOUNT_END_DATE"]?></span></div>
                </div>
            </div>
        <?endforeach;?>    
        </div>
    </div><!--container-->
</div><!--presentGood-->