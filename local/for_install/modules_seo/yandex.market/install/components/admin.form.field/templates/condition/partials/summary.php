<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

$summaryTextParts = [];

foreach ($arParams['VALUE'] as $itemValue)
{
	$hasField = isset($itemValue['FIELD']) && strlen($itemValue['FIELD']) > 0;
	$hasCompare = isset($itemValue['COMPARE']) && strlen($itemValue['COMPARE']) > 0;
	$hasValue = false;

	if (isset($itemValue['VALUE']))
	{
		$hasValue = (is_array($itemValue['VALUE']) ? !empty($itemValue['VALUE']) : (strlen($itemValue['VALUE']) > 0));
	}

	if ($hasField && $hasCompare && $hasValue)
	{
		$fieldText = isset($arParams['FIELD_ENUM'][$itemValue['FIELD']]) ? $arParams['FIELD_ENUM'][$itemValue['FIELD']]['VALUE'] : $itemValue['FIELD'];
		$compareText = $itemValue['COMPARE'];
		$compareEnum = null;
		$isCompareDefined = false;
		$valueText = null;
		$isItemValueMultiple = is_array($itemValue['VALUE']);
		$isFoundItemValue = false;

		if (!empty($arParams['COMPARE_ENUM']))
		{
			foreach ($arParams['COMPARE_ENUM'] as $compareOption)
			{
				if ($itemValue['COMPARE'] === $compareOption['ID'])
				{
					$compareText = $compareOption['VALUE'];
					$compareEnum = isset($compareOption['ENUM']) ? $compareOption['ENUM'] : null;
					$isCompareDefined = isset($compareOption['DEFINED']);
					break;
				}
			}
		}

		if (!$isCompareDefined)
		{
			$valueEnum = null;

			if ($compareEnum !== null)
			{
				$valueEnum = $compareEnum;
			}
			else if (isset($arParams['VALUE_ENUM'][$itemValue['FIELD']]))
			{
				$valueEnum = $arParams['VALUE_ENUM'][$itemValue['FIELD']];
			}

			if ($valueEnum !== null)
			{
				foreach ($valueEnum as $valueOption)
				{
					$isSelected = $isItemValueMultiple
						? in_array($valueOption['ID'], $itemValue['VALUE'])
						: $valueOption['ID'] == $itemValue['VALUE']; // maybe int conversion

					if ($isSelected)
					{
						$isFoundItemValue = true;
						$valueText = ($valueText ? $valueText . ', ' : '') . $valueOption['VALUE'];
					}
				}
			}

			if (!$isFoundItemValue)
			{
				$valueText = $isItemValueMultiple ? implode($itemValue['VALUE']) : $itemValue['VALUE'];
			}
		}

		$summaryTextParts[] = trim($fieldText . ' ' . $compareText . ' ' . $valueText);
	}
}

?>
<a class="b-link action--heading target--inside js-condition-summary__text" href="#"><?= count($summaryTextParts) > 0 ? implode($lang['JUNCTION'], $summaryTextParts) : $lang['PLACEHOLDER']; ?></a>
<div class="b-grid spacing--1x1">
	<div class="b-grid__item vertical--middle">
		<button class="adm-btn js-condition-summary__edit-button" type="button"><?= $langStatic['EDIT_BUTTON']; ?></button>
	</div>
	<?
	$countParentClassName = 'js-condition-summary';

	include __DIR__ . '/count.php';
	?>
</div>
