<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="slide partners pt0">
    <div class="inner">
        <div class="owl-carousel2">
            <?foreach ($arResult['ITEMS'] as $arItem){?>
                <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 290, "height" => 160), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <div><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>"></a></div>
            <?}?>
        </div>
    </div>
</div>