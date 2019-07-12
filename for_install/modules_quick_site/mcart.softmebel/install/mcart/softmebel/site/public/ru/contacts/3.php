<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "АКВИЛОН | диваны, кровати, угловые диваны мебель Санкт-Петербург - Шведская мебель");
$APPLICATION->SetTitle("МЦ «АКВИЛОН»");
?> 
<table border="0" width="100%" bgcolor="#F3F3E2"> 
  <tbody> 
    <tr> <td width="20%" valign="top"> 
        <p><b>МЦ &laquo;АКВИЛОН&raquo; 
            <br />
           </b>			<i>телефон: (812) 454-79-66</i></p>
       </td> <td width="80%"> 
        <div style="margin-top: 0px; margin-right: 0px; margin-bottom: 20px; margin-left: 60px; "><i>Адрес:</i> 
          <br />
         Новолитовская ул.,14А
          <p>&nbsp;</p>
         
          <div></div>
         </div>
       </td> </tr>
   </tbody>
 </table>
 
<br />
 
<table border="0" width="100%"> 
  <tbody> 
    <tr> <td width="358"> 
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту  (начало) -->
 
<script src="http://api-maps.yandex.ru/1.1/?key=ABpu0U0BAAAAhFf0IAIAXUajxc8OqMe8aIOqb_jh3vzXDEoAAAAAAAAAAACwtllw_m_t_knqgXHPDBOU4chWWg==&modules=pmap&wizard=constructor" type="text/javascript"></script>
 
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-2428")[0]);
        map.setCenter(new YMaps.GeoPoint(30.356994,59.984378), 16, YMaps.MapType.MAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "Народная"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));

        YMaps.Styles.add("constructor#pmlbmPlacemark", {
            iconStyle : {
                href : "http://api-maps.yandex.ru/i/0.3/placemarks/pmlbm.png",
                size : new YMaps.Point(28,29),
                offset: new YMaps.Point(-8,-27)
            }
        });

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(30.358067,59.983131), "constructor#pmlbmPlacemark", "Шведская мебель"));
        
        function createObject (type, point, style, description) {
            var allowObjects = ["Placemark", "Polyline", "Polygon"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || "";
            
            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;
            
            return object;
        }
    });
</script>
 
        <div id="YMapsID-2428" style="width: 450px; height: 350px; "></div>
       
        <div style="width: 450px; text-align: right; font-family: Arial; "></div>
       
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (конец) -->
 </td> <td valign="top"> 
        <p></p>
       
        <div style="margin-top: 0px; margin-right: 0px; margin-bottom: 20px; margin-left: 40px; ">Ближайшие станции метро 
          <br />
         <font color="#990000">Лесная </font>793 м 
          <br />
         <font color="#990000">Выборгская</font> 1,458 км 
          <br />
         <font color="#990000">Площадь Мужества</font> 1,949 км </div>
       </td></tr>
   </tbody>
 </table>
 
<p>&nbsp;</p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>