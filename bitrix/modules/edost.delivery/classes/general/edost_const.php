<?
/*********************************************************************************
��������� ������ eDost
*********************************************************************************/

//define('DELIVERY_EDOST_WEIGHT_DEFAULT', '5000'); // ��� � ������� ������� ������ �� ��������� (����� ��������������, ���� ��� � ������ �� �����)

//define('DELIVERY_EDOST_WEIGHT_PROPERTY_NAME', 'WEIGHT'); // �������� �������� (PROPERTY) ������, � ������� �������� ���
//define('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE', 'G'); // 'KG' ��� 'G' - ������� ��������� �������� (PROPERTY) ������, � ������� �������� ���

//define('DELIVERY_EDOST_VOLUME_PROPERTY_NAME', 'VOLUME'); // �������� �������� (PROPERTY) ������, � ������� �������� ����� 'VOLUME' (������������, ����� �������� � ������� �� ������)
//define('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO', 1000); // ����������� �������� ������� ��������� ������ � ������� �������� ��������� (������: ���������� = 1000, ���� ����� � ������ ����������, � �������� � �����������)

// �������� ������� (PROPERTY) ������, � ������� �������� ��������
define('DELIVERY_EDOST_LENGTH_PROPERTY_NAME', 'LENGTH');
define('DELIVERY_EDOST_WIDTH_PROPERTY_NAME', 'WIDTH');
define('DELIVERY_EDOST_HEIGHT_PROPERTY_NAME', 'HEIGHT');

define('DELIVERY_EDOST_SORT', '31,32,33,34,35,29,36,43,1,2,3,18,5,19,37,38,6,7,8,9,10,17,45,46,47,44,27,28,25,26,23,24,11,20,12,21,14,48,15,50,16,49,22,51,39,40,41,42,52,53,54,55'); // ���������� ������� �� ����� ������� eDost (���� ��������� ����� �������), ���� eDost: http://www.edost.ru/kln/help.html#DeliveryCode
//define('DELIVERY_EDOST_PRICELIST', 'Y'); // 'Y' - ���������� ��������� ��������, ��� ����� ����, ��� ����������� ������
//define('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT', 'Y'); // 'Y' - ������������ ��������, ���� � ������� ���� ����� � ������� �����

//define('DELIVERY_EDOST_ORDER_LINK', '/personal/order/make'); // �������� ���������� ������ (��� ����������� ������� PickPoint)
//define('DELIVERY_EDOST_LOCATION_DISABLE', '������|�����-���������|����� (��������� �������)|12345'); // ��������� ������ eDost ��� ��������� �������������� (����������� �������� �������������� ��� ��� ID � bitrix, �������������� ���� '|')

//define('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - ������������ ��� �������� ������, ���� � ��� ��������� ����������� ��� �� �����
//define('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - ������������ �������� (PROPERTY) �������� ������ (��������, ��� � �����)

define('DELIVERY_EDOST_WRITE_LOG', 0); // 1 - ������ ������ ������� � ��� ���� ����� ������� CDeliveryEDOST::__WriteToLog()
define('DELIVERY_EDOST_CACHE_LIFETIME', 18000); // ��� 5 ����� = 60*60*5, ��� 1 ���� = 60*60*24*1
?>