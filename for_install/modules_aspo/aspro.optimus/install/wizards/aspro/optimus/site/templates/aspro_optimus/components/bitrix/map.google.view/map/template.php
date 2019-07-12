<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//$this->setFrameMode(true);
global $APPLICATION;
$frame = $this->createFrame()->begin('');
$frame->setAnimation(true);
$arTransParams = array(
	'INIT_MAP_TYPE' => $arParams['INIT_MAP_TYPE'],
	'INIT_MAP_LON' => $arResult['POSITION']['google_lon'],
	'INIT_MAP_LAT' => $arResult['POSITION']['google_lat'],
	'INIT_MAP_SCALE' => $arResult['POSITION']['google_scale'],
	'MAP_WIDTH' => $arParams['MAP_WIDTH'],
	'MAP_HEIGHT' => $arParams['MAP_HEIGHT'],
	'CONTROLS' => $arParams['CONTROLS'],
	'OPTIONS' => $arParams['OPTIONS'],
	'MAP_ID' => $arParams['MAP_ID'],
	'API_KEY' => $arParams['API_KEY'],
);

if ($arParams['DEV_MODE'] == 'Y'){
	$arTransParams['DEV_MODE'] = 'Y';
	if ($arParams['WAIT_FOR_EVENT'])
		$arTransParams['WAIT_FOR_EVENT'] = $arParams['WAIT_FOR_EVENT'];
}
$arParams["CLICKABLE"] = ( $arParams["CLICKABLE"] ? $arParams["CLICKABLE"] : "Y" );?>
	<div class="module-map">
		<div class="map-wr module-contacts-map-layout">
			<?if( $arParams["ZOOM_BLOCK"]["HIDDEN"] != "Y" ){?>
				<div class="controls <?=( $arParams["ZOOM_BLOCK"]["POSITION"] ? $arParams["ZOOM_BLOCK"]["POSITION"] : 'left' )?>">
					<div class="z zoomIn">+</div>
					<div class="z zoomOut">-</div>
				</div>
			<?}?>
			<?$APPLICATION->IncludeComponent('bitrix:map.google.system', '.default', $arTransParams, false, array('HIDE_ICONS' => 'Y'));?>
		</div>
	</div>
<?$APPLICATION->AddHeadScript( $this->__folder.'/markerclustererplus.js', true )?>
<?$APPLICATION->AddHeadScript( $this->__folder.'/infobox.js', true )?>
<script>
	if (!window.BX_GMapAddPlacemark_){
		window.BX_GMapAddPlacemark_ = function(markers, bounds, arPlacemark, map_id, clickable){
			var map = GLOBAL_arMapObjects[map_id];
			if (null == map) {
				return false;
			}
			if (!arPlacemark.LAT || !arPlacemark.LON) {
				return false;
			}
			var root = '<?=SITE_TEMPLATE_PATH;?>'+'/images/map_marker.png';
			var image = new google.maps.MarkerImage(root,
				new google.maps.Size(45, 57),
				new google.maps.Point(0, 0),
				new google.maps.Point(23, 57)
			);
			var pt = new google.maps.LatLng(arPlacemark.LAT, arPlacemark.LON);
            bounds.extend(pt);
			
			var obPlacemark = new google.maps.Marker({
				'position': pt,
				'map': map,
				'icon': image,
				'clickable': (clickable == "Y" ? true : false),
				'title': arPlacemark.TEXT,
				'zIndex': 5,
				'html': arPlacemark.HTML
			});
			markers.push(obPlacemark);
			
			var boxText = document.createElement('div');
			boxText.className  = 'inner';
			boxText.innerHTML = (typeof(obPlacemark.html) !== 'undefined' ? (obPlacemark.html.length ? obPlacemark.html : '') : (typeof(obPlacemark.title) !== 'undefined' ? (obPlacemark.title.length ? obPlacemark.title : '') : ''));
			if (boxText.innerHTML.length) {
				boxText.innerHTML = '<div class="wrap-big">' + boxText.innerHTML + '</div>';
			}

			var myOptions = {
				content: boxText,
				disableAutoPan: false,
				maxWidth: 0,
				alignBottom: true,
				pixelOffset: new google.maps.Size(-140, -7),
				zIndex: 99999,
				boxStyle:{minwidth: '294px', background: '#fff'},
				closeBoxMargin: '0',
				infoBoxClearance: new google.maps.Size(1, 1),
				isHidden: false,
				pane: 'floatPane',
				enableEventPropagation: false,
				position: obPlacemark.position
				
			};
			if( clickable && boxText.innerHTML.length){
				ib = new InfoBox();
				google.maps.event.addListener(obPlacemark, 'click', function (e){
					if (null != window['__bx_google_infowin_opened_' + map_id]){
						window['__bx_google_infowin_opened_' + map_id].close();
					}
					ib.setOptions(myOptions);
					ib.open(this.map, this);
					window['__bx_google_infowin_opened_' + map_id] = ib;
				});
				
				/*custom close*/
				var oldClose = ib.close;
				ib.close = function(){
					$(ib.div_).fadeOut('100');
					var th = this;
					oldClose.apply(th);
				}
				
				/*custom draw*/
				var oldDraw = ib.draw;
				ib.draw = function(){
					oldDraw.apply(this);
					$(ib.div_).hide();
					$(ib.div_).fadeIn('100');
				}
			}
			google.maps.event.addListener(map, "click", function() {
				ib.close(); 
			});
			var icon = '<?=SITE_TEMPLATE_PATH?>/images/map_marker.png';
			var iconHover = '<?=SITE_TEMPLATE_PATH?>/images/map_marker.png';
			google.maps.event.addListener(obPlacemark, 'mouseover', function() {
				obPlacemark.set("opacity","0.9");
			});
			google.maps.event.addListener(obPlacemark, 'mouseout', function() {
				obPlacemark.set("opacity","1");
			});
			if (BX.type.isNotEmptyString(arPlacemark.TEXT)){
				obPlacemark.infowin = new google.maps.InfoWindow({
					content: "Loading..."
				});
				
			}
			return obPlacemark;
		}
	}

	if (null == window.BXWaitForMap_view){
		function BXWaitForMap_view(map_id){
			if (null == window.GLOBAL_arMapObjects)
				return;
		
			if (window.GLOBAL_arMapObjects[map_id])
				window['BX_SetPlacemarks_' + map_id]();
			else
				setTimeout('BXWaitForMap_view(\'' + map_id + '\')', 300);
		}
	}
</script>
<?if (is_array($arResult['POSITION']['PLACEMARKS']) && ($cnt = count($arResult['POSITION']['PLACEMARKS']))):?>
	<script type="text/javascript">
		function BX_SetPlacemarks_<?echo $arParams['MAP_ID']?>(){
			var markers = [];
			var bounds = new google.maps.LatLngBounds();
			<?for($i = 0; $i < $cnt; $i++):?>
				BX_GMapAddPlacemark_(markers, bounds, <?echo CUtil::PhpToJsObject($arResult['POSITION']['PLACEMARKS'][$i])?>, '<?echo $arParams['MAP_ID']?>', '<?=$arParams["CLICKABLE"];?>');
			<?endfor;?>
			<?if( $arParams["ORDER"] != "Y" ){?>
				/*cluster icon*/
				var map = window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'];
				var clusterOptions = {
					zoomOnClick: true,
					averageCenter: true,
					clusterClass: 'test',
					styles: [{
						url: '<?=SITE_TEMPLATE_PATH?>'+'/images/map_cluster.png',
						height: 53, 
						width: 53,
						textColor: '#383838',
						textSize: 12,
						fontFamily: 'Ubuntu'
					}]
				}
				var markerCluster = new MarkerClusterer(map, markers, clusterOptions);
				var clusterIcons = '<?=SITE_TEMPLATE_PATH?>/images/map_cluster.png';
				var clusterIconsHover = '<?=SITE_TEMPLATE_PATH?>/images/map_cluster.png';
				google.maps.event.addListener(markerCluster, "mouseout", function (c) {
				  c.clusterIcon_.div_.firstChild.src = clusterIcons;
				});
				google.maps.event.addListener(markerCluster, "mouseover", function (c) {
				  c.clusterIcon_.div_.firstChild.src = clusterIconsHover;
				});
			
				center = bounds.getCenter();
				<?if( $cnt > 1 ){?>
					map.fitBounds(bounds);
				<?}else{
					$map_data = unserialize($arParams["MAP_DATA"]);?>
					//map.SetZoom(<?=$map_data["google_scale"]?>);
				<?}?>
			<?}?>
			
			/*reinit map*/
			//google.maps.event.trigger(map,'resize');
		}

		function BXShowMap_<?echo $arParams['MAP_ID']?>() {
			BXWaitForMap_view('<?echo $arParams['MAP_ID']?>');
		}

		BX.ready(BXShowMap_<?echo $arParams['MAP_ID']?>);
	</script>
<?endif;?>