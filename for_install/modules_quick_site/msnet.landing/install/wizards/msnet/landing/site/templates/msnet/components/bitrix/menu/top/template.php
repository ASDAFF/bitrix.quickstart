<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
if (!empty($arResult)): ?>
    <ul class="header__menu header-menu">
        <? foreach ($arResult as $arItem): ?>
            <li class="header-menu__item">
                <a href="#<?= $arItem["PARAMS"]["ANCHOR"] ?>" class="header-menu__link js-scroll-to-element"><?= $arItem["TEXT"] ?></a>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>
