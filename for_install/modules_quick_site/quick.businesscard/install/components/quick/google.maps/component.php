<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=<?=($arParams["TOUCH"] == "Y" ? "true" : "false")?>&key=<?=$arParams["KEY"]?>"></script>
<script> 
jQuery(function () {
	var map = new google.maps.Map(document.getElementById("<?=$arParams["CONTAINER_ID"]?>"), {
			center: new google.maps.LatLng(<?=(!empty($arParams["LATITUDE_CENTER_MAP"]) ? $arParams["LATITUDE_CENTER_MAP"] : $arParams["LATITUDE"])?>,  <?=(!empty($arParams["LONGITUDE_CENTER_MAP"]) ? $arParams["LONGITUDE_CENTER_MAP"] : $arParams["LONGITUDE"])?>),
			zoom:  <?=$arParams["ZOOM"]?>,
			scrollwheel:  <?=($arParams["SCROLLWHEEL"] == "Y" ? "true" : "false")?>,
			mapTypeControl: true,
			zoomControl: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DEFAULT
			},
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.DEFAULT
			},
		});	
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(<?=$arParams["LATITUDE"]?>, <?=$arParams["LONGITUDE"]?>),
			map: map,
			<?if(!empty($arParams["MARKER_IMAGE_FILE"])):?>
			icon: '<?=$arParams["MARKER_IMAGE_FILE"]?>',
			<?endif?>
			title: '<?=$arParams["TITLE"]?>',
		});
	 <?if(!empty($arParams["CONTENT"])):?>
		var infowindow = new google.maps.InfoWindow({content:'<?=html_entity_decode($arParams["CONTENT"])?>', pixelOffset:{height:<?=(empty($arParams["CONTENT_OFFSET_TOP"]) ? 0 : $arParams["CONTENT_OFFSET_TOP"])?>, width:<?=(empty($arParams["CONTENT_OFFSET_RIGHT"]) ? 0 : $arParams["CONTENT_OFFSET_RIGHT"])?>}});
		<?if($arParams["CONTENT_SHOW_ONLOAD"] == "Y"):?>infowindow.open(map, marker);<?endif?>
		google.maps.event.addListener(marker, 'click', function () {
			infowindow.open(map, marker);
		});
	 <?endif?>
	 <?if(!empty($arParams["STYLES"])):?>
		var styledMap = new google.maps.StyledMapType(<?=htmlspecialchars_decode($arParams["STYLES"], ENT_QUOTES)?>, {
				name: 'map_style_<?=$arParams["CONTAINER_ID"]?>'
			});
		map.mapTypes.set('map_style_<?=$arParams["CONTAINER_ID"]?>', styledMap);
		map.setMapTypeId('map_style_<?=$arParams["CONTAINER_ID"]?>');
	 <?endif?>
});
</script>
<div id="<?=$arParams["CONTAINER_ID"]?>" class="<?=$arParams["CONTAINER_CLASS"]?>"></div>