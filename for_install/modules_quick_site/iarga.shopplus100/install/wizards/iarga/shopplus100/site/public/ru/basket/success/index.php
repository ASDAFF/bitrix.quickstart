<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("����� ��������");
$urls = explode("/",$_SERVER['REQUEST_URI']);
$_REQUEST['ORDER_ID'] = $urls[(sizeof($urls)-2)];
if($_REQUEST['ORDER_ID']<1 && !$USER->IsAdmin()) LocalRedirect('/auth/orders/');
?>
<h1>������� �� ����� �� ����� �����</h1>

<div>����� ������ ������ <a href="/auth/orders/"><?=$_REQUEST['ORDER_ID']?></a>.</div>

<div>������ ������ ��������� ��� �� E-mail.</div>

<div>���� ��������� �������� � ���� � ��������� �����.</div>
<div>��� ������������� ����������� ������ ������ ��������� ��������.</div>

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.payment",
	"",
Array(),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>