<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?
$margin_left = 7;
$cnt_elements = ($arParams['COUNT_ELEMENTS'] == 'Y') ? true : false;
?>

<?function childLevelLeft($item, $margin_left, $cnt_elements){?>
    <li>
        <a href="<?=$item['SECTION_PAGE_URL']?>"><?=$item['NAME']?> <?if ($cnt_elements){?>(<?=$item['ELEMENT_CNT']?>)<?}?></a>
        <?/*if (!empty($item['ELEMENTS'])){?>
            <ul style="margin-left: <?echo $margin_left * $item['DEPTH_LEVEL']?>px">
                <?foreach ($item['ELEMENTS'] as $link){?>
                    <li><a href="<?=$link['DETAIL_PAGE_URL']?>">- <?=$link['NAME']?></a></li>
                <?}?>
            </ul>
        <?}*/?>
        <?if (!empty($item['SUB_SECTION'])){?>
            <ul style="margin-left: <?echo $margin_left * $item['DEPTH_LEVEL']?>px">
                <?foreach ($item['SUB_SECTION'] as $link){?>
                    <?=childLevelLeft($link, $margin_left, $cnt_elements);?>
                <?}?>
            </ul>
        <?}?>
    </li>
<?}?>

<div class="blocks6">
    <?foreach($arResult['INNET_SECTIONS']['SUB_SECTION'] as $level_1){?>
        <?
        $pic = '';
        if (!empty($level_1['DETAIL_PICTURE'])){
            $pic = CFile::ResizeImageGet($level_1['DETAIL_PICTURE'], array("width" => 166, "height" => 96), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        } else if (!empty($level_1['PICTURE'])){
            $pic = CFile::ResizeImageGet($level_1['PICTURE'], array("width" => 166, "height" => 96), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        }
        ?>
        <div>
            <div class="hid"><span><a href="<?=$level_1['SECTION_PAGE_URL']?>"><img src="<?=$pic['src']?>" alt="<?=$level_1['NAME']?>"></a></span></div>
            <div class="block">
                <a href="<?=$level_1['SECTION_PAGE_URL']?>"><?=$level_1['NAME']?> <?if ($cnt_elements){?>(<?=$level_1['ELEMENT_CNT']?>)<?}?></a>
                <ul>
                    <?foreach ($level_1['SUB_SECTION'] as $level_2){?>
                        <li>
                            <a href="<?=$level_2['SECTION_PAGE_URL']?>"><?=$level_2['NAME']?> <?if ($cnt_elements){?>(<?=$level_2['ELEMENT_CNT']?>)<?}?></a>
                            <?if (!empty($level_2['SUB_SECTION'])){?>
                                <ul style="margin-left: <?echo $margin_left * $level_2['DEPTH_LEVEL']?>px">
                                    <?foreach ($level_2['SUB_SECTION'] as $level_3){?>
                                        <?=childLevelLeft($level_3, $margin_left, $cnt_elements);?>
                                    <?}?>
                                </ul>
                            <?}?>
                        </li>
                    <?}?>
                </ul>
            </div>
        </div>
    <?}?>
</div>