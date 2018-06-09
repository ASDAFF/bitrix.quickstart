<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<script type="text/javascript">
if (!window.GLOBAL_arMapObjects)
	window.GLOBAL_arMapObjects = {};

function init_<?echo $arParams['MAP_ID']?>(context) 
{
	if (null == context)
		context = window;

	if (!context.YMaps)
		return;
	
	window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'] = new context.YMaps.Map(context.document.getElementById("BX_YMAP_<?echo $arParams['MAP_ID']?>"));
	var map = window.GLOBAL_arMapObjects['<?echo $arParams['MAP_ID']?>'];
	
	map.bx_context = context;
	
	map.setCenter(new context.YMaps.GeoPoint(<?echo $arParams['INIT_MAP_LON']?>, <?echo $arParams['INIT_MAP_LAT']?>), <?echo $arParams['INIT_MAP_SCALE']?>, context.YMaps.MapType.<?echo $arParams['INIT_MAP_TYPE']?>);
<?
foreach ($arResult['ALL_MAP_OPTIONS'] as $option => $method)
{
	if (in_array($option, $arParams['OPTIONS'])):
?>
	map.enable<?echo $method?>();
<?
	else:
?>
	map.disable<?echo $method?>();
<?
	endif;
}
foreach ($arResult['ALL_MAP_CONTROLS'] as $control => $method)
{
	if (in_array($control, $arParams['CONTROLS'])):
?>
	map.addControl(new context.YMaps.<?echo $method?>(<?=($method=='TypeControl'?'[YMaps.MapType.MAP, YMaps.MapType.SATELLITE, YMaps.MapType.HYBRID, YMaps.MapType.PMAP, YMaps.MapType.PHYBRID], [0,1,2,3,4]':'');?>));
<?	
	endif;
}
if ($arParams['DEV_MODE'] == 'Y'):
?>
	context.bYandexMapScriptsLoaded = true;
<?
endif;

if ($arParams['ONMAPREADY']):
?>
	if (window.<?echo $arParams['ONMAPREADY']?>)
	{
		<?
		if ($arParams['ONMAPREADY_PROPERTY']):
		?>
		//alert(map.bx_context);
		<?echo $arParams['ONMAPREADY_PROPERTY']?> = map;
		//alert(window.<?echo $arParams['ONMAPREADY']?>);
		window.<?echo $arParams['ONMAPREADY']?>();
		<?
		else:
		?>
		//alert('qq');
		window.<?echo $arParams['ONMAPREADY']?>(map);
		<?
		endif;
		?>
	}
<?
endif;
?>	
}
<?
if ($arParams['DEV_MODE'] == 'Y'):
?>
function BXMapLoader_<?echo $arParams['MAP_ID']?>(MAP_KEY)
{
	if (null == MAP_KEY || typeof MAP_KEY == 'object')
		MAP_KEY = '<?echo $arParams['KEY']?>';
		
	if (null == window.bYandexMapScriptsLoaded)
	{
		var obMapContainer = document.getElementById("BX_YMAP_<?echo $arParams['MAP_ID']?>");

		var obFrame = document.createElement('IFRAME');
		//obFrame.src = "/bitrix/components/bitrix/map.yandex.system/blank.php";
		
		obFrame.style.height = 0;
		obFrame.style.width = 0;
		obFrame.style.border = 'none';
		obFrame.setAttribute('frameBorder', '0');
		
		obMapContainer.innerHTML = '';
		obMapContainer.appendChild(obFrame);
		
		var iframeWindow = obFrame.contentWindow;

		if (obFrame.contentDocument)
			var iframeDocument = obFrame.contentDocument;
		else
		{
			//alert('ee');
			var iframeDocument = obFrame.contentWindow.document;
			//alert('rr');
		}
		
		var strOnload = 
			('\v'=='v') ? 
			'onreadystatechange="if(this.readyState==\'complete\'&&null!=window.YMaps){window.YMaps.load(function(){parent.init_<?echo $arParams['MAP_ID']?>(window);});}"' :
			'onload="if(null!=window.YMaps){window.YMaps.load(function(){parent.init_<?echo $arParams['MAP_ID']?>(window);});}"';
		
		obFrame.style.height = '<?echo $arParams['MAP_HEIGHT'];?>';
		obFrame.style.width = '<?echo $arParams['MAP_WIDTH'];?>';
		
		iframeDocument.write('<html><head><style type="text/css">body{margin:0;padding:0;overflow:hidden;font-family: Arial; font-size: 11px;}</style>' + 
			'<'+'!--[if IE]><style type="text/css">vml\\:shape,vml\\:group{behavior: url(#default#VML);display:inline-block;}</style><'+'![endif]--'+'>'+
			'<script type="text/javascript" charset="utf-8" src="http://api-maps.yandex.ru/<?echo $arParams['YANDEX_VERSION'];?>/?key=' + MAP_KEY + '&loadByRequire=1&wizard=bitrix&rnd=' + Math.random() + '" ' + strOnload + '></s' + 'cript></head><body><div id="BX_YMAP_<?echo $arParams['MAP_ID']?>" style="height: <?echo $arParams['MAP_HEIGHT'];?>; width: <?echo $arParams['MAP_WIDTH']?>;"><?echo GetMessage('MYS_LOADING');?></div></body></html>');
		iframeDocument.close();
	}
	else
	{
		YMaps.load(init_<?echo $arParams['MAP_ID']?>);
	}
}
<?
	if (!$arParams['WAIT_FOR_EVENT']):
?>
if (window.attachEvent) // IE
	window.attachEvent("onload", BXMapLoader_<?echo $arParams['MAP_ID']?>);
else if (window.addEventListener) // Gecko / W3C
	window.addEventListener('load', BXMapLoader_<?echo $arParams['MAP_ID']?>, false);
else
	window.onload = BXMapLoader_<?echo $arParams['MAP_ID']?>;
<?
	else:
		echo CUtil::JSEscape($arParams['WAIT_FOR_EVENT']),' = BXMapLoader_',$arParams['MAP_ID'],';';
	endif;
else: // $arParams['DEV_MODE'] == 'Y'
?>
if (window.attachEvent) // IE
	window.attachEvent("onload", function(){init_<?echo $arParams['MAP_ID']?>()});
else if (window.addEventListener) // Gecko / W3C
	window.addEventListener('load', function(){init_<?echo $arParams['MAP_ID']?>()}, false);
else
	window.onload = function(){init_<?echo $arParams['MAP_ID']?>()};
<?
endif; // $arParams['DEV_MODE'] == 'Y'
?>
</script>
<div id="BX_YMAP_<?echo $arParams['MAP_ID']?>" class="bx-yandex-map" style="height: <?echo $arParams['MAP_HEIGHT'];?>; width: <?echo $arParams['MAP_WIDTH']?>;"><?echo GetMessage('MYS_LOADING'.($arParams['WAIT_FOR_EVENT'] ? '_WAIT' : ''));?></div>