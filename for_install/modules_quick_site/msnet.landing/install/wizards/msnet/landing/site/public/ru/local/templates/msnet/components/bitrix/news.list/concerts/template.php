<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<div class="m-concerts" id="concerts">
    <h2><?= GetMessage('C_BLOCK_TITLE') ?></h2>
    <div class="m-concerts__table">
        <? foreach ($arResult['ITEMS'] as $key => $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="m-concerts__row" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                <div class="m-concerts__cell">
                    <div class="m-concerts__date">
                        <?= $arItem['DISPLAY_ACTIVE_FROM'] ?>
                    </div>
                </div>
                <div class="m-concerts__cell">
                    <div class="m-concerts__place">
                        <?= $arItem['NAME'] ?>
                    </div>
                </div>
                <div class="m-concerts__cell">
                    <div class="m-concerts__social m-concerts-social">
                        <a href="<?= $arItem['DISPLAY_PROPERTIES']['LINK_GROUP_VK']['VALUE'] ?>" class="m-concerts-social__link -vk" target="_blank"></a>
                    </div>
                </div>
                <div class="m-concerts__cell">
                    <a href="<?= $arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'] ?>" class="m-concerts__link" target="_blank"><?=GetMessage('C_BLOCK_BUY')?></a>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</div>