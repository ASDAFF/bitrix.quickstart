<?php

	if(!defined('IWEB_SMS_SENDING_HTTPS_ADDRESS')) define("IWEB_SMS_SENDING_HTTPS_ADDRESS", "http://lcab.sms-sending.ru/"); //HTTPS-�����, � �������� ����� ���������� �������. �� ������ �� �����.
	if(!defined('IWEB_SMS_SENDING_HTTP_ADDRESS')) define("IWEB_SMS_SENDING_HTTP_ADDRESS", "http://lcab.sms-sending.ru/"); //HTTP-�����, � �������� ����� ���������� �������. �� ������ �� �����.
	if(!defined('IWEB_SMS_SENDING_HTTPS_CHARSET')) define("IWEB_SMS_SENDING_HTTPS_CHARSET", LANG_CHARSET); //��������� ����� ��������. cp1251 - ��� Windows-1251, ���� �� utf-8 ���, ����������� - utf-8 :)
	if(!defined('IWEB_SMS_SENDING_HTTPS_METHOD')) define("IWEB_SMS_SENDING_HTTPS_METHOD", "curl"); //�����, ������� ������������ ������ (curl)
	if(!defined('IWEB_SMS_SENDING_USE_HTTPS')) define("IWEB_SMS_SENDING_USE_HTTPS", 0); //1 - ������������ HTTPS-�����, 0 - HTTP