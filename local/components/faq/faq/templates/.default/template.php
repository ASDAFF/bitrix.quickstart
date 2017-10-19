<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
    <div class="dropDown"><span class="expand"><?= GetMessage("100UP_EXPAND") ?></span> <span
                class="collapse"><?= GetMessage("100UP_COLLAPSE") ?></span>
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="item">
                <p class="name"><span class="arrow"></span><span class="text"><? echo $arItem["NAME"] ?></span></p>
                <p class="dropContent" style="display: none; "><strong><? echo $arItem["PREVIEW_TEXT"]; ?></strong></p>
            </div><!-- item -->
        <? endforeach; ?>
    </div>
<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <br/><?= $arResult["NAV_STRING"] ?>
<? endif; ?>