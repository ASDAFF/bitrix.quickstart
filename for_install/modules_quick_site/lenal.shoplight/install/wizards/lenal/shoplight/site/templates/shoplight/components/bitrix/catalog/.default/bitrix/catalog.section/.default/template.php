<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
if (!empty($arResult['ITEMS'])) {

    if ($arParams["DISPLAY_TOP_PAGER"]) {
        ?><? echo $arResult["NAV_STRING"]; ?><?
    }

    $strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
    $strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
    $arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
    ?>
    <ul class="b-item-list">
        <?
        foreach ($arResult['ITEMS'] as $key => $arItem) {
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
            $strMainID = $this->GetEditAreaId($arItem['ID']);
            ?>
            <li class="b-item-list__item" id="<? echo $strMainID; ?>">
                <a class="b-item-list__item-link" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>">
                    <div class="b-item-list__item-frame">
                        <img class="b-item-list__item-image" src="<? echo $arItem['DETAIL_PICTURE']['SRC']; ?>" alt="<?= $arItem['NAME']; ?>" title="">
                        <div id="5208" class="b-item-list__item-bottom">
                            <div class="b-item-list__item-name">
                                <?= $arItem['NAME']; ?>
                            </div>
                            <? if ($arItem["MIN_PRICE"]["VALUE"] <= $arItem["MIN_PRICE"]["DISCOUNT_VALUE"]): ?>
                                <div class="b-item-list__item-price"><?= $arItem["MIN_PRICE"]["PRINT_VALUE"] ?> </div>
                            <? else: ?>
                                <div class="b-item-list__item-price"><u><?= $arItem["MIN_PRICE"]["PRINT_VALUE"] ?></u> </div>
                                <div class="b-item-list__item-price"><?= $arItem["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"] ?> </div>
                            <? endif; ?>

                            <div id="add2cart_button<?= $arItem['ID']; ?>" class="b-item-list__item-buy b-yellow-button"> <?=GetMessage('CT_BCS_TPL_MESS_BTN_BUY')?> </div>
                        </div>
                    </div>
                </a>
            </li>

            <?
        }
        ?></ul>

    <?
    if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
        ?><? echo $arResult["NAV_STRING"]; ?><?
    }
}
?>