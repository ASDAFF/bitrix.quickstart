<?php

	if(!defined('IWEB_AM4U_HTTPS_ADDRESS')) define("IWEB_AM4U_HTTPS_ADDRESS", "http://sms.am4u.ru/"); //HTTPS-�����, � �������� ����� ���������� �������. �� ������ �� �����.
	if(!defined('IWEB_AM4U_HTTP_ADDRESS')) define("IWEB_AM4U_HTTP_ADDRESS", "http://sms.am4u.ru/"); //HTTP-�����, � �������� ����� ���������� �������. �� ������ �� �����.
	if(!defined('IWEB_AM4U_HTTPS_CHARSET')) define("IWEB_AM4U_HTTPS_CHARSET", LANG_CHARSET); //��������� ����� ��������. cp1251 - ��� Windows-1251, ���� �� utf-8 ���, ����������� - utf-8 :)
	if(!defined('IWEB_AM4U_HTTPS_METHOD')) define("IWEB_AM4U_HTTPS_METHOD", "curl"); //�����, ������� ������������ ������ (curl)
	if(!defined('IWEB_AM4U_USE_HTTPS')) define("IWEB_AM4U_USE_HTTPS", 0); //1 - ������������ HTTPS-�����, 0 - HTTP