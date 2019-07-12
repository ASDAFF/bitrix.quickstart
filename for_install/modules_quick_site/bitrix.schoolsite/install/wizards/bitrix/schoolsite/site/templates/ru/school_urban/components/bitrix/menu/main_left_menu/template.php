<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<?
$levelSelected = array();
$prevLevel = 0;
?>
<ul class="sideNav">
<?
foreach($arResult as $arItem):
    if($arItem["DEPTH_LEVEL"] > 1)
        continue;
?>
    <?if($arItem["SELECTED"]):?>
        <li class="current<?if($arItem["PERMISSION"] <= "D"):?> lock<?endif?>">
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <a class="lock"><?=$arItem["TEXT"]?></a>
            <?endif?>
        </li>
    <?else:?>
        <li<?if($arItem["PERMISSION"] <= "D"):?> class="lock"<?endif?>>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <a class="lock"><?=$arItem["TEXT"]?></a>
            <?endif?>
        </li>
    <?endif?>
<?endforeach?>
</ul>
<?endif?>
