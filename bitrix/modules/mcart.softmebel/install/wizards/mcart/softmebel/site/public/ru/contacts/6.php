<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "����� ������� ������ ������ ������ � �����-����������");
$APPLICATION->SetPageProperty("keywords", "�������-�����, �����, ������ ������ � ����������");
$APPLICATION->SetPageProperty("title", "������� ����� ������ ������. ����� �������-������ � ���������� ������� ������ ������");
$APPLICATION->SetTitle("������� ����� ������� ������� ");
?> 
<table width="100%" border="0" bgcolor="#F3F3E2"> 
  <tbody> 
    <tr> <td width="20%" valign="top"> 
        <p><b>�������-�����</b><b>
            <br />
           </b>			<i>�������: (812) 454-79-66</i></p>
       </td> <td width="80%"> 
        <div style="margin-top: 0pt; margin-right: 0pt; margin-bottom: 20px; margin-left: 60px; ">��.�����������, � 8
          <p>&nbsp;</p>
         
          <div></div>
         </div>
       </td> </tr>
   </tbody>
 </table>
 
<br />
 
<table width="100%" border="0"> 
  <tbody> 
    <tr> <td width="358"> 
<!-- ���� ���� ���� ����� �������� � �� ����� ��������, ��� �� ������ ���������� �����  (������) -->
 
<script src="http://api-maps.yandex.ru/1.1/?key=ABpu0U0BAAAAhFf0IAIAXUajxc8OqMe8aIOqb_jh3vzXDEoAAAAAAAAAAACwtllw_m_t_knqgXHPDBOU4chWWg==&modules=pmap&wizard=constructor" type="text/javascript"></script>
 
<script type="text/javascript">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-829")[0]);
        map.setCenter(new YMaps.GeoPoint(30.355797,59.981982), 16, YMaps.MapType.MAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return "��������"; };
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

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(30.356869,59.98124), "constructor#pmlbmPlacemark", "�������-����� &laquo;�������� ������&raquo;"));
        
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
 
        <div id="YMapsID-829" style="width: 450px; height: 350px; "></div>
       
        <div style="font-family: Arial; text-align: right; width: 450px; "></div>
       
<!-- ���� ���� ���� ����� �������� � �� ����� ��������, ��� �� ������ ���������� ����� (�����) -->
 </td> <td valign="top"> 
        <p></p>
       
        <div style="margin-top: 0px; margin-right: 0px; margin-bottom: 20px; margin-left: 40px; ">��������� ������� ����� 
          <br />
         <font color="#990000">������ </font>793 � 
          <br />
         <font color="#990000">����������</font> 1,458 �� 
          <br />
         <font color="#990000">������� ��������</font> 1,949 �� </div>
       </td> </tr>
   </tbody>
 </table>
 
<p>&nbsp;</p>
 �<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>