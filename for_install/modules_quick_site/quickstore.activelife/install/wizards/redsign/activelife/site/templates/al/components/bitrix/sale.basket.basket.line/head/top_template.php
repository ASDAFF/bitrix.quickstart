
<?php

use Bitrix\Main\Localization\Loc;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
?><a class="clearfix" href="<?=$arParams['PATH_TO_BASKET']?>">
<?
/*
if (!$compositeStub && $arParams['SHOW_AUTHOR'] == 'Y'):?>
    <div class="bx-basket-block">
        <i class="fa fa-user"></i>
        <?if ($USER->IsAuthorized()):
            $name = trim($USER->GetFullName());
            if (! $name)
                $name = trim($USER->GetLogin());
            if (strlen($name) > 15)
                $name = substr($name, 0, 12).'...';
            ?>
            <a href="<?=$arParams['PATH_TO_PROFILE']?>"><?=htmlspecialcharsbx($name)?></a>
            &nbsp;
            <a href="?logout=yes"><?=Loc::getMessage('TSB1_LOGOUT')?></a>
        <?else:?>
            <a href="<?=$arParams['PATH_TO_REGISTER']?>?login=yes"><?=Loc::getMessage('TSB1_LOGIN')?></a>
            &nbsp;
            <a href="<?=$arParams['PATH_TO_REGISTER']?>?register=yes"><?=Loc::getMessage('TSB1_REGISTER')?></a>
        <?endif?>
    </div>
<?endif
*/
?>
    <svg class="icon icon-cart icon-svg"><use xlink:href="#svg-cart"></use></svg>
	<span class="cart_top__text">
        <span class="cart_top__title"><?=Loc::getMessage('TSB1_YOUR_CART')?></span>
        <?
        if (!$compositeStub)
        {
            ?>
            
                <?php if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'): ?>
                    <?php if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y'): ?>
                        <span class="cart_top__str">
                            <span class="hidden-xs"><?=$arResult['PRODUCT(S)']?>:</span>
                            <strong class="cart_top__num"><?=$arResult['NUM_PRODUCTS']?></strong>
                        </span>
                        
                        <?php if ($arParams['SHOW_TOTAL_PRICE'] == 'Y'):?>
                            <span class="cart_top__str hidden-xs">
                                <span><?=Loc::getMessage('TSB1_TOTAL_PRICE')?></span>
                                <?php if ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y'): ?>
                                    <strong><?=$arResult['TOTAL_PRICE']?></strong>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    <?endif; ?>
                <?php else: ?>
                    <span class="hidden-xs"><?=Loc::getMessage('RS_SLINE.BSBBL_HEAD.EMPTY_CART')?></span>
                <?php endif; ?>
            <?
        }
        /*
        if ($arParams['SHOW_PERSONAL_LINK'] == 'Y'):?>
            <br>
            <span class="icon_info"></span>
            <a href="<?=$arParams['PATH_TO_PERSONAL']?>"><?=Loc::getMessage('TSB1_PERSONAL')?></a>
        <?endif
        */?>
    </span>
</a>