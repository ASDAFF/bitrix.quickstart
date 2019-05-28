<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

/** @var $tag \Yandex\Market\Export\Xml\Tag\Base */
/** @var $attribute \Yandex\Market\Export\Xml\Attribute\Base */
/** @var $tagValues array */
/** @var $isTagPlaceholder bool */
/** @var $isAttributePlaceholder bool */
/** @var $sourcesList array */
/** @var $attributeInputName string */
/** @var $attributeValue array */
/** @var $attributeValueType string */
/** @var $attributeType string */
/** @var $tagId string|null */
/** @var $tagName string|null */
/** @var $attributeId string|null */
/** @var $attributeName string|null */
/** @var $isAttribute bool */
/** @var $isRequired bool */
/** @var $isDefined bool */

$selectedTypeId = null;
$attributeFullType = $tagId . ($isAttribute ? '.' . $attributeId : '');
$availableSources = $arResult['NODE_AVAILABLE_SOURCES'][$attributeFullType] ?: $arResult['SOURCE_TYPE_ENUM_MAP'];
$defaultSource = null;
$availableTypes = $arResult['TYPE_MAP'][$attributeValueType];
$attributeRecommendation = isset($arResult['RECOMMENDATION'][$attributeFullType]) ? $arResult['RECOMMENDATION'][$attributeFullType] : null;
$rowAttributes =
	'data-type="' . $attributeFullType . '"'
	. ' data-value-type="' . $attributeValueType . '"';

if ($isRequired)
{
	$rowAttributes .= ' data-required="true"';
}
else if (!$isAttribute || $attribute->isVisible())
{
	$rowAttributes .= ' data-persistent="true"';
}

if ($isAttribute && $attributeName === 'name')
{
	$rowAttributes .= ' data-copy-type="' . $tagId .'"';
}

if (isset($arResult['DEFAULT_SOURCES'][$attributeFullType]))
{
	$defaultSource = $arResult['DEFAULT_SOURCES'][$attributeFullType];
}
else
{
	reset($availableSources);
	$defaultSource = key($availableSources);
}

?>
<tr class="<?= $isAttributePlaceholder ? 'is--hidden' : ''; ?> js-param-node-collection__item" data-plugin="Field.Param.Node" <?= $rowAttributes; ?>>
	<td class="b-param-table__cell" align="right" width="40%">
		<input class="js-param-node__input" type="hidden" data-name="ID" <?

			if (!$isTagPlaceholder && !$isAttributePlaceholder)
			{
				echo 'name="' . $attributeInputName . '[ID]' . '"';
				echo 'value="' . $attributeValue['ID'] . '"';
			}

		?> />
		<input class="js-param-node__input is--persistent" type="hidden" value="<?= htmlspecialcharsbx($attributeType); ?>" data-name="XML_TYPE" <?

			if (!$isTagPlaceholder && !$isAttributePlaceholder)
			{
				echo 'name="' . $attributeInputName . '[XML_TYPE]' . '"';
			}

		?> />
		<input class="js-param-node__input is--persistent" type="hidden" value="<?= htmlspecialcharsbx($attributeId); ?>" data-name="XML_ATTRIBUTE_NAME" <?

			if (!$isTagPlaceholder && !$isAttributePlaceholder)
			{
				echo 'name="' . $attributeInputName . '[XML_ATTRIBUTE_NAME]' . '"';
			}

		?> />
		<?
		include __DIR__ . '/name.php';
		?>
	</td>
	<td class="b-param-table__cell width--param-source-cell">
		<?
		if ($isDefined)
		{
			?>
			<input type="hidden" name="js-param-node__input" value="<?= isset($attributeValue['SOURCE_TYPE']) ? htmlspecialcharsbx($attributeValue['SOURCE_TYPE']) : ''; ?>" data-name="SOURCE_TYPE" <?

				if (!$isTagPlaceholder && !$isAttributePlaceholder)
				{
					echo 'name="' . $attributeInputName . '[SOURCE_TYPE]' . '"';
				}

			?> />
			<?
		}
		?>
		<select class="b-param-table__input js-param-node__source js-param-node__input" <?= $isDefined ? 'disabled' : ''; ?> data-name="SOURCE_TYPE" <?

			if (!$isTagPlaceholder && !$isAttributePlaceholder)
			{
				echo 'name="' . $attributeInputName . '[SOURCE_TYPE]' . '"';
			}

		?>>
			<?
			foreach ($arResult['SOURCE_TYPE_ENUM'] as $typeEnum)
			{
				if (!isset($availableSources[$typeEnum['ID']])) { continue; }

				$isDefault = ($typeEnum['ID'] === $defaultSource);
				$isSelected = (
					(!$isTagPlaceholder && !$isAttributePlaceholder && $typeEnum['ID'] === $attributeValue['SOURCE_TYPE'])
					|| ($isDefault && empty($attributeValue['SOURCE_TYPE']))
				);

				if ($isSelected)
				{
					$selectedTypeId = $typeEnum['ID'];
				}

				?>
				<option value="<?= $typeEnum['ID'] ?>" <?= $isSelected ? 'selected': ''; ?> <?= $isDefault ? 'data-default="true"' : ''; ?>><?= $typeEnum['VALUE']; ?></option>
				<?
			}
			?>
		</select>
	</td>
	<td class="b-param-table__cell width--param-field-cell">
		<?
		if ($arResult['SOURCE_TYPE_ENUM'][$selectedTypeId]['VARIABLE'])
		{
			?>
			<input class="b-param-table__input js-param-node__field js-param-node__input" type="text" <?= $isDefined ? 'readonly' : ''; ?> data-name="SOURCE_FIELD" data-type="variable" <?

				if (!$isTagPlaceholder && !$isAttributePlaceholder)
				{
					echo ' name="' . $attributeInputName . '[SOURCE_FIELD]' . '"';
					echo ' value="' . htmlspecialcharsbx($attributeValue['SOURCE_FIELD'])  . '"';
				}

			?> />
			<?
		}
		else if ($arResult['SOURCE_TYPE_ENUM'][$selectedTypeId]['TEMPLATE'])
		{
			?>
			<div class="b-control-group js-param-node__field-wrap">
				<input class="b-control-group__item pos--first b-param-table__input js-param-node__field js-param-node__input" type="text" <?= $isDefined ? 'readonly' : ''; ?> data-name="SOURCE_FIELD" data-type="template" <?

					if (!$isTagPlaceholder && !$isAttributePlaceholder)
					{
						echo ' name="' . $attributeInputName . '[SOURCE_FIELD]' . '"';
						echo ' value="' . htmlspecialcharsbx($attributeValue['SOURCE_FIELD'])  . '"';
					}

				?> />
				<button class="b-control-group__item pos--last adm-btn around--control js-param-node__template-button" type="button">...</button>
			</div>
			<?
		}
		else
		{
			if ($isDefined)
			{
				?>
				<input type="hidden" name="js-param-node__input" value="<?= isset($attributeValue['SOURCE_FIELD']) ? htmlspecialcharsbx($attributeValue['SOURCE_FIELD']) : ''; ?>" data-name="SOURCE_FIELD" <?

					if (!$isTagPlaceholder && !$isAttributePlaceholder)
					{
						echo 'name="' . $attributeInputName . '[SOURCE_FIELD]' . '"';
					}

				?> />
				<?
			}
			?>
			<select class="b-param-table__input js-param-node__field js-param-node__input" <?= $isDefined ? 'disabled' : ''; ?> data-name="SOURCE_FIELD" data-type="select" <?

				if (!$isTagPlaceholder && !$isAttributePlaceholder)
				{
					echo 'name="' . $attributeInputName . '[SOURCE_FIELD]' . '"';
				}

			?>>
				<?
				if (!$isRequired)
				{
					?>
					<option value=""><?= $lang['SELECT_PLACEHOLDER']; ?></option>
					<?
				}

				if ($selectedTypeId === $arResult['RECOMMENDATION_TYPE'])
				{
					foreach ($attributeRecommendation as $fieldEnum)
					{
						$isSelected = (!$isTagPlaceholder && !$isAttributePlaceholder && $fieldEnum['ID'] === $attributeValue['SOURCE_FIELD']);

						?>
						<option value="<?= $fieldEnum['ID'] ?>" <?= $isSelected ? 'selected': ''; ?>><?= $fieldEnum['VALUE']; ?></option>
						<?
					}
				}
				else
				{
					foreach ($arResult['SOURCE_FIELD_ENUM'] as $fieldEnum)
					{
						if ($fieldEnum['SOURCE'] === $selectedTypeId && ($availableTypes === null || isset($availableTypes[$fieldEnum['TYPE']])))
						{
							$isSelected = (!$isTagPlaceholder && !$isAttributePlaceholder && $fieldEnum['ID'] === $attributeValue['SOURCE_FIELD']);

							?>
							<option value="<?= $fieldEnum['ID'] ?>" <?= $isSelected ? 'selected': ''; ?>><?= $fieldEnum['VALUE']; ?></option>
							<?
						}
					}
				}
				?>
			</select>
			<?
		}
		?>
	</td>
	<td>
		<?
		if ($isAttribute)
		{
			if (!$attribute->isRequired() && !$attribute->isVisible())
			{
				?>
				<button class="adm-btn js-param-node-collection__item-delete spacing--1x3" type="button">-</button>
				<?
			}
		}
		else // is tag value
		{
			if ($tag->isMultiple() || (!$tag->isRequired() && !$tag->isVisible()))
			{
				?>
				<button class="adm-btn js-param-tag-collection__item-delete <?= $tag->isRequired() && count($tagValues) <= 1 ? 'is--hidden' : ''; ?> spacing--1x3" type="button">-</button>
				<?
			}
		}
		?>
	</td>
</tr>