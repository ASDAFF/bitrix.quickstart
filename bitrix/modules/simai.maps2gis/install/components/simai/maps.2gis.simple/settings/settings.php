<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/simai/maps.2gis.simple/lang/'.LANGUAGE_ID.'/settings.php');

//if(!$USER->IsAdmin())
//	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage('2GIS_SET_POPUP_TITLE'),
		'SUFFIX' => 'map_2gis',
		'ARGS' => ''
	)
);

$arData = array();
if ($_REQUEST['MAP_DATA'])
{
	CUtil::JSPostUnescape();
	if( CheckSerializedData($_REQUEST['MAP_DATA']) )
	{
		$arData = unserialize( $_REQUEST['MAP_DATA'] );
		if( is_array($arData) && is_array($arData['PLACEMARKS']) && ($cnt = count($arData['PLACEMARKS'])) )
		{
			for( $i = 0; $i < $cnt; $i++ )
			{
				$arData['PLACEMARKS'][$i]['TEXT'] = str_replace('###RN###', "\r\n", $arData['PLACEMARKS'][$i]['TEXT']);
			}
		}
	}
}

$MAP2GIS_ID = str_replace( '.', '', uniqid( 'map2g_', true ) );

?>
<script type="text/javascript">
    BX.loadCSS('/bitrix/components/simai/maps.2gis.simple/settings/settings.css');
    BX.loadScript('http://maps.api.2gis.ru/1.0?loadByRequire=1', function() {
        DG.enabledLangs    = ['ru','en'];
        DG.defaultLang     = 'ru';
        DG.userDefinedLang = 'auto';
        DG.loadLib();
        setTimeout( map2gis_wait4load_<?=$MAP2GIS_ID?>, 100 );
		});
</script>
<script type="text/javascript" src="/bitrix/components/simai/maps.2gis.simple/settings/serialize_php.js"></script>

<form name="bx_popup_form_2gis_map">
<?php
$obJSPopup->ShowTitlebar();
//$obJSPopup->StartDescription('bx-edit-menu');
?>
    <p><b><?echo GetMessage('2GIS_SET_POPUP_WINDOW_TITLE')?></b></p>
    <p class="note"><?echo GetMessage('2GIS_SET_POPUP_WINDOW_DESCRIPTION')?></p>
	
<?php
$obJSPopup->StartContent();
?>

<script type="text/javascript">
    // 2GIS maps JavaScripts
    var map2gis_<?=$MAP2GIS_ID?> = null;        // map itself
    var map2gis_markers_<?=$MAP2GIS_ID?> = [];  // map markers
    //
    function map2gis_wait4load_<?=$MAP2GIS_ID?>()
    {
        if( DG.Map != undefined )
            map2gis_createMap_<?=$MAP2GIS_ID?>();
        else
            setTimeout( map2gis_wait4load_<?=$MAP2GIS_ID?>, 100 );
    }
    //
    function map2gis_balloonClick_<?=$MAP2GIS_ID?>( mouse_event, marker )
    {
        this.showBalloon();
    }
    //
    function map2gis_addMarker_<?=$MAP2GIS_ID?>( lat, lon, name, icon_url, icon_w, icon_h )
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
            clickCallback: map2gis_balloonClick_<?=$MAP2GIS_ID?>,
            balloonOptions: {
                showLatestOnly:    true,
                isClosed:          true,
                contentHtml:       name
            }
        } );
        var mid = marker._id; //marker.getid();
        var del_link = '<a href="#" onclick="map2gis_removeMarker_<?=$MAP2GIS_ID?>(\''+mid+
            '\')"><?=GetMessage('2GIS_SET_POINT_DEL')?></a>';
        marker.setBalloonHeaderContent( del_link );
        var l = map2gis_markers_<?=$MAP2GIS_ID?>.length;
        var m_info = {
            id: mid,
            lat: lat,
            lon: lon,
            name: name,
            marker: marker
        };
        map2gis_markers_<?=$MAP2GIS_ID?>.push( m_info );
        map2gis_<?=$MAP2GIS_ID?>.markers.add( marker );
        // add to points list in HTML
        var points_container = document.getElementById('map2gis_points_container');
        var point_div = document.createElement( 'DIV' );
        point_div.innerHTML = ' - <a href="#" ' +
            'onclick="map2gis_scrollToPoint_<?=$MAP2GIS_ID?>(\'' + mid + '\')">' + name + '</a>' +
            ' (<a href="#" onclick="map2gis_removeMarker_<?=$MAP2GIS_ID?>(\'' + mid + 
            '\')"><?=GetMessage('2GIS_SET_POINT_DEL')?></a>)';
        point_div.style.display = 'block';
        point_div.className = 'map2gis_point_row';
        point_div.id = 'm2gp_' + mid;
        points_container.appendChild( point_div );
    }
    //
    function map2gis_findMarkerIndexByMarkerId_<?=$MAP2GIS_ID?>( mid )
    {
        var ll = map2gis_markers_<?=$MAP2GIS_ID?>.length;
        var i = 0;
        var index = -1;
        for( i=0; i<ll; i++ )
        {
            var minfo = map2gis_markers_<?=$MAP2GIS_ID?>[i];
            if( minfo.id == mid )
            {
                index = i;
                break;
            }
        }
        return index;
    }
    //
    function map2gis_removeMarker_<?=$MAP2GIS_ID?>( mid )
    {
        var index = map2gis_findMarkerIndexByMarkerId_<?=$MAP2GIS_ID?>( mid );
        var mm = map2gis_markers_<?=$MAP2GIS_ID?>[index];
        map2gis_markers_<?=$MAP2GIS_ID?>.splice( index, 1 );
        mm.marker.hideBalloon();
        map2gis_<?=$MAP2GIS_ID?>.markers.remove( mid );
        // remove from HTML
        var points_container = document.getElementById( 'map2gis_points_container' );
        var point_div = document.getElementById( 'm2gp_' + mid );
        if( point_div )
            points_container.removeChild( point_div );
    }
    //
    function map2gis_scrollToPoint_<?=$MAP2GIS_ID?>( mid )
    {
        var index = map2gis_findMarkerIndexByMarkerId_<?=$MAP2GIS_ID?>( mid );
        if( index == -1 ) return;
        var minfo = map2gis_markers_<?=$MAP2GIS_ID?>[index];
        map2gis_<?=$MAP2GIS_ID?>.setCenter( new DG.GeoPoint( minfo.lon, minfo.lat ) );
        minfo.marker.showBalloon();
    }
    //
    // double click handler
    function maps2gis_onDoubleClick_<?=$MAP2GIS_ID?>(evt) {
        var pt = evt.getGeoPoint(); // returns DG.GeoPoint
        var name = prompt( '<?=CUtil::JSEscape( GetMessage('2GIS_SET_POINT_DESC') );?>', null );
        if( name != null )
            map2gis_addMarker_<?=$MAP2GIS_ID?>( pt.getLat(), pt.getLon(), name );
    }
    //
    // drag handler
    function maps2gis_onDragStop_<?=$MAP2GIS_ID?>(evt) {
        var pt = evt.getGeoPoint(); // returns DG.GeoPoint
        BX('map2gis_center_lat_<?=$MAP2GIS_ID?>').value = pt.getLat();
        BX('map2gis_center_lon_<?=$MAP2GIS_ID?>').value = pt.getLon();
    }
    //
    // zoom handler
    function maps2gis_onZoomChange_<?=$MAP2GIS_ID?>(evt_map) { // DG.Events.Map
        var z = evt_map.getZoom();
        BX('map2gis_zoom_<?=$MAP2GIS_ID?>').value = z;
    }
    //
    // save changes handler
    function map2gis_saveChanges_<?=$MAP2GIS_ID?>()
    {
        // get points (placemarks)
        var len = map2gis_markers_<?=$MAP2GIS_ID?>.length;
        var PLACEMARKS = new Array( len );
        for( var i=0; i<len; i++ )
        {
            PLACEMARKS[i] = { };
            PLACEMARKS[i].TEXT = map2gis_markers_<?=$MAP2GIS_ID?>[i].name;
            PLACEMARKS[i].LAT  = map2gis_markers_<?=$MAP2GIS_ID?>[i].lat;
            PLACEMARKS[i].LON  = map2gis_markers_<?=$MAP2GIS_ID?>[i].lon;
        }
        // get map center & scale
        var m_lon   = BX('map2gis_center_lon_<?=$MAP2GIS_ID?>').value;
        var m_lat   = BX('map2gis_center_lat_<?=$MAP2GIS_ID?>').value;
        var m_scale = BX('map2gis_zoom_<?=$MAP2GIS_ID?>').value;
        // create MAP_DATA array structure
        var to_save = {
            LON        : m_lon,
            LAT        : m_lat,
            SCALE      : m_scale,
            PLACEMARKS : PLACEMARKS
        };
        var serialized_string = serialize_php( to_save );
        // finally, save serialized data
        if( window.js2gisCEOpener )
            window.js2gisCEOpener.SaveData( serialized_string );
    }
    //
    // search helper class: perform search, keep results (in array), show/hide them (in div)
    var map2gis_geosearch_<?=$MAP2GIS_ID?> = {
        input:      null,
        out_ul:     null,
        ar_results: null,
        timerID:    null,
        timerDelay: 1000,
        
        __init: function( element )
        {
            map2gis_geosearch_<?=$MAP2GIS_ID?>.input = element;
            map2gis_geosearch_<?=$MAP2GIS_ID?>.ar_results = null;
            map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID = null;
        },
        
        onblur: function()
        {
            map2gis_geosearch_<?=$MAP2GIS_ID?>.showResults( false );
        },
        
        onfocus: function()
        {
            map2gis_geosearch_<?=$MAP2GIS_ID?>.showResults( true );
        },
        
        onkeypress: function( evt )
        {
            if( evt == null )
                evt = window.event;
            // search immediately on <Enter>
            if( evt != null )
            {
                if( evt.keyCode == 13 )
                {
                    map2gis_geosearch_<?=$MAP2GIS_ID?>.doSearch();
                    return;
                }
            }
            // cancel existing timer
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID )
                clearTimeout( map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID );
            // set new timer
            map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID = setTimeout(
                map2gis_geosearch_<?=$MAP2GIS_ID?>.doSearch, 
                map2gis_geosearch_<?=$MAP2GIS_ID?>.timerDelay );
        },
        
        doSearch: function()
        {
            // deal with timer
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID != null )
                clearTimeout( map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID );
            map2gis_geosearch_<?=$MAP2GIS_ID?>.timerID = null;
            // 
            var value = map2gis_geosearch_<?=$MAP2GIS_ID?>.input.value;
            if( value.length > 1 )
            {
                map2gis_geosearch_<?=$MAP2GIS_ID?>.clearResults();
                // perform search
                var searchOptions = {
                    types: ['city', 'settlement', 'district'], // no 'street'
                    limit: 10,
                    success: map2gis_geosearch_<?=$MAP2GIS_ID?>.receiveSearchResults,
                    failure: map2gis_geosearch_<?=$MAP2GIS_ID?>.showError
                };
                map2gis_<?=$MAP2GIS_ID?>.geocoder.get( value, searchOptions );
            }
        },
        
        receiveSearchResults: function( geocoderObjects )
        {
            if( geocoderObjects == null ) return;
            var _this = map2gis_geosearch_<?=$MAP2GIS_ID?>;
            if( _this.ar_results == null )
                _this.ar_results = [];
            var len = geocoderObjects.length;
            for( var i = 0; i < len; i++ )
            {
                var geocoderObject = geocoderObjects[i];
                var centerGeoPoint = geocoderObject.getCenterGeoPoint();
                // collect result
                var one_result = {
                    name:      geocoderObject.getName(),
                    shortName: geocoderObject.getShortName(),
                    type:      geocoderObject.getType(),
                    lat:       centerGeoPoint.getLat(),
                    lon:       centerGeoPoint.getLon()
                }
                _this.ar_results[i] = one_result;
            }
            _this.generateResults();
            _this.showResults( true );
        },
        
        showError: function( errcode, errmessage )
        {
            var s = 'Geo object search error:\nError code: ' + errcode +'\n\n';
            s += errmessage;
            alert( s );
        },
        
        getTypeStr: function( typ )
        {
            var ret = typ;
            switch( typ )
            {
                case 'city':        ret = '<?=GetMessage('2GIS_TYPE_CITY')?>'; break;
                case 'settlement':  ret = '<?=GetMessage('2GIS_TYPE_SETTLEMENT')?>'; break;
                case 'district':    ret = '<?=GetMessage('2GIS_TYPE_DISTRICT')?>'; break;
                case 'street':      ret = '<?=GetMessage('2GIS_TYPE_STREET')?>'; break;
            }
            return ret;
        },
        
        generateResults: function()
        {
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul == null )
            {
                if( map2gis_geosearch_<?=$MAP2GIS_ID?>.input == null )
                    map2gis_geosearch_<?=$MAP2GIS_ID?>.input = document.getElementById( 'map2gis_geosearch_text<?=$MAP2GIS_ID?>' );
                var obPos = jsUtils.GetRealPos( map2gis_geosearch_<?=$MAP2GIS_ID?>.input );
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul = document.body.appendChild( document.createElement('UL') );
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.className = 'simai-address-search-results';
                //map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.position = 'fixed'; // absolute in CSS
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.top = (obPos.bottom + 2) + 'px';
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.left = obPos.left + 'px';
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.display = 'none';
            }
            var _this = map2gis_geosearch_<?=$MAP2GIS_ID?>;
            var len = _this.ar_results.length;
            for( var i=0; i<len; i++ )
            {
                var lnk_text = '' + _this.ar_results[i].name + '  (' +
                    _this.getTypeStr( _this.ar_results[i].type ) + ')';
                _this.out_ul.appendChild( BX.create( 'LI', {
                    attrs: {className: i == 0 ? 'simai-address-search-result-first' : ''},
                    children: [
                        BX.create( 'A', {
                            attrs:  { href: "javascript:void(0)" },
                            props:  { BXSearchIndex: i },
                            events: { click: _this.onClickResult },
                            children: [
                                BX.create( 'SPAN', {
                                    text: lnk_text
                                } )
                            ]
                        } )
                    ] // children
                } ) );
            }
        },
        
        showResults: function( show )
        {
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul == null )
                return;
            if( show == false) {
                setTimeout( function() { map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.display = 'none' }, 500 );
                return;
            }
            map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.style.display = 'block';
        },
        
        clearResults: function()
        {
            // clear array
            map2gis_geosearch_<?=$MAP2GIS_ID?>.ar_result = null;
            // remove UL block
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul != null )
            {
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul.parentNode.removeChild( map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul );
                map2gis_geosearch_<?=$MAP2GIS_ID?>.out_ul = null;
            }
        },
        
        onClickResult: function()
        {
            if( this.BXSearchIndex == null )
                return;
            if( map2gis_geosearch_<?=$MAP2GIS_ID?>.ar_results == null)
                return;
            //
            var el = map2gis_geosearch_<?=$MAP2GIS_ID?>.ar_results[ this.BXSearchIndex ];
            var point = new DG.GeoPoint( el.lon, el.lat );
            map2gis_<?=$MAP2GIS_ID?>.setCenter( point, 10 );
        }
    };
    //
    // map load handler
    // DG.autoload( function() {
    function map2gis_createMap_<?=$MAP2GIS_ID?>() {
        alert( ' <?php echo GetMessage('2GIS_SET_POPUP_WINDOW_TITLE'); ?> ' );
		
		// map object
        var map_2g = new DG.Map( 'map2gis_<?=$MAP2GIS_ID?>_map' );

		// map initial center pos and scale
		//map_2g.setCenter( new DG.GeoPoint(54.43, 55.58) );
		//map_2g.setZoom(12); 
		
        // map initial center pos and scale
        map_2g.setCenter( new DG.GeoPoint( <?=CUtil::JSEscape($arData['LON'])?>, <?=CUtil::JSEscape($arData['LAT'])?> ) );
		map_2g.setZoom( <?=CUtil::JSEscape($arData['SCALE'])?> );

        map_2g.controls.add( new DG.Controls.Zoom() );
        map_2g.disableDblClickZoom();
        map_2g.fullscreen.enable();
        map_2g.geoclicker.disable();
        map_2g.enableRightButtonMagnifier();
        map2gis_<?=$MAP2GIS_ID?> = map_2g;
// if have points
<?php if( is_array($arData) && is_array($arData['PLACEMARKS']) && count($arData['PLACEMARKS'] > 0) )
foreach( $arData['PLACEMARKS'] as $POINT ) { ?>
        map2gis_addMarker_<?=$MAP2GIS_ID?>( <?=CUtil::JSEscape($POINT['LAT'])?>, <?=CUtil::JSEscape($POINT['LON'])?>, '<?=CUtil::JSEscape($POINT['TEXT'])?>' );
<?php } ?>
        // dbl click handler
        map_2g.addEventListener(  map_2g.getContainerId(), 'DgDoubleClick',
            maps2gis_onDoubleClick_<?=$MAP2GIS_ID?> );
        // drag handler
        map_2g.addEventListener(  map_2g.getContainerId(), 'DgDragStop',
            maps2gis_onDragStop_<?=$MAP2GIS_ID?> );
        // zoom change handler
        map_2g.addEventListener(  map_2g.getContainerId(), 'DgZoomChange',
            maps2gis_onZoomChange_<?=$MAP2GIS_ID?> );
        // init geo search
        var inp = document.getElementById( 'map2gis_geosearch_text<?=$MAP2GIS_ID?>' );
        map2gis_geosearch_<?=$MAP2GIS_ID?>.__init( inp );
        //error.error; //cause error *yaomingface*
    }
    //
    //alert("*");
</script>

<!-- 2GIS map -->
<div id="map2gis_wrapper" class="map2gis_wrapper">
    <div id="map2gis_<?=$MAP2GIS_ID?>_map" style="width: 450px; height: 400px;">2GIS Map</div>
    <?php echo GetMessage('2GIS_GEO_SEARCH'); ?>:
    <input type="text" id="map2gis_geosearch_text<?=$MAP2GIS_ID?>" value="" size="40"
           onblur="map2gis_geosearch_<?=$MAP2GIS_ID?>.onblur()"
           onfocus="map2gis_geosearch_<?=$MAP2GIS_ID?>.onfocus()"
           onkeyup="map2gis_geosearch_<?=$MAP2GIS_ID?>.onkeypress(event)"
           onkeypress="map2gis_geosearch_<?=$MAP2GIS_ID?>.onkeypress(event)" />
</div>
<div class="map2gis_set_row">
    <b><?=GetMessage('2GIS_SET_START_POS')?></b>:
</div>
<div class="map2gis_set_row">
    <?=GetMessage('2GIS_SET_START_POS_LAT')?>:
    <input type="text" id="map2gis_center_lat_<?=$MAP2GIS_ID?>" value="<?=CUtil::JSEscape($arData['LAT'])?>" size="18" />
</div>
<div class="map2gis_set_row">
    <?=GetMessage('2GIS_SET_START_POS_LON')?>:
    <input type="text" id="map2gis_center_lon_<?=$MAP2GIS_ID?>" value="<?=CUtil::JSEscape($arData['LON'])?>" size="18" />
</div>
<div class="map2gis_set_row">
    <?=GetMessage('2GIS_SET_START_POS_SCALE')?>:
    <input type="text" id="map2gis_zoom_<?=$MAP2GIS_ID?>" value="<?=CUtil::JSEscape($arData['SCALE'])?>" size="5" />
</div>

<br />

<div class="map2gis_points_wrapper">
    <?=GetMessage('2GIS_SET_POINTS_ADD_DESCRIPTION')?><br />
    <b><?=GetMessage('2GIS_SET_POINTS')?></b>:<br />
    <div id="map2gis_points_container">
    </div>
</div>

<?php
$obJSPopup->StartButtons(); // has echo '</form>'."\r\n";
?>

<input type="submit" value="<?echo GetMessage('2GIS_SET_SUBMIT')?>" onclick="return map2gis_saveChanges_<?=$MAP2GIS_ID?>();"/>

<?php
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>