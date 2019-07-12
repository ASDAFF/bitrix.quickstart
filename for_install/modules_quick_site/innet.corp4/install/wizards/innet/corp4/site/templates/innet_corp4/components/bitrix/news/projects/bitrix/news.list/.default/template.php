<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['SECTION']['PATH'][0]['NAME'])){?>
    <h2 style="margin-bottom: 20px;"><?=$arResult['SECTION']['PATH'][0]['NAME']?></h2>
<?}?>
<?if (!empty($arResult['SECTION']['PATH'][0]['DESCRIPTION'])) {?>
    <span class="big-desc-text"><?=$arResult['SECTION']['PATH'][0]['DESCRIPTION']?></span>
<?}?>

<div class="blocks7">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 284, "height" => 154), BX_RESIZE_IMAGE_EXACT, true);?>
        <div>
            <div class="hid">
                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME'];?>"></a>
            </div>
            <a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><?=$arItem['NAME'];?></a>
            <p><?=TruncateText($arItem['PREVIEW_TEXT'], 250)?></p>
            <a href="<?=$arItem['DETAIL_PAGE_URL'];?>" class="btn"><?=GetMessage('INNET_PROJECTS_MORE')?></a>
        </div>
    <?}?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="clear"></div><br /><?=$arResult["NAV_STRING"]?>
<?endif;?>