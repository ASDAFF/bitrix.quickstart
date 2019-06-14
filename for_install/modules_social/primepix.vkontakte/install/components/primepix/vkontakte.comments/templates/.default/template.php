<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<div id="vkontakte-comments<?=$arResult['SUFFIX']?>"></div>
<script type="text/javascript">
	VK.Widgets.Comments("vkontakte-comments<?=$arResult['SUFFIX']?>", <?=$arResult['OPTIONS']?>);
</script>