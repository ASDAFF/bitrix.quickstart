<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<div id="vkontakte-group<?=$arResult['SUFFIX']?>"></div>
<script type="text/javascript">
	VK.Widgets.Group("vkontakte-group<?=$arResult['SUFFIX']?>", <?=$arResult['OPTIONS']?>);
</script>
