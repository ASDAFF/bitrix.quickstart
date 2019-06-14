<?php
/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent         $component
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $parentTemplateFolder
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}
$this->addExternalJs('/bitrix/js/api.reviews/history/history.min.js');
?>
<? if($arParams['SORT_FIELDS']): ?>
	<?
	$order = ((is_set($_REQUEST['arorder']) && $_REQUEST['arorder'] == 'desc') ? 'asc' : 'desc');
	$sort  = htmlspecialcharsbx(trim($_REQUEST['arsort']));

	$_SESSION['arsort']  = $sort;
	$_SESSION['arorder'] = $order;
	?>
	<div id="api-reviews-sort" class="api-reviews-sort arsort-color-<?=$arParams['COLOR']?>">
		<div class="api-left">
			<div class="api-sort-label"><?=Loc::getMessage('API_REVIEWS_SORT_BY')?></div>
			<? foreach($arParams['SORT_FIELDS'] as $key => $val): ?>
				<?
				$active  = ($sort == ToLower($val));
				$arorder = $active && $_REQUEST['arorder'] == 'desc' ? 'asc' : 'desc';
				?>
				<a class="<?=trim(htmlspecialcharsbx($_REQUEST['arorder']))?><? if($active): ?> api-active<? endif ?>"
				   href="<?=$APPLICATION->GetCurPageParam('arsort=' . ToLower($val) . '&arorder=' . $arorder, array('arsort', 'arorder'))?>">
					<?=Loc::getMessage('API_REVIEWS_SORT_FIELD_' . $val)?>
				</a>
			<? endforeach; ?>
		</div>
		<div class="api-right"><div></div></div>
	</div>
	<?
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsSort();
		});
	</script>
	<?
	$html = ob_get_contents();
	ob_end_clean();

	Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);
	?>
<? endif ?>

