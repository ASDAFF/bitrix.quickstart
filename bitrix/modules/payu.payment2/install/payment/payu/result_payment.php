<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_GET['result'])) {
	if ($_GET['result'] === '-1') {
		$message = ' ��� ��������� ���� � ������� Qiwi. ���������� ��������� � ������ ������� Qiwi �� ������ http://qiwi.com/ � ����������� ������. ';
	} elseif ($_GET['result'] === '0') {
		$message = ' ��� ����� ������� ��������. ��� ����� ����� ���������...';
	} elseif ($_GET['result'] === '1') {
		$message = ' �� ������� ���������� ��� �����. ���������� ���������� ��������� ����� ��� ���������� � ������ ������������...';
	} else {
		$message = '���������� � ������� �� ��������.';
	}
}
?>

<div>
	<h1> ���������� � ������� ������ </h1>
	<h4> ��������� ����������! </h4>
	<p> <?= $message ?> </p>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>