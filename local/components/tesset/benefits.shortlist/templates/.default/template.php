<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="whyBy">
    <p class="carouselHead">ПОЧЕМУ ПОКУПАЮТ В «ТЕХНОМИРЕ»?</p>
    <ul id="myCarousel"  class="jcarousel-skin-present">
        <?foreach ($arResult["ITEMS"] as $id => $item) : ?>
        <?
        $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <li>
            <div class="carousItem" id="<?=$this->GetEditAreaId($item['ID']);?>">
                <div class="carousPhoto carous">
                    <img src="<?=$item["PREVIEW_PICTURE"]?>"/>
                    <div>
                        <p><?=$item["PREVIEW_TEXT"]?></p>
                        <!-- <p><a href="#"><br />Подробнее ></a></p> -->
                    </div>
                </div>
                <p class="carousItemTitle"><?=$item["NAME"]?></p>
            </div> 
        </li>
        <?endforeach;?>
    </ul>
</div><!--whyBy-->