<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("����� �������� � ������� ��������");
if($_REQUEST['ORDER_ID']<1 && !$USER->IsAdmin()) LocalRedirect('/basket/');
?> 
<h1>����� �������� � ������� �� ����� ����������</h1>

<div>����� ������ ������ <?=$_REQUEST['ORDER_ID']?>.</div>

<div>������ ������ ��������� ��� �� E-mail.</div>

<div>���� ��������� �������� � ���� � ��������� �����.</div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>