<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>

<?if (!empty($arResult)):?>
<div class="menu_cols">
    <div class="menu_cols__title"><?=$arParams['BLOCK_TITLE']?></div>
    <ul class="menu_cols__menu <?if($arParams['ELLIPSIS_NAMES']=='Y'):?> ellipsis<?endif;?> row">

    <?
    
    $arParams['LVL1_COUNT'] = intval($arParams['LVL1_COUNT']);
    $arParams['LVL2_COUNT'] = intval($arParams['LVL2_COUNT']);
    $iLvl1Count = $iLvl2Count = 0;
    $previousLevel = 0;
    foreach($arResult as $arItem):
    ?>

        <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
            <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
        <?endif?>

        <?
        if ($arItem['DEPTH_LEVEL'] > $arParams['MAX_LEVEL']) {
            continue;
        }
        ?>
        <?if ($arItem["IS_PARENT"]):?>

            <?
            if ($arItem["DEPTH_LEVEL"] == 1):
                $iLvl1Count++;
                if ($iLvl1Count > $arParams['LVL1_COUNT']) {
                    break;
                }
                $iLvl2Count = 0;
            ?>
                <li class="col-lg-4"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a>
                <?php if ($arItem['DEPTH_LEVEL'] < $arParams['MAX_LEVEL']): ?>
                    <ul>
                <?php endif; ?>
            <?php
            else:
            
                if ($arItem["DEPTH_LEVEL"] >= 2) {
                    if ($arItem["DEPTH_LEVEL"] == 2) {
                        $iLvl2Count++;
                    }
                    if ($iLvl2Count > $arParams['LVL2_COUNT']) {
                        continue;
                    }
                }
            ?>
                <li<?if ($arItem["SELECTED"]):?> class="item-selected"<?endif?>><a href="<?=$arItem["LINK"]?>" class="parent"><?=$arItem["TEXT"]?></a>
                <?php if ($arItem['DEPTH_LEVEL'] < $arParams['MAX_LEVEL']): ?>
                    <ul>
                <?php endif; ?>
            <?endif?>

        <?else:?>

            <?if ($arItem["PERMISSION"] > "D"):?>

                <?
                if ($arItem["DEPTH_LEVEL"] == 1):
                    $iLvl1Count++;
                    if ($iLvl1Count > $arParams['LVL1_COUNT']) {
                        break;
                    }
                    $iLvl2Count = 0;
                ?>
                    <li class="col-lg-4"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a></li>
                <?php
                else:
                    if ($arItem["DEPTH_LEVEL"] >= 2) {
                        if ($arItem["DEPTH_LEVEL"] == 2) {
                            $iLvl2Count++;
                        }
                        if ($iLvl2Count > $arParams['LVL2_COUNT']) {
                            continue;
                        }
                    }
                ?>
                    <li<?if ($arItem["SELECTED"]):?> class="item-selected"<?endif?>><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
                <?endif?>

            <?else:?>

                <?
                if ($arItem["DEPTH_LEVEL"] == 1):
                    $iLvl1Count++;
                    if ($iLvl1Count > $arParams['LVL1_COUNT']) {
                        break;
                    }
                    $iLvl2Count = 0;
                ?>
                    <li class="col-lg-4"><a href="" class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                <?php
                else:
                    if ($arItem["DEPTH_LEVEL"] >= 2) {
                        if ($arItem["DEPTH_LEVEL"] == 2) {
                            $iLvl2Count++;
                        }
                        if ($iLvl2Count > $arParams['LVL2_COUNT']) {
                            continue;
                        }
                    }
                ?>
                    <li><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
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