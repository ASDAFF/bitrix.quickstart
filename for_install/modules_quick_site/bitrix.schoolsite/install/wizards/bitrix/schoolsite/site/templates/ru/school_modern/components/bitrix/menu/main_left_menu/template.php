<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<?
$levelSelected = array();
$prevLevel = 0;
?>
<ul class="blockInner">
<?
foreach($arResult as $arItem):
    if($arItem["DEPTH_LEVEL"] > 1)
        continue;
?>
    <?if($arItem["SELECTED"]):?>
        <li class="current<?if($arItem["PERMISSION"] <= "D"):?> lock<?endif?>">
            <div class="c tl"></div>
            <div class="c tr"></div>
            <div class="c bl"></div>
            <div class="c br"></div>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <span class="lock"><?=$arItem["TEXT"]?></span>
            <?endif?>
        </li>
    <?else:?>
        <li<?if($arItem["PERMISSION"] <= "D"):?> class="lock"<?endif?>>
            <div class="c tl"></div>
            <div class="c tr"></div>
            <div class="c bl"></div>
            <div class="c br"></div>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <span class="lock"><?=$arItem["TEXT"]?></span>
            <?endif?>
        </li>
    <?endif?>
<?endforeach?>
</ul>
<?endif?>
