<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("�������� ��������");
?>
<p><strong>�� ������ �������� ����� � ����� ������, �������� ����� ����</strong></p><br />
<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "�������, ���� ��������� �������.",
		"EMAIL_TO" => "#COMPANY_CONTROL_EMAIL#",
		"REQUIRED_FIELDS" => array("EMAIL", "MESSAGE"),
		"EVENT_MESSAGE_ID" => array()
	),
false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>