<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="navigation">
    <ul id="footer-nav" class="menu">
<?$previousLevel = 0;
foreach($arResult as $arItem):?>
    <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
        <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
    <?endif?>
    <?if ($arItem["IS_PARENT"]):?>
        <?if ($arItem["DEPTH_LEVEL"] == 1):?>
            <li class="parent"><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                <ul class="sub-menu">
        <?else:?>
            <li class="parent"><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                <ul class="sub-menu">
        <?endif?>
    <?else:?>
        <?if ($arItem["PERMISSION"] > "D"):?>
            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                <li><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
            <?else:?>
                <li><a<?if ($arItem["SELECTED"]):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
            <?endif?>
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