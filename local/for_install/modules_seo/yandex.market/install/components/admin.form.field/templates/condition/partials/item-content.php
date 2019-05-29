<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** @var $itemInputName string */
/** @var $itemValue array */
/** @var $isItemPlaceholder boolean */
/** @var $lang array */
/** @var $langStatic array */

$selectedSourceField = null;
$selectedSourceFieldType = null;
$selectedCompare = null;
$selectedCompareDefined = null;
$isSelectedCompareMultiple = null;

?>
<input class="js-condition-item__input" type="hidden" data-name="ID" <?

	if (!$isItemPlaceholder)
	{
		echo ' name="' . $itemInputName . '[ID]"';
		echo ' value="' . $itemValue['ID'] . '"';
	}

?> />
<div class="b-filter-condition-fields">
	<div class="b-filter-condition-field type--field">
		<label class="b-filter-condition-field__label"><?= $langStatic['FIELD_FIELD']; ?></label>
		<?
		if (!empty($arParams['FIELD_ENUM']))
		{
			?>
			<div class="b-filter-condition-field__input adm-select-wrap">
				<select class="adm-select js-condition-item__input js-condition-item__field" data-name="FIELD" <?

					if (!$isItemPlaceholder)
					{
						echo ' name="' . $itemInputName . '[FIELD]"';
					}

				?>>
					<?
					$prevSource = null;

					foreach ($arParams['FIELD_ENUM'] as $field)
					{
						$fieldSource = (isset($field['SOURCE']) ? $field['SOURCE'] : null);

						if ($prevSource !== $fieldSource)
						{
							$prevSource = $fieldSource;

							if ($prevSource !== null) { echo '</optgroup>'; }

							if ($fieldSource !== null)
							{
								$source = $arParams['SOURCE_ENUM'][$field['SOURCE']];

								echo '<optgroup label="' . $source['VALUE'] . '">';
							}
						}

						$isSelectedField = (!$isItemPlaceholder && $itemValue['FIELD'] === $field['ID']);

						if (!isset($selectedSourceField) || $isSelectedField)
						{
							$selectedSourceField = $field['ID'];
							$selectedSourceFieldType = $field['TYPE'];
						}

						?>
						<option value="<?= $field['ID']; ?>" <?= $isSelectedField ? 'selected' : ''; ?> data-type="<?= $field['TYPE']; ?>"><?= $field['VALUE']; ?></option>
						<?
					}

					if ($prevSource !== null) { echo '</optgroup>'; }
					?>
				</select>
			</div>
			<?
		}
		else
		{
			?>
			<input class="b-filter-condition-field__input adm-input js-condition-item__input" data-name="FIELD" <?

				if (!$isItemPlaceholder)
				{
					echo ' name="' . $itemInputName . '[FIELD]"';
					echo ' value="' . $itemValue['FIELD'] . '"';
				}

			?> />
			<?
		}
		?>
	</div>
	<div class="b-filter-condition-field type--compare">
		<label class="b-filter-condition-field__label"><?= $langStatic['FIELD_COMPARE']; ?></label>
		<?
		if (!empty($arParams['COMPARE_ENUM']))
		{
			?>
			<div class="b-filter-condition-field__input type--compare adm-select-wrap">
				<select class="adm-select js-condition-item__input js-condition-item__compare" data-name="COMPARE" <?

					if (!$isItemPlaceholder)
					{
						echo ' name="' . $itemInputName . '[COMPARE]"';
					}

				?>>
					<?
					foreach ($arParams['COMPARE_ENUM'] as $compareOption)
					{
						$isSelectedCompareOption = (!$isItemPlaceholder && $itemValue['COMPARE'] === $compareOption['ID']);
						$isActive = in_array($selectedSourceFieldType, $compareOption['TYPE_LIST']);

						if ($isSelectedCompareOption || ($selectedCompare === null && $isActive))
						{
							$selectedCompare = $compareOption['ID'];
							$selectedCompareDefined = isset($compareOption['DEFINED']) ? $compareOption['DEFINED'] : null;
							$isSelectedCompareMultiple = $compareOption['MULTIPLE'];
						}

						?>
						<option value="<?= $compareOption['ID']; ?>" <?= $isSelectedCompareOption ? 'selected' : ''; ?> <?= $isActive ? '' : 'disabled'; ?>><?= $compareOption['VALUE']; ?></option>
						<?
					}
					?>
				</select>
			</div>
			<?
		}
		else
		{
			?>
			<input class="b-filter-condition-field__input adm-input js-condition-item__input" data-name="COMPARE" <?

				if (!$isItemPlaceholder)
				{
					echo ' name="' . $itemInputName . '[COMPARE]"';
					echo ' value="' . $itemValue['COMPARE'] . '"';
				}

			?> />
			<?
		}
		?>
	</div>
	<div class="b-filter-condition-field type--value js-condition-item__value-cell <?= $selectedCompareDefined !== null ? 'visible--hidden' : ''; ?>">
		<label class="b-filter-condition-field__label"><?= $langStatic['FIELD_VALUE']; ?></label>
		<input class="js-condition-item__input-holder" type="hidden" data-name="VALUE" value="" <?

			if (!$isItemPlaceholder)
			{
				echo ' name="' . $itemInputName . '[VALUE]"';
			}

		?> />
		<?
		$valueEnum = null;

		if (!empty($arParams['COMPARE_ENUM'][$selectedCompare]['ENUM']))
		{
			$valueEnum = $arParams['COMPARE_ENUM'][$selectedCompare]['ENUM'];
		}
		else if (!empty($arParams['VALUE_ENUM'][$selectedSourceField]))
		{
			$valueEnum = $arParams['VALUE_ENUM'][$selectedSourceField];
		}

		if ($selectedCompareDefined !== null)
		{
			?>
			<input class="js-condition-item__input js-condition-item__value" type="hidden" value="<?= $selectedCompareDefined; ?>" data-name="VALUE" <?

				if (!$isItemPlaceholder)
				{
					echo ' name="' . $itemInputName . '[VALUE]"';
				}

			?> />
			<?
		}
		else if ($valueEnum !== null)
		{
			$isItemValueMultiple = is_array($itemValue['VALUE']);

			?>
			<select class="b-filter-condition-field__input js-condition-item__input js-condition-item__value js-plugin" <?= $isSelectedCompareMultiple ? 'multiple' : ''; ?> size="1" data-plugin="Ui.Input.TagInput" data-name="VALUE" <?

				if (!$isItemPlaceholder)
				{
					echo ' name="' . $itemInputName . '[VALUE][]"';
				}

			?>>
				<?
				foreach ($valueEnum as $enum)
				{
					$isSelectedEnum = false;

					if (!$isItemPlaceholder)
					{
						$isSelectedEnum = ($isItemValueMultiple ? in_array($enum['ID'], $itemValue['VALUE']) : $itemValue['VALUE'] == $enum['ID']);
					}

					?>
					<option value="<?= $enum['ID']; ?>" <?= $isSelectedEnum ? 'selected' : ''; ?>><?= $enum['VALUE']; ?></option>
					<?
				}
				?>
			</select>
			<?
		}
		else
		{
			?>
			<input class="b-filter-condition-field__input adm-input js-condition-item__input js-condition-item__value" data-name="VALUE" <?

				if (!$isItemPlaceholder)
				{
					echo ' name="' . $itemInputName . '[VALUE]"';
					echo ' value="' . $itemValue['VALUE'] . '"';
				}

			?> />
			<?
		}
		?>
	</div>
</div>
