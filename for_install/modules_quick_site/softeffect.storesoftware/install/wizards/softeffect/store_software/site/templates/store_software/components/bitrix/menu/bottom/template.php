<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (!empty($arResult)) { ?>
	<div class="links noprint">
		<? foreach($arResult as $key=>$arItem) { ?><? if ($key!=0) { ?>&nbsp;|&nbsp;<? } ?><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><? } ?>
	</div>
<? } ?>