<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������������ ������");
?><p>� ������ �������� �� ������ ��������� ������� ��������� �������, ��� ���������� ����� �������, ����������� ��� �������� ������ ����������, � ����� ����������� �� ������� � ������ �������������� ��������. </p>
							
<h2>������ ����������</h2>
<ul>
	<li><a href="profile/">�������� ��������������� ������</a></li>
	<li><a href="profile/?change_password=yes">�������� ������</a></li>
	<li><a href="profile/?forgot_password=yes">������ ������?</a></li>
</ul>

<h2>������</h2>
<ul>
	<li><a href="order/">������������ � ���������� �������</a></li>
	<li><a href="cart/">���������� ���������� �������</a></li>
	<li><a href="cart/">���������� ���������� ������</a></li>

	<li><a href="order/?filter_history=Y">���������� ������� �������</a></li>
</ul>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>