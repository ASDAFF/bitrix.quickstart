<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$menuId = $this->getEditAreaId('dd');
?>

<?php if(!empty($arResult)): ?>

    <ul class="menu_top hml_menu clearfix">
        <?php foreach($arResult as $arMenu): ?>
            <li class="menu_top__item">
                <a href="<?=$arMenu['LINK']?>"><?=$arMenu['TEXT']?></a>
            </li>
        <?php endforeach; ?>
        <li class="menu_top__item hml_menu__more dropdown" style="display:none">
            <span class="dropdown-toggle" id="<?=$menuId;?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" role="button">
                <span class="hml_menu__icon menu_icon">
                    <span class="menu_icon__bar"></span>
                    <span class="menu_icon__bar"></span>
                    <span class="menu_icon__bar"></span>
                </span>
                <span class="hml_menu__more_icon"><?=Loc::getMessage('RS_SLINE.BM_TOPPANEL.MORE_BTN')?></span>
            </span>
            <ul class="menu_top__sub dropdown-menu" aria-labelledby="<?=$menuId;?>"></ul>
        </li>
    </ul>

<?php endif; ?>