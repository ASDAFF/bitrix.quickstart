<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (count($arResult["ITEMS"]) > 0): ?>
    <? foreach ($arResult["ITEMS"] as $arSection): ?>
        <div class="section"><?= $arSection['NAME'] ?>
            <? foreach ($arSection["ELEMENTS"] as $key => $arItem): ?>
                <div class="element">
                    <?= $arItem["NAME"] ?>
                    <?= $arItem["PREVIEW_TEXT"] ?>
                </div>
            <? endforeach ?>
        </div>
    <? endforeach ?>
<? endif ?>
