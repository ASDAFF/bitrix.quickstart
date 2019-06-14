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
?>
	<div id="<?=$arParams['FORM_ID']?>" class="api-reviews-filter">
		<div class="api-filter-btn">
				<span class="api_icon">
					<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" ratio="1"> <ellipse fill="none" stroke="#000" cx="6.11" cy="3.55" rx="2.11" ry="2.15"></ellipse> <ellipse fill="none" stroke="#000" cx="6.11" cy="15.55" rx="2.11" ry="2.15"></ellipse> <circle fill="none" stroke="#000" cx="13.15" cy="9.55" r="2.15"></circle> <rect x="1" y="3" width="3" height="1"></rect> <rect x="10" y="3" width="8" height="1"></rect> <rect x="1" y="9" width="8" height="1"></rect> <rect x="15" y="9" width="3" height="1"></rect> <rect x="1" y="15" width="3" height="1"></rect> <rect x="10" y="15" width="8" height="1"></rect></svg>
				</span>
		</div>
		<div class="api-filters">
			<? for($i = 5; $i >= 1; $i--): ?>
				<?
				$active = (in_array($i, $arResult['SESSION_RATING']));
				?>
					<a href="<?=$APPLICATION->GetCurPageParam('arfilter=1&arrating=' . join('|', array_merge(array($i),$arResult['SESSION_RATING'])), array('arfilter', 'arrating'))?>"
					   class="api-filters-item <?=$active ? 'api-active' : ''?>"
					   data-rating="<?=$i?>"
					   rel="nofollow">
						<?=Loc::getMessage('ARFC_TPL_STAR_' . $i)?>
						<? if($active): ?>
							<span class="api-del-filter js-delFilter" data-rating="<?=$i?>">&times;</span>
						<? endif; ?>
					</a>
			<? endfor ?>
		</div>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsFilter();
		});
	</script>
<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);