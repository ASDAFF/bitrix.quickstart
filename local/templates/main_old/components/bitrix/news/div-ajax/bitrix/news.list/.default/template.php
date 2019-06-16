<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="news-list">
    <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
        <?= $arResult["NAV_STRING"] ?><br/>
    <? endif; ?>


<div class="pagination-items">
    <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>

        <div class="row news-item row-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <? if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
                <span class="news-date-time"><? echo $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
            <? endif ?>
            <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                    <a href="<? echo $arItem["DETAIL_PAGE_URL"] ?>"><b><? echo $arItem["NAME"] ?></b></a><br/>
                <? else: ?>
                    <b><? echo $arItem["NAME"] ?></b><br/>
                <? endif; ?>
            <? endif; ?>
            <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                <? echo $arItem["PREVIEW_TEXT"]; ?>
            <? endif; ?>
        </div>

    <? endforeach; ?>
</div>

    <? $leftItems = $arResult['NAV_RESULT']->NavRecordCount - ($arResult['NAV_RESULT']->NavPageNomer * $arResult['NAV_RESULT']->NavPageSize);
    if ($leftItems > $arResult['NAV_RESULT']->NavPageSize) $leftItems = $arResult['NAV_RESULT']->NavPageSize; ?>
    <div class="pages-container">
        <? if ($leftItems > 0): ?>
            <a href="javascript:void(0)" class="more_goods">еще <?= $leftItems ?></a>
        <? endif; ?>
        <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?><br/><?= $arResult["NAV_STRING"] ?><? endif; ?>
    </div>
</div>
