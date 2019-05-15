<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<div id="vkontakte-like<?=$arResult['SUFFIX']?>"></div>
<script type="text/javascript">
	VK.Widgets.Like("vkontakte-like<?=$arResult['SUFFIX']?>", <?=$arResult['OPTIONS']?>);
</script>
