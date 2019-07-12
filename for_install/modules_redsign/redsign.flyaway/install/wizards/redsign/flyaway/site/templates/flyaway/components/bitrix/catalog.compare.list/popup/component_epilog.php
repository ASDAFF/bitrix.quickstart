<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

use \Bitrix\Main\Localization\Loc;

global $APPLICATION;

if (isset($_REQUEST['AJAX']) && $_REQUEST['AJAX'] == 'Y' && isset($_REQUEST['REFRESH_FAVORITE']) && $_REQUEST['REFRESH_FAVORITE'] == 'Y' ){
  $APPLICATION->RestartBuffer();?>
		<?
			include($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/compare_items.php');
		?>
  <?php
	die();
}

$arrIDs = array();

foreach ($arResult as $arItem) {
	$arrIDs[$arItem['ID']] = 'Y';
}

?><script>
	rsFlyaway.compare = <?print(count($arrIDs) > 0 ? json_encode($arrIDs) : '{}')?>;
	rsFlyaway.count_compare = <?=(count($arResult))?>;
</script><?
