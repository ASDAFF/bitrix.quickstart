<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "������� ������ �� ������ | ������� ������ �� ������ �� ������������� | ����� | �����-���������");
$APPLICATION->SetTitle("������� ������ �� ������ � ����������");
?> 
<p align="left"><strong>������� ������ �� ������</strong> ��������� ��� ��������� �� ������ �������� � ��������� �������� �������, ��� ��������� ��� ������������ ������ ���� �� ������ ��� ������� �������� � �������. 
  <br />
 </p>
 ��� <b>������� ������ �� ������</b> �� ������ ������������ �� ������ �� 25%<b> 
  <br />
 
  <br />
 </b>��������� ��� ��������� ����� �������� �����: 
<br />
 
<br />
 <b><?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	".default",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "�������, ���� ��������� �������.",
		"EMAIL_TO" => "mebel@swmebel.ru",
		"REQUIRED_FIELDS" => array(0=>"NAME",1=>"EMAIL",2=>"MESSAGE",),
		"EVENT_MESSAGE_ID" => array()
	)
);?> 
  <br />
 
  <br />
</b> ����� �� ���������, ��� �� ������� <a href="/contacts/6.php" >��������� �������-�����</a>. <b>
  <br />
 
  <br />
 
  <p></p>
 </b><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>