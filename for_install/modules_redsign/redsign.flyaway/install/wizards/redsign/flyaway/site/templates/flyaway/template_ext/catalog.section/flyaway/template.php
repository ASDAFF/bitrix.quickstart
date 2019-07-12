<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

if (!empty($arParams["AJAX_ID_SECTION"])) {
  ?><div id="<?=$arParams['AJAX_ID_SECTION']?>"><?
}
?>

<?php if(empty($arParams['HIDE_BLOCK_TITLE']) || $arParams['HIDE_BLOCK_TITLE'] !== 'Y'): ?>
	<h2><?=($arParams['BLOCK_TITLE']? htmlspecialcharsbx($arParams['BLOCK_TITLE']) : Loc::getMessage('SGB_TPL_BLOCK_TITLE_DEFAULT'))?></h2>
<? endif; ?>

<?php
$strEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$strDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$arDeleteParams = array('CONFIRM' => Loc::getMessage('CT_BCS_ELEMENT_DELETE_CONFIRM'));

if ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'showcase' || $arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'showcase_mob') {
	include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/showcase.php');
} elseif ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'list') {
	include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/list.php');
} elseif ($arResult['TEMPLATE_DEFAULT']['TEMPLATE'] == 'list_little') {
	include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/table.php');
} else {
    include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/flyaway/showcase.php');
}

$templateData['ADD_HIDER'] = false;
if(!is_array($arResult['ITEMS']) || count($arResult['ITEMS']) < 1 && $arParams['EMPTY_ITEMS_HIDE_FIL_SORT'] == 'Y' && empty($_REQUEST['set_filter']) ) {
	$templateData['ADD_HIDER'] = true;
}

if (!empty($arParams["AJAX_ID_SECTION"])) {
  ?></div><?
}
