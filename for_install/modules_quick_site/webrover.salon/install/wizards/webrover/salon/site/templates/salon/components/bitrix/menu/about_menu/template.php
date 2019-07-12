<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if (!empty($arResult)): ?>
	<h2>О салоне</h2>
	<div class="submenu">
		<ul>
			<? foreach($arResult as $arItem): ?>
				<li><a title="<?=$arItem["TEXT"]?>" href="<?=$arItem["LINK"]?>" class="<?=($arItem['SELECTED'] ? 'selected' : '')?>"><?=$arItem["TEXT"]?></a></li>
			<? endforeach ?>
		</ul>
	</div>
<? endif ?>