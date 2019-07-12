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
        echo '<ul class="' . ($prevLevel == 0 ? 'sideNav' : 'secondLevel') . '">';
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
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <a class="lock"><?=$arItem["TEXT"]?></a>
            <?endif?>
    <?else:?>
        <li<?if($arItem["PERMISSION"] <= "D"):?> class="lock"<?endif?>>
            <?if($arItem["PERMISSION"] > "D"):?>
             <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
            <?else:?>
             <a class="lock"><?=$arItem["TEXT"]?></a>
            <?endif?>
    <?endif?>
<?endforeach?>
</li>
<?=str_repeat('</ul></li>', $prevLevel - 1); ?>
</ul>
<?endif?>
