<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['ITEMS'])){?>
    <div class="blocks1">
        <div class="inner in-row">
            <?foreach ($arResult['ITEMS'] as $arItem){?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="column">
                    <div>
                        <div class="title3"><?=$arItem['NAME']?></div>
                        <p><?=TruncateText($arItem['PREVIEW_TEXT'], 150)?></p>
                    </div>
                </a>
            <?}?>
        </div>
    </div>
<?}?>