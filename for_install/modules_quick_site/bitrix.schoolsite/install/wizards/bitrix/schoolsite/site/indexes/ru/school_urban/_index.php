<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("��� ������� ��������������� ����� �8732, ������");
?>
<img src="<?=SITE_DIR?>files/banner_main.jpg"  />
<br />
<div class="about blockAlt">
 <h2><a href="about/">� ����� �����</a></h2>
 <img src="<?=SITE_DIR?>images/director_woomen.jpg" alt=""/>

 <div class="txt">
     <p>����� �8732 - ��� ����� � ������ ���� ���������� �����. ��������
     ������� �� 55 ������-������������ ����������. �������� 1 - 4 ������� ��������� �
     ��������� �������, ��� � ������ �������� ������������� ������� ���� � ����� ���
     ������.</p>

     <p>� ������ ����� ���� ���� "��������". � ����� ����� ���, ������� ��, �����������
         �����������, ���������, � ����� ������� ���� ���� ���������. </p>
     <h4>���� ����� ���:</h4>
     <ul class="list">
         <li>1238 ��������</li>
         <li>21 �������������</li>
         <li>4 ����� � ������</li>
         <li>2 ����� ����������� ����</li>
         <li>102 ����� �����</li>
         <li>4 ������������ ������</li>
     </ul>
 </div>
</div>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "index_video.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
<br />
 <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "index_photo.php",
	"EDIT_TEMPLATE" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>