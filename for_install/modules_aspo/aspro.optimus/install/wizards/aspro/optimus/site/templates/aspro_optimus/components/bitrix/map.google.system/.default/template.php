<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$frame = $this->createFrame()->begin('');
$arAllMapOptions = array_merge($arResult['ALL_MAP_OPTIONS'], $arResult['ALL_MAP_CONTROLS']);
$arMapOptions = array_merge($arParams['OPTIONS'], $arParams['CONTROLS']);
?>

<script type="text/javascript">
	if (!window.GLOBAL_arMapObjects)
		window.GLOBAL_arMapObjects = {};

	function initCustomControls(map){
		$('.map-wr').each(function(){
			var zoomIn = $(this).find('.zoomIn'),
				zoomOut = $(this).find('.zoomOut');
			zoomIn.on('click', function(){
				map.setZoom(map.getZoom() + 1);
			});
			zoomOut.on('click', function() {
				map.setZoom(map.getZoom() - 1);
			});
		});
	}

	function init_<?echo $arParams['MAP_ID']?>(){
		if (!window.google && !window.google.maps)
			return;

		var opts = {
			zoom: <?echo $arParams['INIT_MAP_SCALE']?>,
			center: new google.maps.LatLng(<?echo $arParams['INIT_MAP_LAT']?>, <?echo $arParams['INIT_MAP_LON']?>),
			<?foreach ($arAllMapOptions as $option => $method){

				echo "\t\t".(
					in_array($option, $arMapOptions)
					? str_replace(array('#true#', '#false#'), array('true', 'false'), $method)
					: str_replace(array('#false#', '#true#'), array('true', 'false'), $method)
				).",\r\n";
			}?>
			mapTypeId: google.maps.MapTypeId.<?echo $arParams['INIT_MAP_TYPE']?>,
			streetViewControl: false,
			panControl : false,

		};
		
		window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'] = new window.google.maps.Map(BX("BX_GMAP_<?echo $arParams['MAP_ID']?>"), opts);
		initCustomControls(window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>']);
		
		google.maps.event.addDomListener(window, "resize", function() {
		   var center = window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'].getCenter();
		   google.maps.event.trigger(window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'], "resize");
		   window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'].setCenter(center); 
		});

		<?if ($arParams['DEV_MODE'] == 'Y'){?>
			window.bGoogleMapScriptsLoaded = true;
		<?}?>
	}

	<?if ($arParams['DEV_MODE'] == 'Y'){?>
		function BXMapLoader_<?echo $arParams['MAP_ID']?>(MAP_KEY){
			if (null == window.bGoogleMapScriptsLoaded){
				if (window.google && window.google.maps){
					window.bGoogleMapScriptsLoaded = true;
					BX.ready(init_<?echo $arParams['MAP_ID']?>);
				}else{
					if(window.bGoogleMapsScriptLoading){
						window.bInt<?echo $arParams['MAP_ID']?> = setInterval(
							function(){
								if(window.bGoogleMapScriptsLoaded){
									clearInterval(window.bInt<?echo $arParams['MAP_ID']?>);
									init_<?echo $arParams['MAP_ID']?>();
								}else
									return;
							},
							500
						);
						return;
					}

					window.bGoogleMapsScriptLoading = true;

					<?$scheme = (CMain::IsHTTPS() ? "https" : "http");?>

					BX.loadScript(
						'<?=$scheme?>://www.google.com/jsapi?rnd=' + Math.random(),
						function (){
							if (BX.browser.IsIE())
								setTimeout("window.google.load('maps', <?= intval($arParams['GOOGLE_VERSION'])?>, {callback: init_<?echo $arParams['MAP_ID']?>, other_params: 'sensor=false&language=<?= LANGUAGE_ID?>'})", 1000);
							else
								google.load('maps', <?echo intval($arParams['GOOGLE_VERSION'])?>, {callback: init_<?echo $arParams['MAP_ID']?>, other_params: 'sensor=false&language=<?=LANGUAGE_ID?>'});
						}
					);
				}
			}else{
				init_<?echo $arParams['MAP_ID']?>();
			}
		}
		<?if (!$arParams['WAIT_FOR_EVENT']){?>
			BXMapLoader_<?echo $arParams['MAP_ID']?>('<?echo $arParams['KEY']?>');
		<?}else{
			echo CUtil::JSEscape($arParams['WAIT_FOR_EVENT']),' = BXMapLoader_',$arParams['MAP_ID'],';';
		};
	}else{?>
		BX.ready(init_<?echo $arParams['MAP_ID']?>);
	<?};?>

	/* if map inits in hidden block (display:none),
	*  after the block showed,
	*  for properly showing map this function must be called
	*/
	function BXMapGoogleAfterShow(mapId){
		if(google.maps !== undefined && window.GLOBAL_arMapObjects[mapId] !== undefined)
			google.maps.event.trigger(window.GLOBAL_arMapObjects[mapId],'resize');
	}
</script>
<div id="BX_GMAP_<?=$arParams['MAP_ID'];?>" class="bx-google-map" style="height: <?=$arParams['MAP_HEIGHT'];?>; width: <?=$arParams['MAP_WIDTH']?>;"><?=GetMessage('MYS_LOADING'.($arParams['WAIT_FOR_EVENT'] ? '_WAIT' : ''));?></div>