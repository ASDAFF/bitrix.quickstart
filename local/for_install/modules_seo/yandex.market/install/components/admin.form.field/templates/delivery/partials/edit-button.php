<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

use Bitrix\Main\Localization\Loc;

?>
<span class="b-form-pill-group__empty">
	<button class="b-field-delivery__edit-button adm-btn js-delivery-summary__edit-button" type="button">
		<?= $arParams['EDIT_BUTTON_TITLE'] ?: Loc::getMessage('YANDEX_MARKET_T_ADMIN_FIELD_DELIVERY_ROW_ADD'); ?>
	</button>
</span>
