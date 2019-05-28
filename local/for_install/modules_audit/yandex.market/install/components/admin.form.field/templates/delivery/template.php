<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market;

// assets

$this->addExternalJs('/bitrix/js/yandex.market/lib/editdialog.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/delivery/summary.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/delivery/collection.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/delivery/item.js');

$lang = $arResult['LANG'];

$langStatic = [
	'HELP' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HELP'),
	'HELP_DETAILS' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_HELP_DETAILS'),
	'WARNING' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_WARNING'),
];

// output

$APPLICATION->SetPageProperty($arParams['INPUT_NAME'] . '_IS_FILL_CLASS_NAME', $arResult['HAS_SUMMARY'] ? 'is--fill' : 'is--empty');

?>
<div class="b-field-delivery <?= $arParams['GROUP_OUTSIDE'] !== 'Y' ? 'b-form-pill-group' : ''; ?> <?= $arParams['HAS_ERROR'] ? 'is--invalid' : ''; ?> <?= $arResult['HAS_SUMMARY'] ? 'is--fill' : 'is--empty'; ?> <?= $arParams['CHILD'] ? $arParams['CHILD_CLASS_NAME'] : 'js-plugin'; ?>" data-plugin="Field.Delivery.Summary" <?= $arParams['CHILD'] ? '' : 'data-base-name="' . $arParams['INPUT_NAME'] .'"';  ?> data-name="DELIVERY">
	<?
	include __DIR__ . '/partials/summary.php';
	include __DIR__ . '/partials/edit-button.php';
	?>
	<div class="is--hidden js-delivery-summary__edit-modal">
		<?
		include __DIR__ . '/partials/table.php';
		?>
		<div class="b-admin-message-list">
			<?
			\CAdminMessage::ShowMessage([
				'TYPE' => 'OK',
				'MESSAGE' => $langStatic['HELP'],
				'DETAILS' => $langStatic['HELP_DETAILS'],
				'HTML' => true
			]);

			\CAdminMessage::ShowMessage([
				'TYPE' => 'ERROR',
				'MESSAGE' => $langStatic['WARNING'],
				'HTML' => true
			])
			?>
		</div>
	</div>
</div>
<script>
	(function() {
		var utils = BX.namespace('YandexMarket.Utils');

		utils.registerLang(<?= Market\Utils::jsonEncode($lang, JSON_UNESCAPED_UNICODE); ?>, 'YANDEX_MARKET_FIELD_DELIVERY_');
	})();
</script>