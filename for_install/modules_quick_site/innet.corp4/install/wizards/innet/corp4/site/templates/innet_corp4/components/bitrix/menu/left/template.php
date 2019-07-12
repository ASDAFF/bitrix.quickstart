<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?function childLevelLeft($item){?>
    <li>
        <a href="<?=$item['SECTION_PAGE_URL']?>" class="toogle-title3 toogle-title2"><?=$item['NAME']?><span data-action="open-panel"></span></a>

        <?if (!empty($item['ELEMENTS'])){?>
            <ul class="toogle-main3 toogle-content">
                <?foreach ($item['ELEMENTS'] as $link){?>
                    <li><a href="<?=$link['DETAIL_PAGE_URL']?>">- <?=$link['NAME']?></a></li>
                <?}?>
            </ul>
        <?}?>

        <?if (!empty($item['SUB_SECTION'])){?>
            <ul class="toogle-main3 toogle-content">
                <?foreach ($item['SUB_SECTION'] as $link){?>
                    <?=childLevelLeft($link);?>
                <?}?>
            </ul>
        <?}?>
    </li>
<?}?>

<?if (!empty($arResult)){?>
    <a class="open-toogle"><?=GetMessage('INNET_MENU_LEFT')?></a>

    <div class="border border0">
        <div class="toogle">
            <?foreach ($arResult as $level_1){?>
                <?
                $active_menu = '';
                if($level_1['SELECTED'])
                    $active_menu = 'active';
                ?>
                <div>
                    <a href="<?=$level_1['LINK']?>" class="toogle-title <?=$active_menu?>">
                        <?=$level_1['TEXT']?>

                        <?if (!empty($level_1['PARAMS'])){?>
                            <span data-action="open-panel"></span>
                        <?}?>
                    </a>
                    <?if (!empty($level_1['PARAMS'])){?>
                        <ul class="toogle-main toogle-content">
                            <?foreach ($level_1['PARAMS'] as $level_2){?>
                                <li>
                                    <a href="<?=$level_2['SECTION_PAGE_URL']?>" class="toogle-title2">
                                        <?=$level_2['NAME']?>

                                        <?if (!empty($level_2['ELEMENTS']) || !empty($level_2['SUB_SECTION'])){?>
                                            <span data-action="open-panel"></span>
                                        <?}?>
                                    </a>

                                    <?if (!empty($level_2['ELEMENTS'])){?>
                                        <ul class="toogle-main2 toogle-content">
                                            <li>
                                                <?foreach ($level_2['ELEMENTS'] as $link){?>
                                                    <a href="<?=$link['DETAIL_PAGE_URL']?>">- <?=$link['NAME']?></a><br/>
                                                <?}?>
                                            </li>
                                        </ul>
                                    <?}?>

                                    <?if (!empty($level_2['SUB_SECTION'])){?>
                                        <ul class="toogle-main2 toogle-content">
                                            <?foreach ($level_2['SUB_SECTION'] as $level_3){?>
                                                <?=childLevelLeft($level_3);?>
                                            <?}?>
                                        </ul>
                                    <?}?>
                                </li>
                            <?}?>
                        </ul>
                    <?}?>
                </div>
            <?}?>
        </div>
    </div>
<?}?>