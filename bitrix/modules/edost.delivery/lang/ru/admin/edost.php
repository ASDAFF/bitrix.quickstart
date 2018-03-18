<?
$MESS ['EDOST_ADMIN_TITLE'] = 'eDost: ������ �������';

$MESS ['EDOST_ADMIN_MENU_MAIN'] = '������';
$MESS ['EDOST_ADMIN_MENU_SETTING'] = '���������';
$MESS ['EDOST_ADMIN_HISTORY_HEAD'] = '��������� ��������:';
$MESS ['EDOST_ADMIN_ORDER_ALLOW_DELIVERY_HEAD'] = '������ ������, ����������� �� ��������� ';
$MESS ['EDOST_ADMIN_ORDER_MANUAL_PRINT_HEAD'] = '������ ������ �������:';
$MESS ['EDOST_ADMIN_ORDER_MANUAL_PRINT_HINT'] = '��� ������ ������ ����� �� ����������� �� ����������� �������� �������� � ������ ������ - ����������, ��� ����, �� ������ ������� � ��������.';

$MESS ['EDOST_ADMIN_NO_ORDER_ACTIVE'] = '�� �������� �� ������ ������!';
$MESS ['EDOST_ADMIN_NO_DOC'] = '�� ������� ���������� ������� ��� ���������� �������!';
$MESS ['EDOST_ADMIN_NO_ORDER'] = '�� ������� �������, ��������������� �������� ��������';

$MESS ['EDOST_ADMIN_FIND_HEAD'] = '����� ������� �� �����: ';
$MESS ['EDOST_ADMIN_FIND_HINT'] = '���� ����� ��������� ����� ������� (1,2,10,20, ...),<br> �������� ����� ����� ���� ("10-20" ��� "10-" ��� "-20")';
$MESS ['EDOST_ADMIN_FIND'] = '�����';


$MESS ['EDOST_ADMIN_SIGN'] = array(
	'msk' => '������', 'spb' => '�����-���������', 'rub' => ' ���.', 'kop' => ' ���.', 'list' => '���� ', 'quantity' => ' ��.', 'order' => '� ',
	'total' => '�����: ', 'total2' => '�����', 'delivery' => '��������', 'loading' => '��������...', 'loading_history' => '���������� �������...',
	'change' => '��������',
);

$MESS ['EDOST_ADMIN_107_INFO'] = array(
	'�������',
	'��������� 1-�� ������',
	'� ����������� ���������',
	'� ���������� ��������',
);

$MESS ['EDOST_ADMIN_ORDER_FLAG'] = array(
	'PAYED' => array('name' => '<span style="color: #F70;"><b>�������</b></span>', 'value' => 'Y'),
	'CANCELED' => array('name' => '<span style="color: #A00;"><b>�������</b></span>', 'value' => 'Y'),
	'ALLOW_DELIVERY' => array('name' => '<span style="color: #B122B5;"><b>�������� ���������</b></span>', 'value' => 'Y'),
	'MARKED' => array('name' => '<span style="color: #F00;"><b>��������</b></span>', 'value' => 'Y'),
	'DEDUCTED' => array('name' => '<span style="color: #088;"><b>��������</b></span>', 'value' => 'Y'),
);

$MESS ['EDOST_ADMIN_RENAME'] = array(
	array('����� ������ (����������� 1-�� ������)', '����� (1-� �����)'),
	array('����� ������ (�������� �������)', '����� (�������)'),
);

$MESS ['EDOST_ADMIN_BUTTON'] = array(
	'print' => array('name' => '������� �������� ������', 'status' => ' � ��������� ������� ������', 'deducted' => ' � ��������� ������', 'status_deducted' => ', ��������� � ��������� ������� ������'),
	'history' => array('name' => '�������� ������', 'print' => '�����������'),
	'show_order' => '�������� ������ ������, ����������� �� ��������� ',
	'update' => '��������',
	'check' =>  array('Y' => '��������', 'N' => '��������'),
);

$MESS ['EDOST_ADMIN_SETTING'] = array(
	'status_no_change' => '�� ��������',
	'status' => '����� ������ �������, ��������� ������� ������:',
	'cod' => '������ ������, �������������� ����������� �������:',

	'insurance_107' => '�������� ����� (�.107) ��� ����������� "�� ����������" <span style="color: #888; font-weight: normal;">(�� ��������� ����� ���������� ������ ��� �������)</span>',
	'duplex' => '��� ������������� ���������� ������ ������� �������� � �������� ������� <span style="color: #888; font-weight: normal;">(��� ������� [1,2,3], � �������� [3,2,1])</span>',

	'show_order_id' => '�������� ����� ������ � ������-������� ���� �������',
	'info_color_head' => '����: ',
	'info_color' => array(array('������', '000'), array('�����', '888'), array('������-�����', 'AAA'), array('����� �����', 'DDD'), array('������', 'FF0'), array('�������', '0F0'), array('�������', '0AF')),

	'browser_head' => '��� �������� �������:',
	'browser' => array('ie' => 'Internet Explorer', 'firefox' => 'Firefox', 'opera' => 'Opera', 'chrome' => 'chrome', 'yandex' => '������.�������'),

	'filter_days' => array('1' => '24 ����', '2' => '2 ���', '5' => '5 ����', '10' => '10 ����', '30' => '30 ����', '60' => '2 ������'),
	'duplex_x' => array('�������� ��� ������������� ����������:', ' ��', '��� �������� ���������� ���������, ����� �� �������� ����� ������� � ������������� ������� (������� � ��������� ������ ������).<br><br>�������� ����� ���� �������������.<br><br>����� ����������, ��� ������ "��������" � ������, ��������� ������������ ������������� ��������, ������� ���������� 100% �������� �������� ����������.'),

	'passport_head' => '��������, ������������� ��� �������� ������ (��� �.116):',
	'passport' => array(
		array('name' => '��������: ', 'width' => '160', 'max' => '10', 'default' => '�������'),
		array('name' => ', ����� ', 'width' => '45', 'max' => '8'),
		array('name' => ' � ', 'width' => '65', 'max' => '8'),
		array('name' => ', ���� ������: ', 'width' => '100', 'max' => '11'),
		array('name' => ' 20', 'width' => '30', 'max' => '2'),
		array('name' => ' �.<div style="height: 3px;"></div>������������ ����������, ��������� ��������: ', 'width' => '400', 'max' => '55'),
	),

	'show_allow_delivery' => '���������� ������ ����������� � �������� ������',
	'hide_deducted' => '�������� ����������� ������',
	'deducted' => '����� ������ �������, ��������� ������',
	'hide_unpaid' => '�������� ������ ��� ����������� �������, ���� ��� �� ��������',
	'hide_without_doc' => array('name' => '�������� ������, ��� ������� �� ������� ���������� �������', 'mark' => '<span style="color: #F00; font-weight: bold;">��� ���������� �������</span>'),
	'show_status' => '���������� ������ ������ ��� ���������� ��������:',
	'docs_disable' => '������������� ������ ���������� ����������:',

	'save' => '��������� ���������',
);


$MESS ['EDOST_ADMIN_SHOP_WARNING'] = '<b style="color: #F00;">��������������!!!</b><br> <b>��� ������ ������� ������ ���� ������� "������ ��������"
� <a href="sale_report_edit.php">���������� �������� ����</a></b><br>
��������� �������� <a href="http://edost.ru/kln/help-bitrix11.html#10" target="_blank">�����</a>
';

$MESS ['EDOST_ADMIN_DOC_WARNING'] = '<b style="color: #F00;">��������������!!!</b><br> <b>�� ������� �� ������ ������ - ������ ���������� ����������!<br>
���������, ����� � ���������� <a href="sale_delivery_handler_edit.php?SID=edost">������ eDost</a> ���� ������ �� � ������, � ����� �������� ��������� �������.</b><br>
';

?>