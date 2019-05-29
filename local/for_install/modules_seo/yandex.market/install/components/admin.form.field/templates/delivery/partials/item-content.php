<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Yandex\Market;

/** @var $itemInputName string */
/** @var $itemValue array */
/** @var $isItemPlaceholder boolean */
/** @var $lang array */
/** @var $langStatic array */

?>
<td class="b-delivery-table__cell">
	<input class="js-delivery-item__input" type="hidden" data-name="ID" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[ID]"';
			echo ' value="' . $itemValue['ID'] . '"';
		}

	?> />
	<input class="adm-input control-box--delta js-delivery-item__input" type="text" size="15" data-name="NAME" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[NAME]"';
			echo ' value="' . $itemValue['NAME'] . '"';
		}

	?> />
</td>
<td class="b-delivery-table__cell">
	<input class="adm-input control-box--delta b-reset-number js-delivery-item__input" type="number" min="0" max="99" step="1" data-name="ORDER_BEFORE" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[ORDER_BEFORE]"';
			echo ' value="' . $itemValue['ORDER_BEFORE'] . '"';
		}

	?> />
</td>
<td class="b-delivery-table__cell">
	<input class="b-delivery-table__input-count adm-input control-box--delta b-reset-number js-delivery-item__input" type="number" min="0" max="99" step="1" data-name="PERIOD_FROM" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[PERIOD_FROM]"';
			echo ' value="' . $itemValue['PERIOD_FROM'] . '"';
		}

	?> /><?
	?>&nbsp;&mdash;&nbsp;<?
	?><input class="b-delivery-table__input-count adm-input b-reset-number control-box--delta js-delivery-item__input" type="number" min="0" max="99" step="1" data-name="PERIOD_TO" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[PERIOD_TO]"';
			echo ' value="' . $itemValue['PERIOD_TO'] . '"';
		}

	?> /><?
	?>
</td>
<td class="b-delivery-table__cell">
	<input class="adm-input control-box--delta b-reset-number js-delivery-item__input" type="number" min="0" max="999999" data-name="PRICE" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[PRICE]"';
			echo ' value="' . $itemValue['PRICE'] . '"';
		}

	?> />
</td>
<td class="b-delivery-table__cell">
	<input class="is--persistent js-delivery-item__input" type="hidden" value="<?= Market\Export\Delivery\Table::DELIVERY_TYPE_DELIVERY; ?>" data-name="DELIVERY_TYPE" <?

		if (!$isItemPlaceholder)
		{
			echo ' name="' . $itemInputName . '[DELIVERY_TYPE]"';
		}

	?> />
	<label>
		<input class="adm-designed-checkbox js-delivery-item__input <?= $arParams['MULTIPLE'] ? 'js-checkbox-radio__input' : ''; ?>" type="checkbox" data-name="DELIVERY_TYPE" value="<?= Market\Export\Delivery\Table::DELIVERY_TYPE_PICKUP; ?>" <?

			if (!$isItemPlaceholder)
			{
				echo ' name="' . $itemInputName . '[DELIVERY_TYPE]"';

				if ($itemValue['DELIVERY_TYPE'] === Market\Export\Delivery\Table::DELIVERY_TYPE_PICKUP)
				{
					echo ' checked';
				}
			}

		?> />
		<span class="adm-designed-checkbox-label"></span>
	</label>
</td>
<?
if ($arParams['MULTIPLE'])
{
	?>
	<td class="b-delivery-table__cell">
		<button class="adm-btn adm-filter-add-button b-delivery-table__action-math control-box--delta js-delivery-collection__item-add"></button>
		<button class="adm-btn adm-filter-item-delete b-delivery-table__action-math control-box--delta js-delivery-collection__item-delete"></button>
	</td>
	<?
}
