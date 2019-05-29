<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** @var $countParentClassName string */

?>
<div class="b-grid__item vertical--middle is--hidden <?= $countParentClassName; ?>__count"></div>
<div class="b-grid__item vertical--middle">
	<span class="b-message type--error icon--left is--hidden <?= $countParentClassName; ?>__count-message">
		<span class="b-message__icon"></span><?
		?><span class="<?= $countParentClassName; ?>__count-message-text"></span>
	</span>
</div>