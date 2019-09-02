<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<img id="<?=$arParams['IMAGE_ID']; ?>" class="a" src="<?= $arParams['ELEMENT_URL']; ?>" alt="<?= $arParams['IMAGE_ALT']; ?>" title="<?= $arParams['IMAGE_TITLE']; ?>" width="<?= $arParams['IMAGE_WIDTH']; ?>" height="<?= $arParams['IMAGE_HEIGHT']; ?>"/>

<script type="text/javascript">
	$(function(){
	$('#<?=$arParams['IMAGE_ID']; ?>').zoome({hoverEf:'',showZoomState:true,defaultZoom:<?=$arParams['ZOOM']?>,magnifierSize:[<?=$arParams["BOX_SIZE"];?>,<?=$arParams["BOX_SIZE"];?>]});
});
	function destroyZoome(obj){
		if(obj.parent().hasClass('zm-wrap'))
		{
			obj.unwrap().next().remove();
		}
	}
</script>	