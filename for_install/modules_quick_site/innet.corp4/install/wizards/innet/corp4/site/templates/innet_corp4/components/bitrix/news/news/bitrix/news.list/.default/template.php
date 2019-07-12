<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="news">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$res_preview = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 349, "height" => 235), BX_RESIZE_IMAGE_EXACT, true);?>
        <div class="item rL hid">
            <div class="image_block rL hid">
                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>">
                    <img src="<?=$res_preview['src'];?>" alt="<?=$arItem['NAME'];?>">
                </a>
            </div>
            <div class="text_block rL hid">
                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=$arItem['NAME'];?></a>
                <div class="data"><?=$arItem['DISPLAY_ACTIVE_FROM'];?></div>
                <p><?=$arItem['PREVIEW_TEXT'];?></p>
            </div>
        </div>
    <?}?>

    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>