<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="footmenu">
	<ul class="horizontal">
		<? foreach($arResult as $arItem): ?>
			<li><a href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>"><?=$arItem["TEXT"]?></a></li>
		<? endforeach ?>
	</ul>
	<div class="cl"></div>
</div>