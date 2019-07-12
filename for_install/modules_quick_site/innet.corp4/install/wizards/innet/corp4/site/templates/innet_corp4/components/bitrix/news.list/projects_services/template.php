<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="owl-carousel">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 290, "height" => 160), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
        <div>
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="hid"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>"></a>
            <div class="title3"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div>
            <p><?=TruncateText($arItem['PREVIEW_TEXT'], 150)?></p>
        </div>
    <?}?>
</div>