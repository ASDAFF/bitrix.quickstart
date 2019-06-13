<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** @var $langStatic array */
/** @var $collectionAddButtonId string|null */

if ($arParams['EXPORT_ADD_BUTTON']) { $this->SetViewTarget($arParams['EXPORT_ADD_BUTTON']); }

?>
<div class="spacing--1x1">
	<button class="adm-btn vertical--middle <?= $collectionAddButtonId ? '' : 'js-filter-collection__add-button'; ?>" type="button" <?= $collectionAddButtonId ? 'id="' . $collectionAddButtonId . '"' : ''; ?>>
		<?= $langStatic['ADD_BUTTON']; ?>
	</button>
	<span class="b-icon icon--question indent--left b-tag-tooltip--holder">
		<span class="b-tag-tooltip--content b-tag-tooltip--content_right"><?= $langStatic['ADD_BUTTON_TOOLTIP']; ?></span>
	</span>
</div>
<?

if ($arParams['EXPORT_ADD_BUTTON']) { $this->EndViewTarget(); }