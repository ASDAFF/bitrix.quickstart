<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("��������");
?><p>
	���������� � ����� ������������ � �������� ���������������� ������������ �� �������� �������, �������, ������ ��� ������ ������� � �������������.
</p>
<p>
	�� ������ ���������� � ��� �� ��������, �� ����������� ����� ��� ������������ � ������� � ����� �����. ����� ���� ������ ��� � �������� �� ��� ���� �������.
</p>
<?$APPLICATION->IncludeComponent(
	"citrus:realty.contacts",
	"offices",
	array()
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>