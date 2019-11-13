<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript" src="//vk.com/js/api/openapi.js?105"></script>

<script type="text/javascript">
	VK.init({apiId: <?=$arParams["API_ID"];?>, onlyWidgets: true});
</script>


<div id="vk_comments"></div>
<script type="text/javascript">
	VK.Widgets.Comments("vk_comments", {
							limit: <?=$arParams["COM_AMMOUNT"];?>, 
							width: <?=$arParams["WIDTH"];?>, 
							height: <?=$arParams["HEIGHT"];?>,
							attach: "<?=$arResult["ATTACHMENTS"];?>",
							autoPublish: <?=$arResult["AUTO_PB"];?>,
							norealtime: <?=$arResult["NO_REAL_TIME"];?>}
						);
</script>

