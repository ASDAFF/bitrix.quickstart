<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult)) {?>
    <div class="col1 fll">
        <ul>
            <?foreach ($arResult as $arItem){?>
                <?if($arItem["SELECTED"]):?>
                    <li><a href="<?=$arItem["LINK"]?>" class="active"><?=$arItem["TEXT"]?></a></li>
                <?else:?>
                    <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
                <?endif?>
            <?}?>
        </ul>
    </div>
<?}?>