<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (!empty($arResult)):
?>
	<nav class="menu_v">
		<div class="menu_v__title"><?=$arParams['BLOCK_TITLE']?></div>
        <ul>
		<?php foreach ($arResult as $arMenu): ?>
			<li class="menu_v__item"><a href="<?=$arMenu['LINK']?>"><span><?=$arMenu['TEXT']?></span></a></li>
		<?php endforeach; ?>
        </ul>
	</nav>
<?
endif;