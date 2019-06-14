<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market;

// assets

$this->addExternalJs('/bitrix/js/yandex.market/field/filter/collection.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/filter/item.js');

$lang = [
	'NAME_TOGGLE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_NAME_TOGGLE'),
	'LEFT_COUNT' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_LEFT_COUNT'),
	'LEFT_COUNT_PROGRESS' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_LEFT_COUNT_PROGRESS'),
	'LEFT_COUNT_FAIL' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_LEFT_COUNT_FAIL'),
	'LEFT_COUNT_EMPTY' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_LEFT_COUNT_EMPTY'),
	'PRODUCT_1' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_PRODUCT_1'),
	'PRODUCT_2' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_PRODUCT_2'),
	'PRODUCT_5' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_PRODUCT_5'),
];
$langStatic = [
	'DELIVERY_EDIT_BUTTON' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_DELIVERY_EDIT'),
	'ADD_BUTTON' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_ADD_BUTTON'),
	'ADD_BUTTON_TOOLTIP' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_FILTER_ADD_BUTTON_TOOLTIP'),
];
$fieldId = 'filter-' . $this->randString(5);

// output

if (!$arParams['MULTIPLE']) // single
{
	$itemInputName = $arParams['INPUT_NAME'];
	$itemValue = $arParams['VALUE'];
	$isItemPlaceholder = $arParams['PLACEHOLDER'];

	?>
	<div class="spacing--1x1 <?= $arParams['CHILD'] ? $arParams['CHILD_CLASS_NAME'] : 'js-plugin'; ?> js-condition-manager" id="<?= $fieldId; ?>" data-plugin="Field.Filter.Item" <?= $arParams['CHILD'] ? '' : 'data-base-name="' . $arParams['INPUT_NAME'] .'"';  ?>>
		<?
		include __DIR__ . '/partials/item-content.php';
		?>
	</div>
	<?
}
else // multiple
{
	$collectionAttributes = '';
	$collectionAddButtonId = null;
	$collectionLeftCountId = null;
	$collectionLeftMessageId = null;

	if ($arParams['EXPORT_ADD_BUTTON'])
	{
		$collectionAddButtonId = 'FILTER_ADD_' . $this->randString();

		$collectionAttributes .= ' data-add-button-element="#' . $collectionAddButtonId . '"';
	}

	if ($arParams['EXPORT_LEFT_COUNT'])
	{
		$collectionLeftCountId = 'LEFT_COUNT_' . $this->randString();

		$collectionAttributes .= ' data-left-count-element="#' . $collectionLeftCountId . '"';
	}

	if ($arParams['EXPORT_LEFT_MESSAGE'])
	{
		$collectionLeftMessageId = 'LEFT_MESSAGE_' . $this->randString();

		$collectionAttributes .= ' data-left-message-element="#' . $collectionLeftMessageId . '"';
	}

	if (!empty($arParams['REFRESH_COUNT_ON_LOAD']))
	{
		$collectionAttributes .= ' data-refresh-count-on-load="true"';
	}

	?>
	<div class="<?= $arParams['HAS_VALUE'] ? '' : 'is--empty';  ?> spacing--1x1 <?= $arParams['CHILD'] ? $arParams['CHILD_CLASS_NAME'] : 'js-plugin'; ?> js-condition-manager" id="<?= $fieldId; ?>" data-plugin="Field.Filter.Collection" <?= $arParams['CHILD'] ? '' : 'data-base-name="' . $arParams['INPUT_NAME'] .'"';  ?> <?= $collectionAttributes; ?>>
		<?
		$isFirstValue = true;

		foreach ($arParams['VALUE'] as $itemIndex => $itemValue)
		{
			$itemInputName = $arParams['INPUT_NAME'] . '[' . $itemIndex . ']';
			$isItemPlaceholder = $arParams['PLACEHOLDER'] || !empty($itemValue['PLACEHOLDER']);

			if (!$isFirstValue)
			{
				?>
				<div class="b-block-sort js-filter-collection__sort">
					<button class="b-block-sort__down js-filter-collection__sort-button" type="button"></button><?
					?><button class="b-block-sort__up js-filter-collection__sort-button" type="button"></button>
				</div>
				<?
			}
			?>
			<div class="js-filter-collection__item <?= $isItemPlaceholder ? 'is--hidden' : ''; ?>" data-plugin="Field.Filter.Item">
				<?
				include __DIR__ . '/partials/item-content.php';
				?>
			</div>
			<?

			$isFirstValue = false;
		}
		?>
		<script type="text/html" class="js-filter-collection__sort-template">
			<div class="b-block-sort js-filter-collection__sort">
				<button class="b-block-sort__down js-filter-collection__sort-button" type="button"></button><?
				?><button class="b-block-sort__up js-filter-collection__sort-button" type="button"></button>
			</div>
		</script>
		<?
		include __DIR__ . '/partials/left-count.php';
		include __DIR__ . '/partials/add-button.php';
		?>
	</div>
	<?
}

$managerData = [
	'enums' => $arResult['VALUE_ENUM'],
	'compareList' => $arResult['COMPARE_ENUM']
];
?>
<script>
	(function() {
		var Source = BX.namespace('YandexMarket.Source');
		var utils = BX.namespace('YandexMarket.Utils');

		// source manager

		new Source.Manager('#<?= $fieldId ?>', <?= Market\Utils::jsonEncode($managerData, JSON_UNESCAPED_UNICODE); ?>);

		// lang

		utils.registerLang(<?= Market\Utils::jsonEncode($lang, JSON_UNESCAPED_UNICODE); ?>, 'YANDEX_MARKET_FIELD_FILTER_');
	})();
</script>
