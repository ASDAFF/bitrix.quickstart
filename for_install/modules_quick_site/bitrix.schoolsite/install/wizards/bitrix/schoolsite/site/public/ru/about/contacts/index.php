<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("��������");
?>
<p class="demo_content"><b>���������������� �������:</b> �� ����� ��������� ���������������� �������������� ����������, �� ��������������� ��� ���������� � ���� ��������. ��������� ������������� ������������� ��� ������������ ������������ ����� � �������� ���������� ����������� ��� ���������� ���������� ������� � �����������. �������� �������� �� �����������1�-������� �� ������������� ���� ������ � Google. ����� �������� ���������� �� ��������, ����� ��������������� ������� ��������������. </p>
<p>#SCHOOL_NAME# #SCHOOL_ADDRESS# #SCHOOL_PHONE# #SCHOOL_EMAIL#</p>

<br>
<div><b>����� ������:</b></div>
 
<div> 
  <table width="300" height="80" border="0" cellpadding="3" cellspacing="3" align="left"> 
    <tbody> 
      <tr><td><font class="Apple-style-span" size="2">����������� - �������</font></td><td><font class="Apple-style-span" size="2">8.00 - 18.00</font></td></tr>
     
      <tr><td><font class="Apple-style-span" size="2">�������</font></td><td><font class="Apple-style-span" size="2">8.00 - 17.30</font></td></tr>
     </tbody>
   </table>
 </div>
<br />
 
<br />
 
<br />
 
<br />
 
<br />
 <b>C���� �������: </b> 
<p><?$APPLICATION->IncludeComponent(
	"bitrix:map.google.view",
	"",
	Array(
		"INIT_MAP_TYPE" => "ROADMAP",
		"MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.847353959003;s:10:\"google_lon\";d:37.429544016858;s:12:\"google_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:44:\"������, ����, ###RN###��. ������� ������, 1 \";s:3:\"LON\";d:37.42991952612;s:3:\"LAT\";d:55.847191333221;}}}",
		"MAP_WIDTH" => "600",
		"MAP_HEIGHT" => "500",
		"CONTROLS" => array("SMALL_ZOOM_CONTROL","TYPECONTROL","SCALELINE"),
		"OPTIONS" => array("ENABLE_SCROLL_ZOOM","ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING","ENABLE_KEYBOARD"),
		"MAP_ID" => ""
	)
);?></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>