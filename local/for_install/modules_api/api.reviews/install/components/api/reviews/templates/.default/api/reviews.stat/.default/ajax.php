<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(dirname(__FILE__) . '/template.php');
?>
<div id="api-reviews-stat" class="api-reviews-stat arstat-color-<?=$arParams['COLOR']?>"
     itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
	<div class="api-left-stat">
		<div class="api-title"><?=$arParams['MESS_TOTAL_RATING']?></div>
		<div class="api-stars-empty">
			<div class="api-stars-full" style="width: <?=$arResult['FULL_RATING']?>%"></div>
		</div>
		<div class="api-average">
			<span class="api-average-rating"><?=$arResult['AVERAGE_RATING']?></span>
			<span class="api-full-rating"><span>/</span> <?=Loc::getMessage('API_REVIEWS_STAT_5')?></span>

			<span class="api-hidden" itemprop="ratingValue"><?=$arResult['MIN_AVERAGE_RATING']?></span>
			<span class="api-hidden" itemprop="bestRating"><?=Loc::getMessage('API_REVIEWS_STAT_5')?></span>
			<span class="api-hidden" itemprop="reviewCount"><?=$arResult['COUNT_ITEMS']?></span>
		</div>
		<div class="api-subtitle"><?=str_replace('#N#', $arResult['COUNT_ITEMS'], $arParams['MESS_CUSTOMER_RATING'])?></div>
	</div>
	<div class="api-right-stat">
		<div class="api-info">
			<? for($i = 5; $i >= 1; $i--): ?>
				<div class="api-info-row js-getFilter" data-rating="<?=$i?>">
					<div class="api-info-title">
						<div class="api-icon-star api-icon-star<?=$i?>"></div>
					</div>
					<div class="api-info-progress">
						<div style="width:<?=$arResult['COUNT_PROGRESS'][ $i ]?>%" class="api-info-bar api-info-bar<?=$i?>"></div>
					</div>
					<div class="api-info-qty" title="<?=$arResult['COUNT_REVIEWS'][ $i ]?>"><?=$arResult['COUNT_PROGRESS'][ $i ]?>%</div>
				</div>
			<? endfor ?>
		</div>
	</div>
</div>

