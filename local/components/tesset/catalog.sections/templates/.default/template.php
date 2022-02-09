<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="container">
    <ul class="subPage subBold <?=$arParams["TOP_STYLE"]?>">
        <li id="subPage1" class="subPageActive">ВЫБОР ПО ВИДАМ ТЕХНИКИ <img src="/images/catArrActive.jpg"/></li>
        <li id="subpage2" class="subPageM">ВЫБОР ПО МАРКАМ ПРОИЗВОДИТЕЛЕЙ <img src="/images/catArr.png"/></li>
    </ul>
</div><!--container-->

<div class="pageBody" id="subPage1Body">
    <div class="container <?=$arParams["BODY_STYLE"]?>">
        <ul id="breadcrumbs-one">
            <li class="first"><a href="">Главная</a></li>
            <li><a href="">Каталог</a></li>
            <li><a href="">Автобетоносмесители</a></li>
            <li><a href="" class="current">Автобетоносмеситель, модель ZXCCVB 39089</a></li>
        </ul>
        <div class="mainGrid">
            <!-- ITEMS -->
            <?if ($arResult["OPTIONS"]["SHOW_ITEMS"]) : ?>
                <?if ($arResult["OPTIONS"]["SHOW_ITEMS_II_LEVEL"]) : ?>
                    <?foreach ($arResult["SECTIONS"]["I_LEVEL"] as $id => $section) : ?>
                        <p class="catHead"><?=$section["NAME"]?></p>
                        <div class="mainGrid">
                            <?foreach ($arResult["ITEMS"][$section["ID"]] as $itemID => $item) : ?>
                            <?
                            $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
                            $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                            ?>
                                <div class="catGoodItem" id="<?=$this->GetEditAreaId($item['ID']);?>">
                                    <div class="presentPhoto <?=($item["DISCOUNT"]) ? 'saleFoto' : ''?>">
                                        <a href="<?=$item["DETAIL_PAGE_URL"]?>"><img src="<?=$item["PICTURE"]?>"/></a>
                                        <?if ($item["DISCOUNT"]) : ?>
                                            <img class="saleLable"src="/images/SaleLable.png"/>
                                        <?endif;?>
                                    </div>
                                    <p><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></p>
                                    <div class="cutPrise sale">
                                        <?if ($item["PRICE_OLD"]) : ?>
                                            <p class="lastPrice"><?=$item["PRICE_OLD"]?></p>
                                            <p class="presentNominal lastNominal"><span>руб.</span></p><br />
                                        <?endif?>
                                        <?if ($item["DISCOUNT"]) : ?>
                                            <img src="/images/priceSaleLable.png"/>
                                        <?else : ?>
                                            <img src="/images/priceLable.png"/>
                                        <?endif;?>
                                        <p class="curPrice "><?=$item["PRICE_NEW"]?></p>
                                        <p class="presentNominal"><span>руб.</span></p>
                                        <?if ($item["DISCOUNT_END_DATE"]) : ?>
                                            <div class="saleDate">до<span>&nbsp;<?=$item["DISCOUNT_END_DATE"]?></span></div>
                                        <?endif;?>
                                    </div>
                                </div>
                            <?endforeach;?>
                        </div>
                    <?endforeach;?>         
                <?else : ?>
                    <?foreach ($arResult["ITEMS"] as $itemID => $item) : ?>
                    <?
                    $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                            <div class="catGoodItem" id="<?=$this->GetEditAreaId($item['ID']);?>">
                                <div class="presentPhoto <?=($item["DISCOUNT"]) ? 'saleFoto' : ''?>">
                                    <a href="<?=$item["DETAIL_PAGE_URL"]?>"><img src="<?=$item["PICTURE"]?>"/></a>
                                    <?if ($item["DISCOUNT"]) : ?>
                                        <img class="saleLable"src="/images/SaleLable.png"/>
                                    <?endif;?>
                                </div>
                                <p><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></p>
                                <div class="cutPrise sale">
                                    <?if ($item["PRICE_OLD"]) : ?>
                                        <p class="lastPrice"><?=$item["PRICE_OLD"]?></p>
                                        <p class="presentNominal lastNominal"><span>руб.</span></p><br />
                                    <?endif?>
                                    <?if ($item["DISCOUNT"]) : ?>
                                        <img src="/images/priceSaleLable.png"/>
                                    <?else : ?>
                                        <img src="/images/priceLable.png"/>
                                    <?endif;?>
                                    <p class="curPrice "><?=$item["PRICE_NEW"]?></p>
                                    <p class="presentNominal"><span>руб.</span></p>
                                    <?if ($item["DISCOUNT_END_DATE"]) : ?>
                                        <div class="saleDate">до<span>&nbsp;<?=$item["DISCOUNT_END_DATE"]?></span></div>
                                    <?endif;?>
                                </div>
                            </div>
                    <?endforeach;?>
                <?endif;?>
            <!--/ ITEMS -->
            <?else : ?>
                <?foreach ($arResult["SECTIONS"]["I_LEVEL"] as $id => $section) : ?>
                    <div class="goodItem withHide" id="<?=$this->GetEditAreaId($item['ID']);?>">
                        <div class="visible">
                            <div class="goodImg">
                                <a href="<?=$section["SECTION_PAGE_URL"]?>"><img src="<?=$section["PICTURE"]?>"/></a>
                            </div>
                            <div><p><?=$section["NAME"]?></p></div>
                        </div>
                        <?if ($arResult["SECTIONS"]["II_LEVEL"][$id]) : ?>
                            <div class="hidden">
                                <hr class="hiddenHR"/>
                                <?foreach ($arResult["SECTIONS"]["II_LEVEL"][$id] as $iisection) : ?>                    
                                    <p><a href="<?=$iisection["SECTION_PAGE_URL"]?>"><?=$iisection["NAME"]?></a></p>
                                    <ul>
                                        <?foreach ($arResult["SECTIONS"]["III_LEVEL"][$iisection["ID"]] as $iiisection) : ?> 
                                            <li><a href="<?=$iiisection["SECTION_PAGE_URL"]?>"><?=$iiisection["NAME"]?></a></li>
                                        <?endforeach;?>
                                    </ul>
                                <?endforeach;?>
                            </div>
                        <?endif;?>
                    </div>
                <?endforeach;?>
            <?endif;?>
        <div class="clear"></div>
        </div><!--mainGrid-->
    </div><!--container-->

    <?$APPLICATION->IncludeComponent(
        "tesset:tehnika.banner",
        "",
        Array(
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => "4",
            "ID" => array(),
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600"
        ),
    false
    );?>
</div><!--pageBody-->

<div class="pageBody" id="subPage2Body">
</div>