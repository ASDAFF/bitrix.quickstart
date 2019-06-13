<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;
use Yandex\Market;

$lang = [
 	'TOGGLE' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FORM_FIELD_SALES_NOTES_TOGGLE')
];
$langStatic = [
	'PLACEHOLDER' => Loc::getMessage('YANDEX_MARKET_T_ADMIN_FORM_FIELD_SALES_NOTES_PLACEHOLDER')
];

$this->addExternalCss('/bitrix/css/yandex.market/ui/input/hiddeninput.css');
$this->addExternalJs('/bitrix/js/yandex.market/ui/input/hiddeninput.js');
$this->addExternalJs('/bitrix/js/yandex.market/ui/input/symbolcount.js');

$maxLength = 50;

?>
<span class="b-hidden-input js-plugin" data-plugin="Ui.Input.HiddenInput" data-lang-prefix="YANDEX_MARKET_SALES_NOTES_">
	<span class="b-hidden-input__control js-plugin" data-plugin="Ui.Input.SymbolCount" data-max="<?= $maxLength; ?>">
		<input class="adm-input <?= $arParams['CHILD_CLASS_NAME']; ?> js-hidden-input__input js-symbol-count__input" placeholder="<?= $langStatic['PLACEHOLDER']; ?>" size="50" maxlength="<?= $maxLength; ?>" data-name="SALES_NOTES" <?
			if (!$arParams['PLACEHOLDER'])
			{
				echo ' name="' . $arParams['INPUT_NAME'] . '"';
				echo ' value="' . $arParams['VALUE'] . '"';
			}
		?> />
		<span class="js-symbol-count__value"><?= !$arParams['HAS_VALUE'] ? $maxLength : ($maxLength - strlen($arParams['VALUE'])); ?></span>
	</span>
	<label class="b-hidden-input__toggle b-link action--heading target--inside js-hidden-input__label" tabindex="0"><?= $arParams['HAS_VALUE'] ? $arParams['VALUE'] : $lang['TOGGLE']; ?></label><?

	if (!empty($arParams['SALES_NOTES_TIP']))
	{
		?>
		<span class="b-hidden-input__addition b-icon icon--question indent--left b-tag-tooltip--holder">
			<span class="b-tag-tooltip--content b-tag-tooltip--content_right">
				<?= $arParams['SALES_NOTES_TIP']; ?>
			</span>
		</span>
		<?
	}
	?>
</span>
<script>
	(function() {
		var utils = BX.namespace('YandexMarket.Utils');

		utils.registerLang(
			<?= Market\Utils::jsonEncode($lang, JSON_UNESCAPED_UNICODE); ?>,
			'YANDEX_MARKET_SALES_NOTES_'
		);
	})();
</script>
