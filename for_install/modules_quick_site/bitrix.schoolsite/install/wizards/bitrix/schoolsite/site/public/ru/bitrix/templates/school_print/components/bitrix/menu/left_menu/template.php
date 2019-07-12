<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<?
$levelSelected = array();
$prevLevel = 0;
?>
<?
foreach($arResult as $arItem):
    if($arItem["DEPTH_LEVEL"] > 2)
        continue;
    if (!($arItem['DEPTH_LEVEL'] == 1 || $levelSelected[$arItem['DEPTH_LEVEL'] - 1] == true)) continue;

    if ($prevLevel < $arItem["DEPTH_LEVEL"]) {
        echo '<ul class="' . ($prevLevel == 0 ? 'blockInner' : 'secondLevel') . '">';
    } elseif($prevLevel > $arItem["DEPTH_LEVEL"]) {
        echo str_repeat('</ul></li>', $prevLevel - $arItem["DEPTH_LEVEL"]);
    } else {
        echo '</li>';
    }

    $prevLevel = $arItem["DEPTH_LEVEL"];

    $levelSelected[$arItem['DEPTH_LEVEL']] = $arItem['SELECTED'] == true;

?>
    <?if($arItem["SELECTED"]):?>
        <li class="current<?if($arItem["PERMISSION"] <= "D"):?> lock<?endif?>">
            <?if ($arItem['DEPTH_LEVEL'] == 1): ?>
                <div class="c tl"></div>
                <div class="c tr"></div>
                <div class="c bl"></div>
                <div class="c br"></div>
            <?endif; ?>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <span class="lock"><?=$arItem["TEXT"]?></span>
            <?endif?>
    <?else:?>
        <li<?if($arItem["PERMISSION"] <= "D"):?> class="lock"<?endif?>>
            <?if ($arItem['DEPTH_LEVEL'] == 1): ?>
                <div class="c tl"></div>
                <div class="c tr"></div>
                <div class="c bl"></div>
                <div class="c br"></div>
            <?endif; ?>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <span class="lock"><?=$arItem["TEXT"]?></span>
            <?endif?>
    <?endif?>
<?endforeach?>
<?=str_repeat('</ul></li>', $prevLevel - $arItem["DEPTH_LEVEL"]); ?>
</ul>
<?endif?>
