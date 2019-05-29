<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market;

// assets

CJSCore::Init([ 'popup' ]);

$this->addExternalCss('/bitrix/js/yandex.market/lib/chosen/chosen.css');
$this->addExternalJs('/bitrix/js/yandex.market/lib/chosen/chosen.jquery.js');
$this->addExternalJs('/bitrix/js/yandex.market/lib/editdialog.js');
$this->addExternalJs('/bitrix/js/yandex.market/ui/input/taginput.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/condition/summary.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/condition/collection.js');
$this->addExternalJs('/bitrix/js/yandex.market/field/condition/item.js');

$lang = [
	'MODAL_TITLE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_MODAL_TITLE'),
	'PLACEHOLDER' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_SUMMARY_PLACEHOLDER'),
	'JUNCTION' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_SUMMARY_JUNCTION'),
	'COUNT' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_COUNT'),
	'COUNT_PROGRESS' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_COUNT_PROGRESS'),
	'COUNT_FAIL' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_COUNT_FAIL'),
	'COUNT_EMPTY' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_COUNT_EMPTY'),
	'PRODUCT_1' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PRODUCT_1'),
	'PRODUCT_2' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PRODUCT_2'),
	'PRODUCT_5' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PRODUCT_5'),
];
$langStatic = [
	'EDIT_BUTTON' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_SUMMARY_EDIT'),
	'FIELD_FIELD' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_FIELD_FIELD'),
	'PLACEHOLDER_FIELD' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PLACEHOLDER_FIELD'),
	'FIELD_COMPARE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_FIELD_COMPARE'),
	'PLACEHOLDER_COMPARE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PLACEHOLDER_COMPARE'),
	'FIELD_VALUE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_FIELD_VALUE'),
	'PLACEHOLDER_VALUE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_PLACEHOLDER_VALUE'),
	'NO_VALUE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_NO_VALUE'),
	'ADD_BUTTON' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_CONDITION_ADD_BUTTON'),
];
$langChosen = [
	'CHOSEN_PLACEHOLDER' => $langStatic['PLACEHOLDER_VALUE'],
	'CHOSEN_NO_RESULTS' => $langStatic['NO_VALUE']
];

// output

?>
<div class="<?= $arParams['CHILD'] ? $arParams['CHILD_CLASS_NAME'] : 'js-plugin'; ?>" data-plugin="Field.Condition.Summary" <?= $arParams['CHILD'] ? '' : 'data-base-name="' . $arParams['INPUT_NAME'] .'"';  ?> data-name="FILTER_CONDITION">
	<?
	include __DIR__ . '/partials/summary.php';
	?>
	<div class="is--hidden js-condition-summary__edit-modal">
		<?
		include __DIR__ . '/partials/table.php';
		?>
	</div>
</div>
<?
if ($APPLICATION->GetPageProperty('YANDEX_MARKET_FORM_FIELD_CONDITION_LANG') !== 'Y')
{
	$APPLICATION->SetPageProperty('YANDEX_MARKET_FORM_FIELD_CONDITION_LANG', 'Y');

	?>
	<script>
		(function() {
			var utils = BX.namespace('YandexMarket.Utils');

			utils.registerLang(<?= Market\Utils::jsonEncode($lang, JSON_UNESCAPED_UNICODE); ?>, 'YANDEX_MARKET_FIELD_CONDITION_'); // field lang
			utils.registerLang(<?= Market\Utils::jsonEncode($langChosen, JSON_UNESCAPED_UNICODE); ?>); // chosen lang
		})();
	</script>
	<?
}