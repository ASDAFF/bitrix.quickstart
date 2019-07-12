<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['SECTION']['PATH'][0]['NAME'])){?>
    <h2 style="margin-bottom: 20px;"><?=$arResult['SECTION']['PATH'][0]['NAME']?></h2>
<?}?>
<?if (!empty($arResult['SECTION']['PATH'][0]['DESCRIPTION'])) {?>
    <span class="big-desc-text"><?=$arResult['SECTION']['PATH'][0]['DESCRIPTION']?></span>
<?}?>

<div class="clearfix">
    <div class="flr view-style">
        <span><?=GetMessage('INNET_SERVICES_LIST_VIEW')?>:</span>
        <a class="view-style1 active" rel="blocks8-view1"></a>
        <a class="view-style2" rel="blocks8-view2"></a>
        <a class="view-style3" rel="blocks8-view3"></a>
    </div>
</div>

<div class="blocks8 blocks8-view1">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 140, "height" => 120), BX_RESIZE_IMAGE_EXACT, true);?>
        <div>
            <div class="hid"><a href="<?=$arItem['DETAIL_PAGE_URL'];?>"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME'];?>"></a></div>
            <div class="col2">
                <div>
                    <a href="<?=$arItem['DETAIL_PAGE_URL'];?>" class="link"><?=$arItem['NAME'];?></a>
                    <p><?=TruncateText($arItem['PREVIEW_TEXT'], 150)?></p>
                </div>
                <a href="<?=$arItem['DETAIL_PAGE_URL'];?>" class="btn"><?=GetMessage('INNET_SERVICES_LIST_MORE')?></a>
            </div>
        </div>
    <?}?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="clear"></div><br /><?=$arResult["NAV_STRING"]?>
<?endif;?>