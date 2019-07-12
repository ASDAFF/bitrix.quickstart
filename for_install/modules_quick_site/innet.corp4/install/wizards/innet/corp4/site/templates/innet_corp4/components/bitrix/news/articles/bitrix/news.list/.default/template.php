<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="articles">
    <?if($arParams["DISPLAY_TOP_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?><br /><div class="clear"></div>
    <?endif;?>

    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$res_preview = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 183, "height" => 183), BX_RESIZE_IMAGE_EXACT, true);?>
        <div class="item rL hid">
            <div class="image_block rL hid"><a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><img src="<?=$res_preview['src']?>" alt=""></a></div>
            <div class="text_block rL hid">
                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=$arItem['NAME'];?></a>
                <p><?=$arItem['PREVIEW_TEXT'];?></p>
                <a class="more" href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=GetMessage('INNET_SERVICES_LIST_MORE')?></a>
            </div>
        </div>
    <?}?>

    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>