<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['ITEMS'])){?>
    <div class="comments">
        <?foreach ($arResult['ITEMS'] as $arItem){?>
            <div>
                <div class="lvl1">
                    <span><?=$arItem['PROPERTIES']['NAME_CLIENT']['VALUE']?></span>
                    <?=$arItem['DISPLAY_ACTIVE_FROM']?>
                </div>
                <p><?=$arItem['PROPERTIES']['COMMENT']['VALUE']['TEXT']?></p>
            </div>
        <?}?>

        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <br /><?=$arResult["NAV_STRING"]?>
        <?endif;?>
    </div>
<?}?>