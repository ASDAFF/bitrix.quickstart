<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("����� �������� �����");
?>�� ������ ������ ���������� �� ����� � &quot;�����&quot; ��� ����������� ������ �� �������� 8(495) 225-58-90� ��� ��������� ����� �������� �����. 
<br />
<font color="#ff0000"><strong>��������!</strong> </font>���� �� ������ ���������� �� ������������� ��� ���� ���� �����, <strong>����������� �������</strong> � ���� ������ <strong>���� ����� ��������</strong> ��� ������������ ������. � ��������� ������ ����� �� ����� ������ � ����������. 
<br />

<br />
���� ���������� &quot;*&quot; ����������� ��� ���������� 
<br />

<br />
�<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	".default",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "�������, ���� ��������� �������.",
		"EMAIL_TO" => "",
		"REQUIRED_FIELDS" => array("NAME", "EMAIL", "MESSAGE"),
		"EVENT_MESSAGE_ID" => array()
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>