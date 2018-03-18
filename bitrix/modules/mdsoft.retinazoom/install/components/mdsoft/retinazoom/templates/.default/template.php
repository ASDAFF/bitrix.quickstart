<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (count($arResult["ITEMS"]) < 1)
	return;
?>

<center>
<noindex>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<img class="a" id="zoome_img" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" rel="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>"  width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"/>
<?endforeach;?>
</noindex>
</center>

<script type="text/javascript">
	$(function(){
	$('#zoome_img').zoome({hoverEf:'<?=$arParams["EFFECT"];?>',showZoomState:true,defaultZoom:<?=$arParams['ZOOM']?>,magnifierSize:[<?=$arParams["BOX_SIZE"];?>,<?=$arParams["BOX_SIZE"];?>]});
});
	function destroyZoome(obj){
		if(obj.parent().hasClass('zm-wrap'))
		{
			obj.unwrap().next().remove();
		}
	}
</script>	