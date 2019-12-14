<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

<?php if (strlen($arResult["PREVIEW_TEXT"]) > 0): ?>
	
	<?php $this->SetViewTarget('brand-full'); ?>
        <div class="media brand">
            <div class="media-left media-middle">
                <?php if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])): ?>
                    <?php $picture = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], Array("width" => 250, "height" => 180)); ?>
                    <img class="media-object brand__logo" src="<?=$picture["src"]?>" alt="<?=$arResult["DETAIL_PICTURE"]['ALT']?>" title="<?=$arResult["DETAIL_PICTURE"]['TITLE']?>">
                <?php elseif (is_array($arResult["PREVIEW_PICTURE"])): ?>
                    <?php $picture = CFile::ResizeImageGet($arResult["PREVIEW_PICTURE"], Array("width" => 250, "height" => 180)); ?>
                    <img class="media-object brand__logo" src="<?=$picture["src"]?>" alt="<?=$arResult["PREVIEW_PICTURE"]['ALT']?>" title="<?=$arResult["PREVIEW_PICTURE"]['TITLE']?>">
                <?php endif; ?>
            </div>
            <div class="media-body brand__text">
                <?=$arResult["PREVIEW_TEXT"]?>
            </div>
        </div>
	<?php $this->EndViewTarget(); ?>
	
<?php else: ?>
	
	<?php $this->SetViewTarget('brand-mini'); ?>

        <?php if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])): ?>
            <?php $picture = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], Array("width" => 100, "height" => 100)); ?>
            <img src="<?=$picture["src"]?>" class="brand__logo" alt="<?=$arResult["DETAIL_PICTURE"]['ALT']?>" title="<?=$arResult["DETAIL_PICTURE"]['TITLE']?>">
        <?php elseif (is_array($arResult["PREVIEW_PICTURE"])): ?>
            <?php $picture = CFile::ResizeImageGet($arResult["PREVIEW_PICTURE"], Array("width" => 100, "height" => 100)); ?>
            <img src="<?=$picture["src"]?>" class="brand__logo" alt="<?=$arResult["PREVIEW_PICTURE"]['ALT']?>" title="<?=$arResult["PREVIEW_PICTURE"]['TITLE']?>">
        <?php endif; ?>

	<?php $this->EndViewTarget(); ?>

<?php endif; ?>