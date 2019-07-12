<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$FILTER_NAME = (string)$arParams["CATALOG_FILTER_NAME"];

global ${$FILTER_NAME};
if (!is_array(${$FILTER_NAME})) {
	${$FILTER_NAME} = array();
}

${$FILTER_NAME} = array_merge(${$FILTER_NAME}, $arResult['CATALOG_FILTER']);
?>

<?php
//$this->SetViewTarget('brand-logo');
ob_start(); ?>

<?php if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])): ?>
    <div class="brand">
        <img src="<?=$arResult["DETAIL_PICTURE"]['RESIZE']["src"]?>" class="brand__logo" alt="<?=$arResult["DETAIL_PICTURE"]['ALT']?>" title="<?=$arResult["DETAIL_PICTURE"]['TITLE']?>">
    </div>
<?php elseif (is_array($arResult["PREVIEW_PICTURE"])): ?>
    <div class="brand">
        <img src="<?=$arResult["PREVIEW_PICTURE"]['RESIZE']["src"]?>" class="brand__logo" alt="<?=$arResult["PREVIEW_PICTURE"]['ALT']?>" title="<?=$arResult["PREVIEW_PICTURE"]['TITLE']?>">
    </div>
<?php endif; ?>

<?php
//$this->EndViewTarget();
global $APPLICATION;
$sHtmlContent = ob_get_clean();
$APPLICATION->AddViewContent('catalog_sidebar', $sHtmlContent, 200);
