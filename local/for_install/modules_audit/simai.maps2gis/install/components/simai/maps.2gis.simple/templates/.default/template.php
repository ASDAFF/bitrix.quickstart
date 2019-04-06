<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript" src="http://maps.api.2gis.ru/1.0"></script>

<script type="text/javascript">
    // 2GIS maps JavaScripts
    var map2gis_<?=$arResult['MAP2GIS_ID']?> = null;        // map itself
    var map2gis_init_point_count_<?=$arResult['MAP2GIS_ID']?> = <?=$arResult['POINTS_COUNT']?>; // initial points count
    //
    function map2gis_balloonClick_<?=$arResult['MAP2GIS_ID']?>( mouse_event, marker )
    {
        this.showBalloon();
    }
    //
    function map2gis_addMarker_<?=$arResult['MAP2GIS_ID']?>( lat, lon, name, icon_url, icon_w, icon_h )
    {
        var marker = null;
        var oIcon = null;
        if( icon_url != null )
        {
            oIcon = new DG.Icon( icon_url, new DG.Size(icon_w, icon_h) );
        }
        marker = new DG.Markers.MarkerWithBalloon( {
            geoPoint:      new DG.GeoPoint( lon, lat ),
            icon:          oIcon,
            clickCallback: map2gis_balloonClick_<?=$arResult['MAP2GIS_ID']?>,
            balloonOptions: {
                showLatestOnly:    true,
                isClosed:          true,
                contentHtml:       name
            }
        } );
        map2gis_<?=$arResult['MAP2GIS_ID']?>.markers.add( marker );
        if( map2gis_init_point_count_<?=$arResult['MAP2GIS_ID']?> == 1 )
        {
            marker.showBalloon();
        }
    }
    //
    // page load handler:
    DG.autoload( function() {
        // map object
        var map_2g = new DG.Map( 'map2gis_map_<?=$arResult['MAP2GIS_ID']?>' );
        map_2g.setCenter( new DG.GeoPoint(<?=$arResult['CENTER_POINT_LON']?>,
            <?=$arResult['CENTER_POINT_LAT']?>), <?=$arResult['MAP_ZOOM']?> );
<?php if( $arResult['MAP_CONTROL_ZOOM'] == 'Y' ) { ?>
        map_2g.controls.add( new DG.Controls.Zoom() );
<?php } ?>
<?php if( $arResult['MAP_CONTROL_DBLCLICK_ZOOM'] == 'N' ) { ?>
        map_2g.disableDblClickZoom();
<?php } ?>
<?php if( $arResult['MAP_CONTROL_FULLSCREEN_BUTTON'] == 'N' ) { ?>
        map_2g.fullscreen.disable();
<?php } ?>
<?php if( $arResult['MAP_CONTROL_GEOCLICKER'] == 'Y' ) { ?>
        map_2g.geoclicker.enable();
<?php } ?>
<?php if( $arResult['MAP_CONTROL_RIGHTBUTTON_MAGNIFIER'] == 'Y' ) { ?>
        map_2g.enableRightButtonMagnifier();
<?php } ?>
        map2gis_<?=$arResult['MAP2GIS_ID']?> = map_2g;
<?php if( count($arResult['POINTS']) > 0 ) {
foreach( $arResult['POINTS'] as $POINT ) { ?>
        map2gis_addMarker_<?=$arResult['MAP2GIS_ID']?>( <?=$POINT['LAT']?>, <?=$POINT['LON']?>, '<?=$POINT['TEXT']?>' );
<?php } } ?>
    } );
</script>


<!-- 2GIS map -->
<div id="map2gis_map_<?=$arResult['MAP2GIS_ID']?>"
     style="width: <?=$arResult['MAP_WIDTH']?>px; height: <?=$arResult['MAP_HEIGHT']?>px">
    <?php echo GetMessage('2GIS_MAP_LOADING'); ?>
</div>

