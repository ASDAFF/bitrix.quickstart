<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<div id="vkontakte-poll"></div>
<script type="text/javascript">
	VK.Widgets.Poll("vkontakte-poll", <?=$arResult['OPTIONS']?>, "<?=$arResult['ID_POLL']?>");
</script>
