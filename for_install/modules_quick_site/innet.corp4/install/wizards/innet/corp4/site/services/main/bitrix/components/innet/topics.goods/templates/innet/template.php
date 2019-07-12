<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="blocks3">
    <?foreach ($arResult as $arItem){?>
        <a href="<?=$arItem['SECTION_PAGE_URL']?>" class="in-row-mid">
            <div class="hid"><img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt=""></div>
            <div class="col2">
                <div><?=$arItem['NAME']?></div>
                <p><?=$arItem['UF_DESCRIPTION_INDEX']?></p>
            </div>
        </a>
    <?}?>
</div>