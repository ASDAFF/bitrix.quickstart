<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['ITEMS'])){?>
    <div class="blocks2">
        <?foreach ($arResult['ITEMS'] as $arItem){?>
            <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 290, "height" => 160), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                <img src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>">
                <div class="title3"><?=$arItem['NAME']?></div>
                <p><?=TruncateText($arItem['PREVIEW_TEXT'], 150)?></p>
            </a>
        <?}?>
    </div>
<?}?>