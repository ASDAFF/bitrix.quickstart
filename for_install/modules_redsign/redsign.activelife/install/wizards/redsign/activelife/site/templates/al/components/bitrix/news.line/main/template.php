<?

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$obParser = new CTextParser;
?>
<section class="newsmain js-newsline owl-shift">
	<?php
		foreach($arResult['ITEMS'] as $arItem):
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
		<article class="newsmain__item clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <?php if(is_array($arItem['PREVIEW_PICTURE']) && $arItem['PREVIEW_PICTURE']['SRC'] != ''): ?>
                <a class="newsmain__pic" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <img class="newsmain__img" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
                </a>
            <?php endif; ?>
            <div class="newsmain__body">
                <a class="newsmain__parent" href="<?=$arItem['IBLOCK_LINK']?>"><?=$arItem['IBLOCK_NAME']?></a>
                <a class="newsmain__name text_fade" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a>
            </div>
            <?php if ($arItem['PREVIEW_TEXT'] != ''): ?>
                <div class="newsmain__preview"><?=$obParser->html_cut($arItem['PREVIEW_TEXT'], 128)?></div>
            <?php endif; ?>
		</article>
	<?php endforeach; ?>
</section>