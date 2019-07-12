<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
    <div class="menu">
        <ul>
            <li class="categories-link"><a href="<?=SITE_DIR?>catalog/"><?=GetMessage("ALL")?></a></li>

            <?$previousLevel = 0;
            foreach($arResult as $arItem):?>
            <? if(!empty($arItem["PARAMS"]["DEPTH_LEVEL"])): ?>
                <? $arItem["DEPTH_LEVEL"] = $arItem["PARAMS"]["DEPTH_LEVEL"]; ?>
            <? endif; ?>
            <? if(!empty($arItem["PARAMS"]["IS_PARENT"])): ?>
                <? $arItem["IS_PARENT"] = $arItem["PARAMS"]["IS_PARENT"]; ?>
            <? endif; ?>
            <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
            <?endif?>
            <?if ($arItem["IS_PARENT"]):?>
            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
            <li<?if ($arItem["SELECTED"] || $arItem["PARAMS"]["SELECTED"]):?> class="active"<?endif?>><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                <ul class="sub-menu">
                    <?endif?>
                    <?else:?>
                        <?if ($arItem["PERMISSION"] > "D"):?>
                            <li>
                                <a<?if ($arItem["SELECTED"] || $arItem["PARAMS"]["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                                <? if(!empty($arItem["PARAMS"]["CNT"])):?>&nbsp;(<?=$arItem["PARAMS"]["CNT"]?>)<? endif; ?>
                            </li>
                        <?else:?>
                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                <li><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                            <?else:?>
                                <li><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                            <?endif?>
                        <?endif?>
                    <?endif?>
                    <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
                    <?endforeach?>
                    <?if ($previousLevel > 1)://close last item tags?>
                        <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
                    <?endif?>
                </ul>
    </div>
<?endif?>