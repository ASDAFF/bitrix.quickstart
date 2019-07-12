<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?function childLevelTop($item){?>
    <?
    $arrow = '';
    $toogle_block_title2 = '';
    if (!empty($item['PARAMS']['SUB_SECTION']) || !empty($item['PARAMS']['ELEMENTS'])){
        $arrow = 'class="arrow"';
        $toogle_block_title2 = 'toogle-block-title2';
    }
    ?>

    <li <?=$arrow?>><a class="<?=$toogle_block_title2?>" href="<?=$item['LINK']?>"><?=$item['TEXT']?><span></span></a>
        <?if (!empty($item['PARAMS'])){?>
            <?if (!empty($item['PARAMS']['SUB_SECTION']) || !empty($item['SUB_SECTION'])){?>
                <ul class="toogle-block2">
                    <?foreach ($item['PARAMS']['SUB_SECTION'] as $levelChild){?>
                        <?
                        $arrow = '';
                        $toogle_block_title2 = '';
                        if (!empty($levelChild['SUB_SECTION']) || !empty($levelChild['ELEMENTS'])){
                            $arrow = 'class="arrow"';
                            $toogle_block_title2 = 'toogle-block-title2';
                        }
                        ?>
                        <li <?=$arrow?>>
                            <a class="<?=$toogle_block_title2?>" href="<?=$levelChild['SECTION_PAGE_URL']?>"><?=$levelChild['TEXT']?><span></span></a>

                            <?if (!empty($levelChild['ELEMENTS']) || !empty($levelChild['SUB_SECTION'])){?>
                                <ul class="toogle-main2">
                                    <?if (!empty($levelChild['ELEMENTS'])){?>
                                        <?foreach ($levelChild['ELEMENTS'] as $link){?>
                                            <li><a href="<?=$link['LINK']?>"><?=$link['TEXT']?></a></li>
                                        <?}?>
                                    <?}?>

                                    <?if (!empty($levelChild['SUB_SECTION'])){?>
                                        <?foreach ($levelChild['SUB_SECTION'] as $link){?>
                                            <?=childLevelTop($link);?>
                                        <?}?>
                                    <?}?>
                                </ul>
                            <?}?>
                        </li>
                    <?}?>
                </ul>
            <?}?>

            <?if (!empty($item['PARAMS']['ELEMENTS'])){?>
                <ul class="toogle-block2">
                    <?foreach ($item['PARAMS']['ELEMENTS'] as $levelChild){?>
                        <li><a href="<?=$levelChild['LINK']?>"><?=$levelChild['TEXT']?></a></li>
                    <?}?>
                </ul>
            <?}?>
        <?}?>
    </li>
<?}?>


<a class="mob-nav-btn toogle-block-title" data-fixed="fixed2"><?=GetMessage('INNET_MENU_HEADER')?></a>

<div class="col1 fll toogle-block">
    <ul>
        <?foreach($arResult as $level_1) {?>
            <?
            $active_menu = '';
            if($level_1['SELECTED'])
                $active_menu = 'active';

            $arrow = '';
            $toogle_block_title2 = '';
            if (!empty($level_1['PARAMS'])){
                $arrow = 'class="arrow"';
                $toogle_block_title2 = 'toogle-block-title2';
            }
            ?>
            <li <?=$arrow?>>
                <a class="<?=$toogle_block_title2?> <?=$active_menu?>" href="<?=$level_1['LINK']?>"><?=$level_1['TEXT']?><span></span></a>
                <?if (!empty($level_1['PARAMS'])){?>
                    <ul class="toogle-block2">
                        <?foreach ($level_1['PARAMS'] as $levelChild){?>
                            <?=childLevelTop($levelChild);?>
                        <?}?>
                    </ul>
                <?}?>
            </li>
        <?}?>
    </ul>
</div>
