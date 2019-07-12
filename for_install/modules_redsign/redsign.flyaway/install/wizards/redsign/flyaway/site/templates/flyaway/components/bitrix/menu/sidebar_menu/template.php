<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if (!empty($arResult)) {
    $this->SetViewTarget('sidebar_menu');
    ?><ul class="nav-side nav nav-list"><?
            $previousLevel = 0;
            $index = 0;
            foreach ($arResult as $key => $arItem) {
                    if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
                            echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
                    }
                    if ($arItem['IS_PARENT']==1) {
                            ?><li class="nav-side__item<?if($arItem['SELECTED']=='Y'):?> active showed<?endif;?> level<?print_r($arItem['DEPTH_LEVEL'])?>"><?
                                    ?><a class="nav-side__label element" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?><i class="nav-side__icon collapsed" href="#collapse<?=$index?>" data-toggle="collapse"></i></a><?
                                    ?><ul class="nav-side__submenu nav-side__lvl2 lvl2 collapse" id="collapse<?=$index?>"><?
                    } else {
                            ?><li class="nav-side__item <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?> level<?print_r($arItem['DEPTH_LEVEL'])?>"><a class="nav-side__label element" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?
                    }
                    $previousLevel = $arItem["DEPTH_LEVEL"];
                    $index++;
            }
            if ($previousLevel > 1) {
                    echo str_repeat("</li></ul>", ($previousLevel-1) );
            }
    ?></ul><?
    $this->EndViewTarget();
    ?><ul class="nav-side nav nav-list"><?
            $previousLevel = 0;
            $index = 0;
            foreach ($arResult as $key => $arItem) {
                    if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
                            echo str_repeat("</li></ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
                    }
                    if ($arItem['IS_PARENT']==1) {
                            ?><li class="nav-side__item<?if($arItem['SELECTED']=='Y'):?> active showed<?endif;?> level<?print_r($arItem['DEPTH_LEVEL'])?>"><?
                                    ?><a class="nav-side__label element" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?><i class="nav-side__icon collapsed" href="#collapse<?=$index?>_pc" data-toggle="collapse"></i></a><?
                                    ?><ul class="nav-side__submenu nav-side__lvl2 lvl2 collapse" id="collapse<?=$index?>_pc"><?
                    } else {
                            ?><li class="nav-side__item <?if ($arItem['SELECTED']=='Y'):?>active<?endif;?> level<?print_r($arItem['DEPTH_LEVEL'])?>"><a class="nav-side__label element" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a><?
                    }
                    $previousLevel = $arItem["DEPTH_LEVEL"];
                    $index++;
            }
            if ($previousLevel > 1) {
                    echo str_repeat("</li></ul>", ($previousLevel-1) );
            }
    ?></ul><?
}
