<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("�������, ����������");
?>

<p><strong>������� ��������� ��������������� ������</strong><br />
<a href = "/info/rules/files/skoroport.rtf">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/skoroport.rtf")/1024);?> �����)
</p>
<br />

<p><strong>�������������. �� ����������� ������ �����������-�������������� ������������</strong><br />
<a href = "/info/rules/files/ted_rules.rtf">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/ted_rules.rtf")/1024);?> �����)
</p>
<br />

<p><strong>������� ������� ������ � ��������� �� ��</strong><br />
<a href = "/info/rules/files/zhd_rules.doc">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/zhd_rules.doc")/1024);?> �����)
</p>
<br />

<p><strong>������� ��������� ������������ � ������������ ������</strong><br />
<a href = "/info/rules/files/large_rules.doc">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/large_rules.doc")/1024);?> �����)
</p>
<br />

<p><strong>������� ��������� ������� ������</strong><br />
<a href = "/info/rules/files/danger_rules.doc">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/danger_rules.doc")/1024);?> �����)
</p>
<br />

<p><strong>������������� ������������� � 112. �� ����������� ������ ��������� ���������� � ������ ������������� ����������� � ��������� �������� ������������� �����������</strong><br />
<a href = "/info/rules/files/112.rtf">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/112.rtf")/1024);?> �����)
</p>
<br />

<p><strong>����� ������� ��������� ������ ������������� �����������</strong><br />
<a href = "/info/rules/files/general_rules.rtf">�������</a> (~ <?echo ceil(filesize($_SERVER["DOCUMENT_ROOT"]."/info/rules/files/general_rules.rtf")/1024);?> �����)
</p>
<br />

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>