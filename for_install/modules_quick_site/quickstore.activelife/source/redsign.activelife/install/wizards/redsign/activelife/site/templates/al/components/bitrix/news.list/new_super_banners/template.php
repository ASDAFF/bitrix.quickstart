<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
?>

<?php $this->SetViewTarget('mainbanners'); ?>

<?php if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0): ?>
<div class="mainbanners">
    <div class="mainbanners__owl js-mainbanners owl-carousel owl-theme" data-timeout="<?=(int)$arParams['TIME_INTERVAL'] < 1000 ? 8000 : $arParams['TIME_INTERVAL'];?>">

        <?php foreach($arResult['ITEMS'] as $arItem): ?>
            <?php $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")); ?>
            <?php $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
            <div class="mainbanner">
                
                <?php if (is_array($arItem['PREVIEW_PICTURE'])): ?>
                    <div class="mainbanner__img" style="background-image:url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>)"></div>
                <?php elseif (!empty($arItem['PROPERTIES']['BANNER']['VALUE'])): ?>
                    <div class="mainbanner__img" style="background-image:url(<?=CFile::GetPath($arItem['PROPERTIES']['BANNER']['VALUE'])?>)"></div>
                <?php endif; ?>

                <a class="mainbanner__link container" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>"
                   <?php if($arItem['PROPERTIES']['BLANK']['VALUE']): ?>target="_blank"<?php endif; ?>
                ></a>

                <?php
                if(!empty($arResult['ADDITIONAL_BANNERS'][$arItem['ID']])):
                    $arAdditionalBanners = array_reverse($arResult['ADDITIONAL_BANNERS'][$arItem['ID']]);
                ?>
                    <div class="additional-banners js-additionals container">
                        <?php foreach($arAdditionalBanners as $arAdditionalBanner): ?>
                            <a href="<?=$arAdditionalBanner['LINK']?>" class="additional-banners__banner">
                                <img src="<?=$arAdditionalBanner['IMAGE']?>" alt="<?=$arAdditionalBanner['NAME']?>">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
    <div class="mainbanner-progressline js-mainbanners-progressline">
        <div class="mainbanner-progressline__progress js-progress"></div>
    </div>
</div>
<?php endif; ?>
<?php $this->EndViewTarget(); ?>