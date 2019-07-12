<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<div class="m-news clearfix" id="news">
    <h2><?= $arResult['NAME'] ?></h2>
    <? foreach ($arResult['ITEMS'] as $key => $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div class="m-news__item clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
            <div class="m-news__image">
                <img src="<?= $arItem['FIELDS']['PREVIEW_PICTURE']['SRC'] ?>" alt="">
            </div>
            <div class="m-news__info">
                <div class="m-news__date">
                    <?=$arItem['DISPLAY_ACTIVE_FROM']?>
                </div>
                <div class="m-news__title">
                    <?=$arItem['FIELDS']['NAME']?>
                </div>
            </div>
        </div>
    <? endforeach; ?>
</div>