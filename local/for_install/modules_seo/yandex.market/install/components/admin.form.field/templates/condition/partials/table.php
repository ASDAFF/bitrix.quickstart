<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

if (!$arParams['MULTIPLE'])
{
	$itemInputName = $arParams['INPUT_NAME'];
	$itemValue = $arParams['VALUE'];
	$isItemPlaceholder = $arParams['PLACEHOLDER'];

	?>
	<div class="b-form-pill dimension--gamma js-condition-summary__field js-condition-manager" data-plugin="Field.Condition.Item">
		<?
		include __DIR__ . '/item-content.php';
		?>
	</div>
	<?
}
else
{
	?>
	<div class="js-condition-summary__field js-condition-manager" data-plugin="Field.Condition.Collection">
		<?
		$isFirstValue = true;
		$valuesCount = count($arParams['VALUE']);

		foreach ($arParams['VALUE'] as $itemIndex => $itemValue)
		{
			$itemInputName = $arParams['INPUT_NAME'] . '[' . $itemIndex . ']';
			$isItemPlaceholder = $arParams['PLACEHOLDER'] || !empty($itemValue['PLACEHOLDER']);

			if (!$isFirstValue)
			{
				?>
				<span class="b-filter-condition-junction js-condition-collection__junction"><?= $lang['JUNCTION']; ?></span>
				<?
			}
			?>
			<div class="b-form-pill dimension--gamma <?= $isItemPlaceholder ? 'is--hidden' : ''; ?> js-condition-collection__item" data-plugin="Field.Condition.Item">
				<?
				include __DIR__ . '/item-content.php';
				?>
				<button class="b-close js-condition-collection__item-delete <?= $valuesCount === 1 ? 'is--hidden' : ''; ?>" type="button"></button>
			</div>
			<?

			$isFirstValue = false;
		}
		?>
		<div class="spacing--1x1">
			<button class="adm-btn js-condition-collection__add-button" type="button"><?= $langStatic['ADD_BUTTON']; ?></button>
		</div>
		<div class="b-grid spacing--3x4">
			<?
			$countParentClassName = 'js-condition-collection';

			include __DIR__ . '/count.php';
			?>
		</div>
		<script class="js-condition-collection__junction-template" type="text/html">
			<span class="b-filter-condition-junction js-condition-collection__junction"><?= $lang['JUNCTION']; ?></span>
		</script>
	</div>
	<?
}
?>