<?php

use \Bitrix\Main\Localization\Loc;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>
<?php
if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):
    $strEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
    $strDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
    $arDeleteParams = array('CONFIRM' => Loc::getMessage('CT_BCS_ELEMENT_DELETE_CONFIRM'));
?>
	<section class="search__items">
	<?php foreach ($arResult['ITEMS'] as $key1 => $arItem): ?>
        <?php
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strDelete, $arDeleteParams);
		?>
        <article class="search_item clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>">

            <?php if ($arItem['IMAGES'][0]['src'] != ''): ?>
                <div class="search_item__pic">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <img class="search_item__img" src="<?=$arItem['IMAGES'][0]['src']?>" width="<?=$arItem['IMAGES'][0]['width']?>" height="<?=$arItem['IMAGES'][0]['height']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>">
                    </a>
                </div>
			<?php endif; ?>

            <header class="search_item__head">
                <a class="search_item__name" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                <ol class="search_item__breadcrumb breadcrumb">
                    <?php foreach($arItem['SECTION']['PATH'] as $arPath): ?>
                        <li><a href="<?=$arPath['SECTION_PAGE_URL']?>"><?=$arPath['NAME']?></a></li>
                    <?php endforeach; ?>
                </ol>
            </header>
            
            <?php if ($arItem['PREVIEW_TEXT'] != '' || $arItem['DETAIL_TEXT'] != ''): ?>
                <div class="search_item__descr">
                    <?php
                    if ($arItem['PREVIEW_TEXT'] != '') {
                        echo $arItem['PREVIEW_TEXT'];
                    } else {
                        echo $arItem['DETAIL_TEXT'];
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="prices">
                <?php if (is_array($arItem['OFFERS']) && count($arItem['OFFERS']) > 0): ?>
                    <span class="price__pv"><?php if ($arItem['OFFERS'][0]['MIN_PRICE']['DISCOUNT_DIFF']) { echo $arItem['OFFERS'][0]['MIN_PRICE']['PRINT_VALUE']; }?></span>
                    <span class="price__pdv"><?=GetMessage('PRICE_FROM')?> <?=$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span>
                <?php else: ?>
                    <span class="price__pv"><?if($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0) { echo $arItem['MIN_PRICE']['PRINT_VALUE']; } ?></span>
                    <span class="price__pdv"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span>
                <?php endif; ?>
            </div>
		</article>
	<?php endforeach; ?>
	</section>
<?php endif; ?>