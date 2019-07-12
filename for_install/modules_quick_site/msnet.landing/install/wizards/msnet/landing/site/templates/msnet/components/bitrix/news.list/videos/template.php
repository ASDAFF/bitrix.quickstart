<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<div class="m-video" id="videos">
    <h2><?= $arResult['NAME'] ?></h2>
    <div class="row">
        <?
        $i = 0;
        foreach ($arResult['ITEMS'] as $key => $arItem): ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        $i++;
        ?>
        <div class="col-sm-6 col-md-3" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
            <div class="m-video__item">
                <a href="<?= $arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'] ?>" class="m-video__link" data-fancybox="">
                    <div class="m-video__image">
                        <img src="<?= $arItem['FIELDS']['PREVIEW_PICTURE']['SRC'] ?>"
                             alt="<?= $arItem['FIELDS']['NAME'] ?>">
                    </div>
                    <div class="m-video__title">
                        <?= $arItem['FIELDS']['NAME'] ?>
                    </div>
                </a>
            </div>
        </div>
        <? if ($i % 4 == 0): ?>
    </div>
    <div class="row">
        <? endif; ?>
        <? endforeach; ?>
    </div>
</div>