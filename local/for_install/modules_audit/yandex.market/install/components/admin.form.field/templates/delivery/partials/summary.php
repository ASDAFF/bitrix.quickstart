<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market\Utils;

$summaryValues = $arParams['MULTIPLE'] ? $arParams['VALUE'] : array( $arParams['VALUE'] );

?>
<div class="b-field-delivery__summary-list <?= $arResult['HAS_SUMMARY'] ? 'is--fill' : 'is--empty'; ?> js-delivery-summary__list">
	<?
	foreach ($arResult['SUMMARY_LIST'] as $itemIndex => $summary)
	{
		?>
		<div class="b-field-delivery__summary b-form-pill <?= $summary['PLACEHOLDER'] ? 'is--hidden' : ''; ?> js-delivery-summary__item" data-index="<?= $itemIndex; ?>">
			<a class="b-link action--heading target--inside js-delivery-summary__item-text" href="#"><?= $summary['TEXT']; ?></a>
			<button class="b-close js-delivery-summary__item-delete" type="button" title="<?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_ROW_DELETE'); ?>"></button>
		</div>
		<?
	}
	?>
</div>