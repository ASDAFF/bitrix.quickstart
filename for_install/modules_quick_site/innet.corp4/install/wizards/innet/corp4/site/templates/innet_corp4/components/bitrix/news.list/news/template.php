<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="blocks5">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 136, "height" => 97), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
        <div class="in-row">
            <div class="hid"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>"></a></div>
            <div class="col2">
                <div><?=$arItem['DISPLAY_ACTIVE_FROM']?></div>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
            </div>
        </div>
    <?}?>
</div>