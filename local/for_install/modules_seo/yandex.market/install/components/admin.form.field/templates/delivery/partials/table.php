<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;

if ($arParams['MULTIPLE'])
{
	$this->addExternalJs('/bitrix/js/yandex.market/ui/input/checkboxradio.js');
}

?>
<table class="b-delivery-table js-delivery-summary__field" data-plugin="<?= $arParams['MULTIPLE'] ? 'Field.Delivery.Collection' : 'Field.Delivery.Item'; ?>">
	<thead>
		<tr>
			<th class="b-delivery-table__header">
				<?
				echo Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_NAME');

				?><span class="b-icon icon--question indent--left b-tag-tooltip--holder">
					<span class="b-tag-tooltip--content b-tag-tooltip--content_right"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_NAME_TOOLTIP'); ?></span>
				</span>
			</th>
			<th class="b-delivery-table__header">
				<?
				echo Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_ORDER_BEFORE');

				?><span class="b-icon icon--question indent--left b-tag-tooltip--holder">
					<span class="b-tag-tooltip--content b-tag-tooltip--content_right"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_ORDER_BEFORE_TOOLTIP'); ?></span>
				</span>
			</th>
			<th class="b-delivery-table__header">
				<?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PERIOD'); ?>
				<span class="b-delivery-table__header-note"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PERIOD_NOTE'); ?></span><?
				?><span class="b-icon icon--question indent--left b-tag-tooltip--holder">
					<span class="b-tag-tooltip--content b-tag-tooltip--content_right"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PERIOD_TOOLTIP'); ?></span>
				</span>
			</th>
			<th class="b-delivery-table__header">
				<?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PRICE'); ?>
				<span class="b-delivery-table__header-note"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PRICE_NOTE'); ?></span><?
				?><span class="b-icon icon--question indent--left b-tag-tooltip--holder">
					<span class="b-tag-tooltip--content"><?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_PRICE_TOOLTIP'); ?></span>
				</span>
			</th>
			<th class="b-delivery-table__header">
				<?= Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HEADER_DELIVERY_TYPE'); ?>
			</th>
			<?
			if ($arParams['MULTIPLE'])
			{
				?>
				<th class="b-delivery-table__header-holder"></th>
				<?
			}
			?>
		</tr>
	</thead>
	<tbody <?= $arParams['MULTIPLE'] ? 'class="js-plugin" data-plugin="Ui.Input.CheckboxRadio"' : ''; ?>>
		<?
		if (!$arParams['MULTIPLE'])
		{
			$itemInputName = $arParams['INPUT_NAME'];
			$isItemPlaceholder = ($arParams['PLACEHOLDER']);

			?>
			<tr>
				<?
				include __DIR__ . '/item-content.php';
				?>
			</tr>
			<?
		}
		else
		{
			foreach ($arParams['VALUE'] as $itemIndex => $itemValue)
			{
				$itemInputName = $arParams['INPUT_NAME'] . '[' . $itemIndex . ']';
				$isItemPlaceholder = ($arParams['PLACEHOLDER'] || !empty($itemValue['PLACEHOLDER']));

				?>
				<tr class="<?= $isItemPlaceholder ? 'is--hidden' : '' ?> js-delivery-collection__item" data-plugin="Field.Delivery.Item">
					<?
					include __DIR__ . '/item-content.php';
					?>
				</tr>
				<?
			}
		}
		?>
	</tbody>
</table>
