<?php

// encoding: WINDOWS-1251

$checkStatus = ' <a href="http://triggmine.cloudapp.net/" target="_blank">��������� ������ ����� ����������</a> ���� ���������� � <a href="http://triggmine.cloudapp.net/Support" target="_blank">������������</a>';
$contactSupport = ' ���������� � <a href="http://triggmine.cloudapp.net/Support" target="_blank">������������</a> TriggMine.';
$getApiUrl = ' ���������� API URL �� ������ ������ � <a href="http://triggmine.cloudapp.net/Integration" target="_blank">���������� ���������� TriggMine</a>';
$getApiKey = ' ��� ���� �� ������ ������ � <a href="http://triggmine.cloudapp.net/Integration" target="_blank">���������� ���������� TriggMine</a>';

$MESS['failed_to_activate'] = '�� ������� ������������ ������.' . $checkStatus;
$MESS['failed_to_deactivate'] = '�� ������� �������������� ������.' . $checkStatus;
$MESS['low_version'] = 'TriggMine �� ��������� � Bitrix ������ ���� %1';
$MESS['missing_module'] = '����������� ������ Bitrix %1 �� ����������';
$MESS['missing_function'] = '����������� Bitrix ������� %1 ����������';
$MESS['no_transport'] = '����� \'allow_url_fopen\' ��������� � ����� php.ini � ���������� cURL �� �����������!' . $contactSupport;
$MESS['empty_api_url'] = '�������, ����������, API URL. API URL �� ����� ���� ������.' . $getApiUrl;
$MESS['invalid_api_url'] = '%1 �� �������� ���������� API URL.' . $getApiUrl;
$MESS['empty_api_key'] = 'API ���� �� ����� ���� ������. ������� API ���� ��� ������ ������.' . $getApiKey;
$MESS['no_access_to_api'] = '��� ������� � API URL (%1). ��������� ������������ API URL.' . $getApiUrl;
$MESS['invalid_response_from_api'] = '��� ������� � API URL %1.' . $contactSupport;
$MESS['invalid_token'] = '��������� API ���� �� ���������.' . $getApiKey;
$MESS['api_returns_error'] = 'TriggMine API ���������� ������ %1.' . $contactSupport;
$MESS['plugin_cannot_be_active'] = '������ �� ����� ���� �����������';
$MESS['plugin_can_be_active'] = '������ ��������. �������� ������, ��� ���� ����� �� ����� ������.';
$MESS['wrong_cart_url'] = '���� � ������� �� �������� ���������� URL';