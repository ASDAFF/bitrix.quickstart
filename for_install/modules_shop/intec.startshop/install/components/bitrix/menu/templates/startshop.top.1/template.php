<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?if (!empty($arResult)):?>
    <div class="startshop-menu top-1 startshop-tabs">
        <div class="tabs">
            <?foreach($arResult as $arItem):?>
                <?if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) continue;?>
                <?if($arItem["SELECTED"]):?>
                    <div class="tab ui-state-active"><a href="<?=$arItem["LINK"]?>" class="selected"><?=$arItem["TEXT"]?></a></div>
                <?else:?>
                    <div class="tab"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
                <?endif?>
            <?endforeach?>
        </div>
    </div>
<?endif?>