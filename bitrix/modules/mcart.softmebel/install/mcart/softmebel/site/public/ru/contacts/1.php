<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "����� ������ | ������, �������, ������� ������ ������ �����-��������� - �������� ������");
$APPLICATION->SetTitle("�� ������ �����ͻ");
?> 
<table width="100%" border="0" bgcolor="#F3F3E2"> 
  <tbody> 
    <tr> <td width="20%" valign="top"> 
        <p><b>�� &laquo;����� ������&raquo; 
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
        var map = new YMaps.Map(YMaps.jQuery("#YMapsID-4058")[0]);
        map.setCenter(new YMaps.GeoPoint(30.325258,60.060675), 15, YMaps.MapType.MAP);
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

       map.addOverlay(createObject("Placemark", new YMaps.GeoPoint(30.327404,60.059194), "constructor#pmlbmPlacemark", "�������� ������"));
        
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
 
        <div style="width: 450px; height: 350px; " id="YMapsID-4058"></div>
       
        <div style="width: 450px; text-align: right; font-family: Arial; "></div>
       
<!-- ���� ���� ���� ����� �������� � �� ����� ��������, ��� �� ������ ���������� ����� (�����) -->
 </td> <td valign="top"> 
        <p></p>
       
        <div style="margin-top: 0pt; margin-right: 0pt; margin-bottom: 20px; margin-left: 40px; ">��������� ������� ����� 
          <br />
         		<font color="#33CCFF">�������� �����������</font> 907 � 
          <br />
         <font color="#33CCFF">������</font> 1,058 �� 
          <br />
         <font color="#33CCFF">������</font> 2,47 �� 
          <p></p>
         </div>
       </td> </tr>
   </tbody>
 </table>
 
<p>&nbsp;</p>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>