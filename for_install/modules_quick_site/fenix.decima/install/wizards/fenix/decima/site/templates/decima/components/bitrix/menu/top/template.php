<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); $this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
    <ul class="nav navbar-nav">
    <?
        $previousLevel = 0;
        foreach($arResult as $arItem):?>
        <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
            <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
            <?endif?>
        <?if ($arItem["IS_PARENT"]):?>
            <li class="dropdown <?if ($arItem["SELECTED"]):?>active<?endif?>">
            <a href="<?=$arItem["LINK"]?>" class="dropdown-toggle" data-toggle="dropdown"><?=$arItem["TEXT"]?><?if($arItem["DEPTH_LEVEL"]=='1'):?> <b class="caret"></b><?endif?></a>
            <ul class="dropdown-menu">
                <?else:?>
                <li<?if ($arItem["SELECTED"]):?> class="active"<?endif?>><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
           <?endif?>
            <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
            <?endforeach?>
        <?if ($previousLevel > 1)://close last item tags?>
            <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
            <?endif?>
    </ul>
<?endif?>