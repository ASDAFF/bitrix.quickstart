<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="row">
    <ul class="reviews_list">

        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <li class="reviews_item">
                <h4><?= $arItem["NAME"] ?></h4>
                <div class="reviews_text">
                    <? echo $arItem["PREVIEW_TEXT"]; ?>
                </div>
                <? if ($arItem["DETAIL_TEXT"]): ?>
                    <ul class="reviews_item_parent">
                        <li class="answer">
                            <h4>МедПлюсТест</h4>
                            <div class="answer_text">
                                <? echo $arItem["DETAIL_TEXT"]; ?>
                            </div>
                        </li>
                    </ul>
                <? endif ?>
            </li>
        <? endforeach; ?>
    </ul>

    <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?>
    <? endif; ?>
</div>
