<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** @var $collectionLeftCountId string|null */
/** @var $collectionLeftMessageId string|null */

// count

if ($arParams['EXPORT_LEFT_COUNT']) { $this->SetViewTarget($arParams['EXPORT_LEFT_COUNT']); }

?>
<span class="color--secondary is--hidden <?= $collectionLeftCountId ? '' : 'js-filter-collection__left-count'; ?>" href="#" <?= $collectionLeftCountId ? 'id="' . $collectionLeftCountId . '"' : ''; ?>></span>
<?

if ($arParams['EXPORT_LEFT_COUNT']) { $this->EndViewTarget(); }

// message

if ($arParams['EXPORT_LEFT_MESSAGE']) { $this->SetViewTarget($arParams['EXPORT_LEFT_MESSAGE']); }

?>
<span class="b-message type--error icon--left is--hidden <?= $collectionLeftMessageId ? '' : 'js-filter-collection__left-message'; ?>" href="#" <?= $collectionLeftMessageId ? 'id="' . $collectionLeftMessageId . '"' : ''; ?>>
	<span class="b-message__icon"></span><?
	?><span class="js-filter-collection__left-message-text"></span>
</span>
<?

if ($arParams['EXPORT_LEFT_MESSAGE']) { $this->EndViewTarget(); }